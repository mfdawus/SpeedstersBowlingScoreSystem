<?php
require_once __DIR__ . '/../database.php';

// Create a new game session
function createGameSession($data) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO game_sessions (
                session_name, session_date, session_time, game_mode, 
                max_players, created_by, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['session_name'],
            $data['session_date'],
            $data['session_time'],
            $data['game_mode'],
            $data['max_players'],
            $data['created_by'],
            $data['notes'] ?? ''
        ]);
        
        return $result ? $pdo->lastInsertId() : false;
        
    } catch(PDOException $e) {
        return false;
    }
}

// Get all game sessions
function getAllGameSessions() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                gs.*,
                u.username as created_by_name,
                COUNT(DISTINCT gsc.user_id) as players_played,
                gs.max_players as participant_count
            FROM game_sessions gs
            LEFT JOIN users u ON gs.created_by = u.user_id
            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND gsc.status = 'Completed'
            GROUP BY gs.session_id
            ORDER BY gs.session_date DESC, gs.session_time DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return [];
    }
}

// Get active session
function getActiveSession() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                gs.*,
                u.username as created_by_name,
                COUNT(DISTINCT gsc.user_id) as players_played,
                gs.max_players as participant_count
            FROM game_sessions gs
            LEFT JOIN users u ON gs.created_by = u.user_id
            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND DATE(gsc.created_at) = CURDATE() AND gsc.status = 'Completed'
            WHERE gs.status = 'Active'
            GROUP BY gs.session_id
            ORDER BY gs.created_at DESC
            LIMIT 1
        ");
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return null;
    }
}

// Start a session
function startSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            UPDATE game_sessions 
            SET status = 'Active', started_at = NOW() 
            WHERE session_id = ?
        ");
        
        return $stmt->execute([$sessionId]);
        
    } catch(PDOException $e) {
        return false;
    }
}

// End a session
function endSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            UPDATE game_sessions 
            SET status = 'Completed', ended_at = NOW() 
            WHERE session_id = ?
        ");
        
        return $stmt->execute([$sessionId]);
        
    } catch(PDOException $e) {
        return false;
    }
}

// Note: Session participants functionality removed - all players can join any session automatically

// Add score to session
function addScoreToSession($sessionId, $userId, $gameNumber, $playerScore, $strikes = 0, $spares = 0, $openFrames = 0, $laneNumber = null) {
    try {
        $pdo = getDBConnection();
        
        // Get player's team name and determine game mode
        $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $teamName = $user['team_name'] ?? null;
        
        // Set game mode to 'Team' for today's matches
        $gameMode = 'Team';
        
        $stmt = $pdo->prepare("
            INSERT INTO game_scores (
                user_id, session_id, game_mode, game_date, game_time, 
                lane_number, game_number, player_score, strikes, spares, 
                open_frames, status, team_name
            ) VALUES (?, ?, ?, CURDATE(), CURTIME(), ?, ?, ?, ?, ?, ?, 'Completed', ?)
        ");
        
        return $stmt->execute([
            $userId, $sessionId, $gameMode, $laneNumber, $gameNumber, 
            $playerScore, $strikes, $spares, $openFrames, $teamName
        ]);
        
    } catch(PDOException $e) {
        return false;
    }
}

// Get session scores
function getSessionScores($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                gs.*,
                u.username,
                u.first_name,
                u.last_name,
                u.team_name
            FROM game_scores gs
            JOIN users u ON gs.user_id = u.user_id
            WHERE gs.session_id = ?
            ORDER BY gs.game_number, gs.player_score DESC
        ");
        $stmt->execute([$sessionId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return [];
    }
}

// Get session by ID
function getSessionById($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                gs.*,
                u.username as created_by_name
            FROM game_sessions gs
            LEFT JOIN users u ON gs.created_by = u.user_id
            WHERE gs.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return null;
    }
}
?>