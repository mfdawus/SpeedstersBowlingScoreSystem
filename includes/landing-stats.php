<?php
require_once 'database.php';

// Get landing page statistics
function getLandingPageStats() {
    try {
        $pdo = getDBConnection();
        
        // Total active players
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE status = 'Active'");
        $stmt->execute();
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
        
        // Total games played
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_games FROM game_scores WHERE status = 'Completed'");
        $stmt->execute();
        $totalGames = $stmt->fetch(PDO::FETCH_ASSOC)['total_games'];
        
        // Average score across all games
        $stmt = $pdo->prepare("SELECT COALESCE(ROUND(AVG(player_score), 0), 0) as avg_score FROM game_scores WHERE status = 'Completed'");
        $stmt->execute();
        $avgScore = $stmt->fetch(PDO::FETCH_ASSOC)['avg_score'];
        
        // Highest score ever
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(player_score), 0) as high_score FROM game_scores WHERE status = 'Completed'");
        $stmt->execute();
        $highScore = $stmt->fetch(PDO::FETCH_ASSOC)['high_score'];
        
        return [
            'total_users' => $totalUsers ?: 0,
            'total_games' => $totalGames ?: 0,
            'avg_score' => $avgScore ?: 0,
            'high_score' => $highScore ?: 0
        ];
        
    } catch(PDOException $e) {
        // Return default values if database fails
        return [
            'total_users' => 0,
            'total_games' => 0,
            'avg_score' => 0,
            'high_score' => 0
        ];
    }
}
?>

