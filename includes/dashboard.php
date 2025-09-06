<?php
require_once 'database.php';

// Get player statistics
function getPlayerStats($userId) {
    try {
        $pdo = getDBConnection();
        
        // Get basic player info
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$player) {
            return null;
        }
        
        // Get game statistics
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_games,
                AVG(player_score) as average_score,
                MAX(player_score) as best_score,
                SUM(strikes) as total_strikes,
                SUM(spares) as total_spares,
                COUNT(CASE WHEN game_mode = 'Solo' THEN 1 END) as solo_games,
                COUNT(CASE WHEN game_mode = 'Doubles' THEN 1 END) as doubles_games,
                COUNT(CASE WHEN game_mode = 'Team' THEN 1 END) as team_games
            FROM game_scores 
            WHERE user_id = ? AND status = 'Completed'
        ");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get recent matches (grouped by match)
        $stmt = $pdo->prepare("
            SELECT 
                game_date, 
                game_mode, 
                team_name, 
                lane_number,
                SUM(player_score) as total_score,
                COUNT(*) as games_played
            FROM game_scores 
            WHERE user_id = ? AND status = 'Completed'
            GROUP BY game_date, game_mode, team_name, lane_number
            ORDER BY game_date DESC, MAX(game_time) DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        $recentMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'player' => $player,
            'stats' => $stats,
            'recent_matches' => $recentMatches
        ];
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Get leaderboard data
function getLeaderboard($limit = 10) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.team_name,
                u.skill_level,
                AVG(gs.player_score) as average_score,
                MAX(gs.player_score) as best_score,
                COUNT(gs.score_id) as total_games
            FROM users u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
            WHERE (u.user_role = 'Player' OR u.user_role = 'Admin')
            GROUP BY u.user_id
            HAVING total_games > 0
            ORDER BY best_score DESC, average_score DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return [];
    }
}

// Get weekly top players (highest scores this week)
function getWeeklyTopPlayers($limit = 3) {
    try {
        $pdo = getDBConnection();
        
        // Simple approach: get top players from all games (since we have test data)
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.team_name,
                u.skill_level,
                gs.player_score,
                gs.game_date,
                gs.game_mode,
                gs.lane_number
            FROM game_scores gs
            JOIN users u ON gs.user_id = u.user_id
            WHERE gs.status = 'Completed'
            ORDER BY gs.player_score DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return [];
    }
}

// Get admin dashboard statistics
function getAdminStats() {
    try {
        $pdo = getDBConnection();
        
        // Total users
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE status = 'Active'");
        $stmt->execute();
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
        
        // Total games played
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_games FROM game_scores WHERE status = 'Completed'");
        $stmt->execute();
        $totalGames = $stmt->fetch(PDO::FETCH_ASSOC)['total_games'];
        
        // Games today
        $stmt = $pdo->prepare("SELECT COUNT(*) as games_today FROM game_scores WHERE DATE(game_date) = CURDATE() AND status = 'Completed'");
        $stmt->execute();
        $gamesToday = $stmt->fetch(PDO::FETCH_ASSOC)['games_today'];
        
        // Players played today (distinct players with scores today)
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as players_played_today FROM game_scores WHERE DATE(game_date) = CURDATE() AND status = 'Completed'");
        $stmt->execute();
        $playersPlayedToday = $stmt->fetch(PDO::FETCH_ASSOC)['players_played_today'];
        
        // Total matches (grouped by date, game_mode, team_name, lane_number)
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT CONCAT(game_date, '-', game_mode, '-', COALESCE(team_name, 'Solo'), '-', lane_number)) as total_matches 
            FROM game_scores 
            WHERE status = 'Completed'
        ");
        $stmt->execute();
        $totalMatches = $stmt->fetch(PDO::FETCH_ASSOC)['total_matches'];
        
        // Top player
        $stmt = $pdo->prepare("
            SELECT u.first_name, u.last_name, MAX(gs.player_score) as best_score
            FROM users u
            JOIN game_scores gs ON u.user_id = gs.user_id
            WHERE gs.status = 'Completed'
            GROUP BY u.user_id
            ORDER BY best_score DESC
            LIMIT 1
        ");
        $stmt->execute();
        $topPlayer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_users' => $totalUsers,
            'total_games' => $totalGames,
            'games_today' => $gamesToday,
            'players_played_today' => $playersPlayedToday,
            'total_matches' => $totalMatches,
            'top_player' => $topPlayer
        ];
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Get team statistics
function getTeamStats() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                team_name,
                game_mode,
                COUNT(DISTINCT user_id) as players_count,
                AVG(player_score) as team_average,
                MAX(player_score) as team_best,
                COUNT(*) as total_games
            FROM game_scores 
            WHERE team_name IS NOT NULL AND team_name != 'Solo' AND status = 'Completed'
            GROUP BY team_name, game_mode
            ORDER BY team_average DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Get all players statistics (regardless of game mode)
function getAllPlayersStats() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.skill_level,
                u.team_name,
                AVG(gs.player_score) as avg_score,
                MAX(gs.player_score) as best_score,
                COUNT(gs.score_id) as total_games,
                SUM(CASE WHEN gs.player_score >= 200 THEN 1 ELSE 0 END) as strikes_count,
                GROUP_CONCAT(DISTINCT gs.game_mode ORDER BY gs.game_mode SEPARATOR ', ') as game_modes_played
            FROM users u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
            WHERE (u.user_role = 'Player' OR u.user_role = 'Admin')
            GROUP BY u.user_id, u.username, u.first_name, u.last_name, u.skill_level, u.team_name
            HAVING total_games > 0
            ORDER BY avg_score DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Get solo players statistics (for Solo Players tab)
function getSoloPlayersStats() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.skill_level,
                AVG(gs.player_score) as avg_score,
                MAX(gs.player_score) as best_score,
                COUNT(gs.score_id) as total_games,
                SUM(CASE WHEN gs.player_score >= 200 THEN 1 ELSE 0 END) as strikes_count
            FROM users u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed' AND gs.game_mode = 'Solo'
            WHERE (u.user_role = 'Player' OR u.user_role = 'Admin')
            GROUP BY u.user_id, u.username, u.first_name, u.last_name, u.skill_level
            HAVING total_games > 0
            ORDER BY avg_score DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Get recent activities for admin dashboard
function getRecentActivities() {
    try {
        $pdo = getDBConnection();
        
        // Get recent games with high scores
        $stmt = $pdo->prepare("
            SELECT 
                'high_score' as activity_type,
                u.first_name,
                u.last_name,
                gs.player_score,
                gs.game_mode,
                gs.game_date,
                gs.game_time,
                gs.team_name
            FROM game_scores gs
            JOIN users u ON gs.user_id = u.user_id
            WHERE gs.status = 'Completed' AND gs.player_score >= 200
            ORDER BY gs.game_date DESC, gs.game_time DESC
            LIMIT 5
        ");
        $stmt->execute();
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format activities
        $formattedActivities = [];
        foreach ($activities as $activity) {
            $formattedActivities[] = [
                'type' => 'high_score',
                'icon' => 'ti-trophy',
                'icon_color' => 'bg-primary',
                'title' => 'High Score Achieved',
                'description' => $activity['first_name'] . ' ' . $activity['last_name'] . ' scored ' . $activity['player_score'] . ' in ' . $activity['game_mode'],
                'time' => getTimeAgo($activity['game_date'] . ' ' . $activity['game_time'])
            ];
        }
        
        return $formattedActivities;
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Helper function to get time ago
function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    return floor($time/2592000) . ' months ago';
}
?>
