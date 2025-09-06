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
    $headers = ['No', 'Name', 'Team', 'Game1', 'Game2', 'Game3', 'Game4', 'Game5', 'Average', 'Clean Game', 'Penalty Per Game', 'Total', 'Rank'];
    $csvContent .= implode(',', array_map('escapeCSV', $headers)) . "\n";
    
    // Calculate stats for each player
    $playerStats = [];
    foreach ($allPlayers as $player) {
        $playerGames = $scoresByUser[$player['user_id']] ?? [];
        
        $gameScores = [];
        $totalScore = 0;
        $gamesPlayed = 0;
        
        // Get scores for each game (1-5)
        for ($gameNum = 1; $gameNum <= 5; $gameNum++) {
            if (isset($playerGames[$gameNum])) {
                $score = $playerGames[$gameNum]['player_score'];
                $gameScores[$gameNum] = $score;
                $totalScore += $score;
                $gamesPlayed++;
            } else {
                $gameScores[$gameNum] = '';
            }
        }
        
        $average = $gamesPlayed > 0 ? round($totalScore / $gamesPlayed, 1) : 0;
        
        $playerStats[] = [
            'player' => $player,
            'game_scores' => $gameScores,
            'total_score' => $totalScore,
            'average' => $average,
            'games_played' => $gamesPlayed
        ];
    }
    
    // Sort by total score descending
    usort($playerStats, function($a, $b) {
        return $b['total_score'] <=> $a['total_score'];
    });
    
    // Add data rows
    $rank = 1;
    foreach ($playerStats as $stats) {
        $player = $stats['player'];
        $gameScores = $stats['game_scores'];
        $totalScore = $stats['total_score'];
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
        
        // Game1-5
        for ($gameNum = 1; $gameNum <= 5; $gameNum++) {
            $score = $gameScores[$gameNum];
            $row[] = $score !== '' ? $score : '';
        }
        
        // Average
        $row[] = $average > 0 ? $average : '';
        
        // Clean Game (blank - we don't have this data)
        $row[] = '';
        
        // Penalty Per Game (blank - we don't have this data)
        $row[] = '';
        
        // Total
        $row[] = $totalScore > 0 ? $totalScore : '';
        
        // Rank
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
    
    echo "No,Name,Team,Game1,Game2,Game3,Game4,Game5,Average,Clean Game,Penalty Per Game,Total,Rank\n";
    echo "1,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error,Error\n";
    echo "Error Details: " . $e->getMessage() . "\n";
}
?>
