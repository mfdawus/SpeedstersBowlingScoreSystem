<?php
require_once '../includes/auth.php';
require_once '../database.php';

// Require admin access
requireAdmin();

// Get selected date from POST or use today
$selectedDate = $_POST['selected_date'] ?? date('Y-m-d');

// Set headers for Excel file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="bowling_scores_' . $selectedDate . '_' . date('H-i-s') . '.xlsx"');
header('Cache-Control: max-age=0');

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
    
    // Create Excel content
    $excelContent = "<?xml version=\"1.0\"?>\n";
    $excelContent .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
    $excelContent .= " xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n";
    $excelContent .= " xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
    $excelContent .= " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
    $excelContent .= " xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
    
    // Define styles
    $excelContent .= "<Styles>\n";
    $excelContent .= "<Style ss:ID=\"Header\">\n";
    $excelContent .= "<Font ss:Bold=\"1\" ss:Color=\"#FFFFFF\"/>\n";
    $excelContent .= "<Interior ss:Color=\"#4CAF50\" ss:Pattern=\"Solid\"/>\n";
    $excelContent .= "<Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>\n";
    $excelContent .= "</Style>\n";
    $excelContent .= "<Style ss:ID=\"Center\">\n";
    $excelContent .= "<Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>\n";
    $excelContent .= "</Style>\n";
    $excelContent .= "</Styles>\n";
    
    // Create worksheet
    $excelContent .= "<Worksheet ss:Name=\"Bowling Scores\">\n";
    $excelContent .= "<Table>\n";
    
    // Header row
    $excelContent .= "<Row>\n";
    $headers = ['No', 'Name', 'Team', 'Game1', 'Game2', 'Game3', 'Game4', 'Game5', 'Average', 'Clean Game', 'Penalty Per Game', 'Total', 'Rank'];
    foreach ($headers as $header) {
        $excelContent .= "<Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">$header</Data></Cell>\n";
    }
    $excelContent .= "</Row>\n";
    
    // Data rows
    $rank = 1;
    $playerStats = [];
    
    // Calculate stats for each player
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
        
        $excelContent .= "<Row>\n";
        
        // No
        $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"Number\">$rank</Data></Cell>\n";
        
        // Name
        $fullName = $player['first_name'] . ' ' . $player['last_name'];
        $excelContent .= "<Cell><Data ss:Type=\"String\">$fullName</Data></Cell>\n";
        
        // Team
        $teamName = $player['team_name'] ?: 'No Team';
        $excelContent .= "<Cell><Data ss:Type=\"String\">$teamName</Data></Cell>\n";
        
        // Game1-5
        for ($gameNum = 1; $gameNum <= 5; $gameNum++) {
            $score = $gameScores[$gameNum];
            if ($score !== '') {
                $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"Number\">$score</Data></Cell>\n";
            } else {
                $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"String\"></Data></Cell>\n";
            }
        }
        
        // Average
        if ($average > 0) {
            $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"Number\">$average</Data></Cell>\n";
        } else {
            $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"String\"></Data></Cell>\n";
        }
        
        // Clean Game (blank - we don't have this data)
        $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"String\"></Data></Cell>\n";
        
        // Penalty Per Game (blank - we don't have this data)
        $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"String\"></Data></Cell>\n";
        
        // Total
        if ($totalScore > 0) {
            $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"Number\">$totalScore</Data></Cell>\n";
        } else {
            $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"String\"></Data></Cell>\n";
        }
        
        // Rank
        $excelContent .= "<Cell ss:StyleID=\"Center\"><Data ss:Type=\"Number\">$rank</Data></Cell>\n";
        
        $excelContent .= "</Row>\n";
        $rank++;
    }
    
    $excelContent .= "</Table>\n";
    $excelContent .= "</Worksheet>\n";
    $excelContent .= "</Workbook>\n";
    
    // Output the Excel file
    echo $excelContent;
    
} catch (Exception $e) {
    // If there's an error, output a simple CSV instead
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="bowling_scores_' . date('Y-m-d_H-i-s') . '.csv"');
    
    echo "No,Name,Team,Game1,Game2,Game3,Game4,Game5,Average,Clean Game,Penalty Per Game,Total,Rank\n";
    echo "Error: " . $e->getMessage() . "\n";
}
?>
