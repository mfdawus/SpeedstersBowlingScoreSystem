<?php
require_once 'database.php';

echo "<h2>Database Debug Information</h2>";

try {
    $pdo = getDBConnection();
    
    // Check users table
    echo "<h3>Users Table:</h3>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total users: " . $result['count'] . "<br>";
    
    // Check game_scores table
    echo "<h3>Game Scores Table:</h3>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM game_scores");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total game scores: " . $result['count'] . "<br>";
    
    // Check sample data
    echo "<h3>Sample Game Scores:</h3>";
    $stmt = $pdo->prepare("SELECT gs.player_score, gs.game_date, gs.game_mode, u.first_name, u.last_name FROM game_scores gs JOIN users u ON gs.user_id = u.user_id ORDER BY gs.player_score DESC LIMIT 5");
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($games)) {
        echo "No game scores found!<br>";
    } else {
        foreach ($games as $game) {
            echo $game['first_name'] . " " . $game['last_name'] . " - Score: " . $game['player_score'] . " - Date: " . $game['game_date'] . " - Mode: " . $game['game_mode'] . "<br>";
        }
    }
    
    // Test the weekly function
    echo "<h3>Weekly Top Players Test:</h3>";
    require_once 'includes/dashboard.php';
    $weeklyTop = getWeeklyTopPlayers(3);
    
    if (empty($weeklyTop)) {
        echo "No weekly top players found!<br>";
    } else {
        foreach ($weeklyTop as $player) {
            echo $player['first_name'] . " " . $player['last_name'] . " - Score: " . $player['player_score'] . "<br>";
        }
    }
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
