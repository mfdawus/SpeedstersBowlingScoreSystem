<?php
require_once __DIR__ . '/../database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $pdo = getDBConnection();
    
    // Get the selected date from POST data
    $selectedDate = $_POST['selected_date'] ?? date('Y-m-d');
    
    // Build date condition - use session_date instead of game_date
    $dateCondition = '';
    if ($selectedDate === 'today') {
        $dateCondition = "DATE(sess.session_date) = CURDATE()";
    } elseif ($selectedDate === 'all') {
        $dateCondition = "1=1"; // All time
    } else {
        $dateCondition = "DATE(sess.session_date) = '" . $selectedDate . "'";
    }
    
    // No need for session participants - all players can join any game
    $activeSession = ['session_id' => 'all', 'session_name' => 'All Games', 'game_mode' => 'Mixed'];
    
    $leaderboard = [];
    $teamStats = [];
    $soloStats = [];
    $recentActivities = [];
    
    // Get all players with scores for the selected date (including admins) - WITH LANE ASSIGNMENTS
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.team_name,
            u.skill_level,
            u.status,
            u.user_role,
            COALESCE(SUM(gs.player_score), 0) as total_score,
            COALESCE(COUNT(gs.score_id), 0) as games_played,
            COALESCE(ROUND(AVG(gs.player_score), 1), 0) as avg_score,
            COALESCE(MAX(gs.player_score), 0) as best_score,
            COALESCE(SUM(gs.strikes), 0) as total_strikes,
            COALESCE(SUM(gs.spares), 0) as total_spares,
            CASE 
                WHEN COUNT(gs.score_id) > 0 THEN ROUND((SUM(gs.strikes) / (COUNT(gs.score_id) * 10)) * 100, 1)
                ELSE 0 
            END as strike_rate,
            sp.lane_number
        FROM users u
        LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
        LEFT JOIN game_sessions sess ON gs.session_id = sess.session_id
        LEFT JOIN session_participants sp ON u.user_id = sp.user_id AND sess.session_id = sp.session_id
        WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') AND u.status = 'Active' AND ($dateCondition)
        GROUP BY u.user_id, sp.lane_number
        ORDER BY total_score DESC, u.first_name, u.last_name
        LIMIT 20
    ");
    $stmt->execute();
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get team stats with date filtering (including admins) - NO SESSION PARTICIPANTS NEEDED
    $stmt = $pdo->prepare("
        SELECT 
            u.team_name,
            COUNT(DISTINCT u.user_id) as player_count,
            COALESCE(SUM(gs.player_score), 0) as total_score,
            COALESCE(COUNT(gs.score_id), 0) as total_games,
            COALESCE(ROUND(AVG(gs.player_score), 1), 0) as avg_score,
            COALESCE(MAX(gs.player_score), 0) as best_score
        FROM users u
        LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
        LEFT JOIN game_sessions sess ON gs.session_id = sess.session_id
        WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') AND u.status = 'Active' AND u.team_name IS NOT NULL AND ($dateCondition)
        GROUP BY u.team_name
        ORDER BY total_score DESC
    ");
    $stmt->execute();
    $teamStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get solo stats with date filtering (including admins) - NO SESSION PARTICIPANTS NEEDED
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.skill_level,
            u.status,
            u.user_role,
            COALESCE(SUM(gs.player_score), 0) as total_score,
            COALESCE(COUNT(gs.score_id), 0) as games_played,
            COALESCE(ROUND(AVG(gs.player_score), 1), 0) as avg_score,
            COALESCE(MAX(gs.player_score), 0) as best_score,
            CASE 
                WHEN COUNT(gs.score_id) > 0 THEN ROUND((SUM(gs.strikes) / (COUNT(gs.score_id) * 10)) * 100, 1)
                ELSE 0 
            END as strike_rate
        FROM users u
        LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
        LEFT JOIN game_sessions sess ON gs.session_id = sess.session_id
        WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') AND u.status = 'Active' AND ($dateCondition)
        GROUP BY u.user_id
        ORDER BY total_score DESC, u.first_name, u.last_name
        LIMIT 10
    ");
    $stmt->execute();
    $soloStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent activities with date filtering (including admins) - NO SESSION PARTICIPANTS NEEDED
    $stmt = $pdo->prepare("
        SELECT 
            u.first_name,
            u.last_name,
            u.user_role,
            gs.player_score,
            gs.game_number,
            gs.created_at,
            gs.strikes,
            gs.spares
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.user_id
        JOIN game_sessions sess ON gs.session_id = sess.session_id
        WHERE gs.status = 'Completed' AND ($dateCondition)
        ORDER BY gs.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate filtered statistics (including admins)
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT u.user_id) as total_players,
            COUNT(gs.score_id) as total_games,
            COUNT(DISTINCT gs.user_id) as players_played_today,
            COALESCE(ROUND(AVG(gs.player_score), 1), 0) as avg_score_today
        FROM users u
        LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
        LEFT JOIN game_sessions sess ON gs.session_id = sess.session_id
        WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') AND u.status = 'Active' AND ($dateCondition)
    ");
    $stmt->execute();
    $filteredStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'data' => [
            'leaderboard' => $leaderboard,
            'team_stats' => $teamStats,
            'solo_stats' => $soloStats,
            'recent_activities' => $recentActivities,
            'filtered_stats' => $filteredStats,
            'selected_date' => $selectedDate,
            'active_session' => $activeSession,
            'has_active_session' => !empty($activeSession)
        ],
        'debug' => [
            'leaderboard_count' => count($leaderboard),
            'team_stats_count' => count($teamStats),
            'solo_stats_count' => count($soloStats),
            'date_condition' => $dateCondition
        ]
    ];
    
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
