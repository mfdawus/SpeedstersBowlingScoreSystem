<?php
require_once __DIR__ . '/../database.php';

// Get user by ID
function getUserById($userId) {
    try {
        $pdo = getDBConnection();
        
        // First get user basic info
        $stmt = $pdo->prepare("
            SELECT 
                user_id,
                username,
                first_name,
                last_name,
                email,
                phone,
                skill_level,
                user_role,
                status,
                team_name,
                created_at
            FROM users 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['error' => 'User not found'];
        }
        
        // Then get game statistics separately
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_games,
                    AVG(player_score) as avg_score,
                    MAX(player_score) as best_score
                FROM game_scores 
                WHERE user_id = ? AND status = 'Completed'
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Merge the stats with user data
            $user['total_games'] = $stats['total_games'] ?: 0;
            $user['avg_score'] = $stats['avg_score'] ?: 0;
            $user['best_score'] = $stats['best_score'] ?: 0;
            
        } catch(PDOException $e) {
            // If game_scores table doesn't exist or has issues, set defaults
            $user['total_games'] = 0;
            $user['avg_score'] = 0;
            $user['best_score'] = 0;
        }
        
        return $user;
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Update user
function updateUser($userId, $data) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            UPDATE users 
            SET 
                username = ?,
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                skill_level = ?,
                status = ?,
                team_name = ?
            WHERE user_id = ?
        ");
        
        $result = $stmt->execute([
            $data['username'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'],
            $data['skill_level'],
            $data['status'],
            $data['team_name'],
            $userId
        ]);
        
        return $result;
        
    } catch(PDOException $e) {
        return false;
    }
}

// Delete user
function deleteUser($userId) {
    try {
        $pdo = getDBConnection();
        
        // First delete related game scores
        $stmt1 = $pdo->prepare("DELETE FROM game_scores WHERE user_id = ?");
        $stmt1->execute([$userId]);
        
        // Then delete the user
        $stmt2 = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $result = $stmt2->execute([$userId]);
        
        return $result;
        
    } catch(PDOException $e) {
        return false;
    }
}

// Create new user
function createUser($data) {
    try {
        $pdo = getDBConnection();
        
        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (
                username, first_name, last_name, email, phone, 
                skill_level, user_role, status, team_name, password, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $data['username'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'],
            $data['skill_level'],
            $data['user_role'],
            $data['status'],
            $data['team_name'],
            $hashedPassword
        ]);
        
        return $result ? $pdo->lastInsertId() : false;
        
    } catch(PDOException $e) {
        return false;
    }
}

// Get user's recent games
function getUserRecentGames($userId, $limit = 5) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                game_date,
                game_mode,
                player_score,
                team_name,
                lane_number,
                status
            FROM game_scores 
            WHERE user_id = ? AND status = 'Completed'
            ORDER BY game_date DESC, score_id DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return [];
    }
}
?>
