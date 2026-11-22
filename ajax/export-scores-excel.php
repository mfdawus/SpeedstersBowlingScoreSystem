<?php
require_once '../includes/auth.php';
require_once '../database.php';

// Require admin access
requireAdmin();

// Get selected date from POST or use today
$selectedDate = $_POST['selected_date'] ?? date('Y-m-d');

// Set headers for CSV file download (Excel compatible)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="bowling_scores_' . $selectedDate . '_' . date('H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

// Add BOM for UTF-8 to ensure proper Excel compatibility
echo "\xEF\xBB\xBF";

try {
    $pdo = getDBConnection();
    
    // Get all players (including admins)
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.team_name,
            u.user_role
        FROM users u 
        WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') AND u.status = 'Active'
        ORDER BY u.first_name, u.last_name
    ");
    $stmt->execute();
    $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all scores for selected date
    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            game_number,
            player_score,
            COALESCE(scratch_score, player_score) AS scratch_score,
            COALESCE(hdcp_applied, 0) AS hdcp_applied,
            strikes,
            spares,
            created_at
        FROM game_scores 
        WHERE status = 'Completed' AND DATE(created_at) = ?
        ORDER BY user_id, game_number
    ");
    $stmt->execute([$selectedDate]);
    $allTodayScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group scores by user_id and game_number
    $scoresByUser = [];
    foreach ($allTodayScores as $score) {
        $userId = $score['user_id'];
        $gameNumber = $score['game_number'];
        
        if (!isset($scoresByUser[$userId])) {
            $scoresByUser[$userId] = [];
        }
        
        $scoresByUser[$userId][$gameNumber] = $score;
    }
    
    // Function to escape CSV values
    function escapeCSV($value) {
        if (is_numeric($value)) {
            return $value;
        }
        // Escape quotes and wrap in quotes if contains comma, quote, or newline
        if (strpos($value, '"') !== false || strpos($value, ',') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }
    
    // Create CSV content
    $csvContent = '';
    
    // Header row
    $headers = ['No', 'Name', 'Team'];
    for ($gameNum = 1; $gameNum <= 5; $gameNum++) {
        $headers[] = 'Game' . $gameNum . ' Scratch';
        $headers[] = 'Game' . $gameNum . ' HDCP';
    }
    $headers = array_merge($headers, ['Scratch Total', 'HDCP Applied', 'Adjusted Record Total', 'Average Scratch', 'Clean Game', 'Penalty Per Game', 'Scratch Rank']);
    $csvContent .= implode(',', array_map('escapeCSV', $headers)) . "\n";
    
    // Calculate stats for each player
    $playerStats = [];
    foreach ($allPlayers as $player) {
        $playerGames = $scoresByUser[$player['user_id']] ?? [];
        
        $gameScores = [];
        $gameHdcp = [];
        $scratchTotal = 0;
        $hdcpTotal = 0;
        $gamesPlayed = 0;
        
        // Get scores for each game (1-5)
        for ($gameNum = 1; $gameNum <= 5; $gameNum++) {
            if (isset($playerGames[$gameNum])) {
                $score = (int)$playerGames[$gameNum]['scratch_score'];
                $hdcp = (int)$playerGames[$gameNum]['hdcp_applied'];
                $gameScores[$gameNum] = $score;
                $gameHdcp[$gameNum] = $hdcp;
                $scratchTotal += $score;
                $hdcpTotal += $hdcp;
                $gamesPlayed++;
            } else {
                $gameScores[$gameNum] = '';
                $gameHdcp[$gameNum] = '';
            }
        }
        
        $average = $gamesPlayed > 0 ? round($scratchTotal / $gamesPlayed, 1) : 0;
        
        $playerStats[] = [
            'player' => $player,
            'game_scores' => $gameScores,
            'game_hdcp' => $gameHdcp,
            'scratch_total' => $scratchTotal,
            'hdcp_total' => $hdcpTotal,
            'adjusted_record_total' => $scratchTotal + $hdcpTotal,
            'average' => $average,
            'games_played' => $gamesPlayed
        ];
    }
    
    // Scratch winner: scratch total first, then highest score from Game 5 down to Game 1.
    usort($playerStats, function($a, $b) {
        $totalComparison = $b['scratch_total'] <=> $a['scratch_total'];
        if ($totalComparison !== 0) {
            return $totalComparison;
        }
        for ($gameNumber = 5; $gameNumber >= 1; $gameNumber--) {
            $aScore = isset($a['game_scores'][$gameNumber])
                ? (int)$a['game_scores'][$gameNumber] : 0;
            $bScore = isset($b['game_scores'][$gameNumber])
                ? (int)$b['game_scores'][$gameNumber] : 0;
            $gameComparison = $bScore <=> $aScore;
            if ($gameComparison !== 0) {
                return $gameComparison;
            }
        }
        return (int)$a['player']['user_id'] <=> (int)$b['player']['user_id'];
    });
    
    // Add data rows
    $rank = 1;
    foreach ($playerStats as $stats) {
        $player = $stats['player'];
        $gameScores = $stats['game_scores'];
        $scratchTotal = $stats['scratch_total'];
        $hdcpTotal = $stats['hdcp_total'];
        $average = $stats['average'];
        
        $row = [];
        
        // No
        $row[] = $rank;
        
        // Name
        $fullName = $player['first_name'] . ' ' . $player['last_name'];
        $row[] = $fullName;
        
        // Team
        $teamName = $player['team_name'] ?: 'No Team';
        $row[] = $teamName;
        
        // Game1-5 scratch scores and separately recorded HDCP
        for ($gameNum = 1; $gameNum <= 5; $gameNum++) {
            $score = $gameScores[$gameNum];
            $row[] = $score !== '' ? $score : '';
            $row[] = $stats['game_hdcp'][$gameNum] !== '' ? $stats['game_hdcp'][$gameNum] : '';
        }
        
        // Scratch, applied HDCP, adjusted record total, and scratch average
        $row[] = $scratchTotal > 0 ? $scratchTotal : '';
        $row[] = $hdcpTotal > 0 ? $hdcpTotal : '';
        $row[] = $stats['adjusted_record_total'] > 0 ? $stats['adjusted_record_total'] : '';
        $row[] = $average > 0 ? $average : '';
        
        // Clean Game (blank - we don't have this data)
        $row[] = '';
        
        // Penalty Per Game (blank - we don't have this data)
        $row[] = '';
        
        // Scratch rank
        $row[] = $rank;
        
        $csvContent .= implode(',', array_map('escapeCSV', $row)) . "\n";
        $rank++;
    }
    
    // Output the CSV file
    echo $csvContent;
    
} catch (Exception $e) {
    // If there's an error, output a simple CSV with error message
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="bowling_scores_error_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    echo "No,Name,Team,Game1 Scratch,Game1 HDCP,Game2 Scratch,Game2 HDCP,Game3 Scratch,Game3 HDCP,Game4 Scratch,Game4 HDCP,Game5 Scratch,Game5 HDCP,Scratch Total,HDCP Applied,Adjusted Record Total,Average Scratch,Clean Game,Penalty Per Game,Scratch Rank\n";
    echo "1,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error\n";
    echo "Error Details: " . $e->getMessage() . "\n";
}
?>
