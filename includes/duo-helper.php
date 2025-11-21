<?php
/**
 * Duo Match System - Helper Functions
 * Handles duo pairing logic, scoring, and calculations
 */

require_once __DIR__ . '/../database.php';

/**
 * Calculate a player's average score from their last N games
 * Gets the latest games from ALL modes (Solo, Doubles, Team) regardless of game_mode
 * @param int $userId User ID
 * @param int $gameCount Number of recent games to consider (default: 5)
 * @return float Average score
 */
function calculatePlayerAverage($userId, $gameCount = 5) {
    try {
        $pdo = getDBConnection();
        // Get latest N games from ALL modes (Solo, Doubles, Team)
        // Order by game_date DESC (most recent first), then by created_at as fallback
        $stmt = $pdo->prepare("
            SELECT ROUND(AVG(player_score), 2) as avg_score
            FROM (
                SELECT 
                    gs.player_score
                FROM game_scores gs
                WHERE gs.user_id = ? 
                AND gs.status = 'Completed'
                AND gs.player_score IS NOT NULL
                AND gs.player_score > 0
                ORDER BY 
                    COALESCE(gs.game_date, DATE(gs.created_at)) DESC,
                    gs.created_at DESC
                LIMIT ?
            ) as recent_games
        ");
        $stmt->execute([$userId, $gameCount]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If no games found, return 0
        if (!$result || $result['avg_score'] === null) {
            return 0;
        }
        
        return (float)$result['avg_score'];
    } catch (PDOException $e) {
        error_log("Error calculating player average: " . $e->getMessage());
        return 0;
    }
}

/**
 * Auto-pair players based on skill level
 * Pairs highest with next-highest (similar averages together)
 * @param int $sessionId Session ID
 * @return array Array of created duos or error
 */
function autoPairPlayers($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Get all players in lobby who aren't paired yet
        $stmt = $pdo->prepare("
            SELECT 
                djl.user_id,
                djl.avg_score,
                u.first_name,
                u.last_name,
                u.profile_picture
            FROM duo_join_lobby djl
            JOIN users u ON djl.user_id = u.user_id
            WHERE djl.session_id = ? AND djl.is_paired = FALSE
            ORDER BY djl.avg_score DESC
        ");
        $stmt->execute([$sessionId]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $playerCount = count($players);
        
        if ($playerCount < 2) {
            return ['success' => false, 'message' => 'Not enough players to form duos (minimum 2)'];
        }
        
        $duos = [];
        
        // Manual grouping for balanced teams (Group A with Group B)
        // Define player groups based on user_id or first_name
        $groupA = ['Fiq', 'RT', 'Syirah', 'Adam'];
        $groupB = ['SR', 'CT', 'RA', 'Ammar'];
        
        // Separate players into groups
        $playersGroupA = [];
        $playersGroupB = [];
        
        foreach ($players as $player) {
            $firstName = $player['first_name'];
            if (in_array($firstName, $groupA)) {
                $playersGroupA[] = $player;
            } elseif (in_array($firstName, $groupB)) {
                $playersGroupB[] = $player;
            } else {
                // If player not in predefined groups, add to group with fewer members
                if (count($playersGroupA) <= count($playersGroupB)) {
                    $playersGroupA[] = $player;
                } else {
                    $playersGroupB[] = $player;
                }
            }
        }
        
        // Sort each group by average score (highest first)
        usort($playersGroupA, function($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });
        usort($playersGroupB, function($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });
        
        // Pair Group A with Group B (highest A with highest B, etc.)
        $pairsCount = min(count($playersGroupA), count($playersGroupB));
        
        for ($i = 0; $i < $pairsCount; $i++) {
            $player1 = $playersGroupA[$i];
            $player2 = $playersGroupB[$i];
            
            // Create default duo name
            $duoName = "Duo " . ($i + 1);
            
            // Insert duo team
            $insertStmt = $pdo->prepare("
                INSERT INTO duo_teams (
                    session_id, duo_name, 
                    player1_id, player2_id, 
                    player1_avg, player2_avg, 
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, 'Pending')
            ");
            $insertStmt->execute([
                $sessionId,
                $duoName,
                $player1['user_id'],
                $player2['user_id'],
                $player1['avg_score'],
                $player2['avg_score']
            ]);
            
            $duoId = $pdo->lastInsertId();
            
            // Update lobby entries to mark as paired
            $updateStmt = $pdo->prepare("
                UPDATE duo_join_lobby 
                SET is_paired = TRUE, duo_id = ? 
                WHERE session_id = ? AND user_id IN (?, ?)
            ");
            $updateStmt->execute([$duoId, $sessionId, $player1['user_id'], $player2['user_id']]);
            
            $duos[] = [
                'duo_id' => $duoId,
                'duo_name' => $duoName,
                'player1' => $player1,
                'player2' => $player2,
                'combined_avg' => round(($player1['avg_score'] + $player2['avg_score']) / 2, 2)
            ];
            
            // Send notifications to both players
            createDuoNotification($sessionId, $player1['user_id'], $duoId, 'pairing_complete', 
                "You've been paired with {$player2['first_name']} {$player2['last_name']}!");
            createDuoNotification($sessionId, $player2['user_id'], $duoId, 'pairing_complete', 
                "You've been paired with {$player1['first_name']} {$player1['last_name']}!");
        }
        
        // Handle remaining unpaired players from either group
        $unpairedPlayers = [];
        if (count($playersGroupA) > $pairsCount) {
            for ($i = $pairsCount; $i < count($playersGroupA); $i++) {
                $unpairedPlayers[] = $playersGroupA[$i];
            }
        }
        if (count($playersGroupB) > $pairsCount) {
            for ($i = $pairsCount; $i < count($playersGroupB); $i++) {
                $unpairedPlayers[] = $playersGroupB[$i];
            }
        }
        
        // If there are 2+ unpaired players, pair them together
        $unpairedCount = count($unpairedPlayers);
        if ($unpairedCount >= 2) {
            $extraPairs = (int) floor($unpairedCount / 2);
            for ($i = 0; $i < $extraPairs; $i++) {
                $idx1 = $i * 2;
                $idx2 = $idx1 + 1;
                
                $player1 = $unpairedPlayers[$idx1];
                $player2 = $unpairedPlayers[$idx2];
                
                $duoName = "Duo " . (count($duos) + 1);
                
                $insertStmt = $pdo->prepare("
                    INSERT INTO duo_teams (
                        session_id, duo_name, 
                        player1_id, player2_id, 
                        player1_avg, player2_avg, 
                        status
                    ) VALUES (?, ?, ?, ?, ?, ?, 'Pending')
                ");
                $insertStmt->execute([
                    $sessionId,
                    $duoName,
                    $player1['user_id'],
                    $player2['user_id'],
                    $player1['avg_score'],
                    $player2['avg_score']
                ]);
                
                $duoId = $pdo->lastInsertId();
                
                $updateStmt = $pdo->prepare("
                    UPDATE duo_join_lobby 
                    SET is_paired = TRUE, duo_id = ? 
                    WHERE session_id = ? AND user_id IN (?, ?)
                ");
                $updateStmt->execute([$duoId, $sessionId, $player1['user_id'], $player2['user_id']]);
                
                $duos[] = [
                    'duo_id' => $duoId,
                    'duo_name' => $duoName,
                    'player1' => $player1,
                    'player2' => $player2,
                    'combined_avg' => round(($player1['avg_score'] + $player2['avg_score']) / 2, 2)
                ];
                
                createDuoNotification($sessionId, $player1['user_id'], $duoId, 'pairing_complete', 
                    "You've been paired with {$player2['first_name']} {$player2['last_name']}!");
                createDuoNotification($sessionId, $player2['user_id'], $duoId, 'pairing_complete', 
                    "You've been paired with {$player1['first_name']} {$player1['last_name']}!");
            }
        }
        
        // Handle final odd player (if any)
        $oddPlayer = null;
        if ($unpairedCount % 2 != 0) {
            $oddPlayer = $unpairedPlayers[$unpairedCount - 1];
        }
        
        return [
            'success' => true,
            'duos' => $duos,
            'odd_player' => $oddPlayer,
            'message' => count($duos) . ' duo(s) created successfully!'
        ];
        
    } catch (PDOException $e) {
        error_log("Error auto-pairing players: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Get all duos for a specific session
 * @param int $sessionId Session ID
 * @return array Array of duos with player details
 */
function getDuosBySession($sessionId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                dt.duo_id,
                dt.duo_name,
                dt.combined_total_score,
                dt.lane_number,
                dt.lane_vote_player1,
                dt.lane_vote_player2,
                dt.status,
                dt.player1_avg,
                dt.player2_avg,
                u1.user_id as player1_id,
                u1.first_name as player1_first_name,
                u1.last_name as player1_last_name,
                u1.profile_picture as player1_picture,
                u2.user_id as player2_id,
                u2.first_name as player2_first_name,
                u2.last_name as player2_last_name,
                u2.profile_picture as player2_picture
            FROM duo_teams dt
            JOIN users u1 ON dt.player1_id = u1.user_id
            JOIN users u2 ON dt.player2_id = u2.user_id
            WHERE dt.session_id = ?
            ORDER BY dt.combined_total_score DESC, dt.duo_name
        ");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting duos: " . $e->getMessage());
        return [];
    }
}

/**
 * Get individual and combined scores for a duo
 * @param int $duoId Duo ID
 * @param int|null $gameNumber Specific game number (null for all games)
 * @return array Score data
 */
function getDuoScores($duoId, $gameNumber = null) {
    try {
        $pdo = getDBConnection();
        
        $query = "
            SELECT 
                gs.score_id,
                gs.user_id,
                u.first_name,
                u.last_name,
                u.profile_picture,
                gs.game_number,
                gs.player_score,
                gs.strikes,
                gs.spares,
                gs.open_frames,
                gs.lane_number,
                gs.created_at
            FROM game_scores gs
            JOIN users u ON gs.user_id = u.user_id
            WHERE gs.duo_id = ?
        ";
        
        $params = [$duoId];
        
        if ($gameNumber !== null) {
            $query .= " AND gs.game_number = ?";
            $params[] = $gameNumber;
        }
        
        $query .= " ORDER BY gs.game_number, gs.user_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group by game number and calculate combined scores
        $gameScores = [];
        foreach ($scores as $score) {
            $game = $score['game_number'];
            if (!isset($gameScores[$game])) {
                $gameScores[$game] = [
                    'game_number' => $game,
                    'players' => [],
                    'combined_score' => 0,
                    'total_strikes' => 0,
                    'total_spares' => 0
                ];
            }
            
            $gameScores[$game]['players'][] = $score;
            $gameScores[$game]['combined_score'] += $score['player_score'];
            $gameScores[$game]['total_strikes'] += $score['strikes'];
            $gameScores[$game]['total_spares'] += $score['spares'];
        }
        
        return array_values($gameScores);
        
    } catch (PDOException $e) {
        error_log("Error getting duo scores: " . $e->getMessage());
        return [];
    }
}

/**
 * Update duo's combined total score
 * @param int $duoId Duo ID
 * @return bool Success status
 */
function updateDuoCombinedScore($duoId) {
    try {
        $pdo = getDBConnection();
        
        // Calculate total combined score from all games
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(player_score), 0) as total
            FROM game_scores
            WHERE duo_id = ? AND status = 'Completed'
        ");
        $stmt->execute([$duoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalScore = $result['total'];
        
        // Update duo team
        $updateStmt = $pdo->prepare("
            UPDATE duo_teams 
            SET combined_total_score = ? 
            WHERE duo_id = ?
        ");
        return $updateStmt->execute([$totalScore, $duoId]);
        
    } catch (PDOException $e) {
        error_log("Error updating duo combined score: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a player is already in a duo for a session
 * @param int $userId User ID
 * @param int $sessionId Session ID
 * @return array|false Duo info if found, false otherwise
 */
function isPlayerInDuo($userId, $sessionId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT duo_id, duo_name, status
            FROM duo_teams
            WHERE session_id = ? 
            AND (player1_id = ? OR player2_id = ?)
        ");
        $stmt->execute([$sessionId, $userId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error checking player duo: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a player has joined the lobby
 * @param int $userId User ID
 * @param int $sessionId Session ID
 * @return bool
 */
function hasPlayerJoinedLobby($userId, $sessionId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM duo_join_lobby
            WHERE session_id = ? AND user_id = ?
        ");
        $stmt->execute([$sessionId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    } catch (PDOException $e) {
        error_log("Error checking lobby status: " . $e->getMessage());
        return false;
    }
}

/**
 * Get lobby status (who joined, how many, etc.)
 * @param int $sessionId Session ID
 * @return array Lobby information
 */
function getLobbyStatus($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Get all players in lobby
        $stmt = $pdo->prepare("
            SELECT 
                djl.user_id,
                djl.avg_score,
                djl.joined_at,
                djl.is_paired,
                u.first_name,
                u.last_name,
                u.profile_picture,
                u.team_name
            FROM duo_join_lobby djl
            JOIN users u ON djl.user_id = u.user_id
            WHERE djl.session_id = ?
            ORDER BY djl.joined_at
        ");
        $stmt->execute([$sessionId]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get session details
        $sessionStmt = $pdo->prepare("
            SELECT session_name, session_date, status
            FROM game_sessions
            WHERE session_id = ?
        ");
        $sessionStmt->execute([$sessionId]);
        $session = $sessionStmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'session' => $session,
            'players' => $players,
            'total_joined' => count($players),
            'all_paired' => count(array_filter($players, fn($p) => $p['is_paired'])) === count($players)
        ];
        
    } catch (PDOException $e) {
        error_log("Error getting lobby status: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error'];
    }
}

/**
 * Create a notification for duo events
 * @param int $sessionId Session ID
 * @param int $userId User ID
 * @param int|null $duoId Duo ID
 * @param string $type Notification type
 * @param string $message Message text
 * @return bool Success status
 */
function createDuoNotification($sessionId, $userId, $duoId, $type, $message) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO duo_notifications (session_id, user_id, duo_id, notification_type, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$sessionId, $userId, $duoId, $type, $message]);
    } catch (PDOException $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Get unread notifications for a user
 * @param int $userId User ID
 * @return array Array of notifications
 */
function getUnreadNotifications($userId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                notification_id,
                session_id,
                duo_id,
                notification_type,
                message,
                created_at
            FROM duo_notifications
            WHERE user_id = ? AND is_read = FALSE
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Mark notification as read
 * @param int $notificationId Notification ID
 * @return bool Success status
 */
function markNotificationRead($notificationId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE duo_notifications SET is_read = TRUE WHERE notification_id = ?");
        return $stmt->execute([$notificationId]);
    } catch (PDOException $e) {
        error_log("Error marking notification read: " . $e->getMessage());
        return false;
    }
}

/**
 * Assign a lane to a duo (either by consensus vote or admin override)
 * @param int $duoId Duo ID
 * @param int $laneNumber Lane number
 * @return bool Success status
 */
function assignLaneToDuo($duoId, $laneNumber) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE duo_teams 
            SET lane_number = ? 
            WHERE duo_id = ?
        ");
        return $stmt->execute([$laneNumber, $duoId]);
    } catch (PDOException $e) {
        error_log("Error assigning lane: " . $e->getMessage());
        return false;
    }
}

/**
 * Update duo name
 * @param int $duoId Duo ID
 * @param string $duoName New duo name
 * @return bool Success status
 */
function updateDuoName($duoId, $duoName) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE duo_teams 
            SET duo_name = ? 
            WHERE duo_id = ?
        ");
        return $stmt->execute([trim($duoName), $duoId]);
    } catch (PDOException $e) {
        error_log("Error updating duo name: " . $e->getMessage());
        return false;
    }
}

/**
 * Record a lane choice for a duo.
 * Only ONE player per duo may successfully vote.
 * The first valid vote immediately assigns lane_number.
 *
 * @param int $duoId Duo ID
 * @param int $userId User ID (to determine player1 or player2)
 * @param int $laneNumber Lane number preference
 * @return array
 */
function recordLaneVote($duoId, $userId, $laneNumber) {
    try {
        $pdo = getDBConnection();
        
        // Get duo info
        $stmt = $pdo->prepare("
            SELECT player1_id, player2_id, lane_number, lane_vote_player1, lane_vote_player2
            FROM duo_teams
            WHERE duo_id = ?
        ");
        $stmt->execute([$duoId]);
        $duo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$duo) {
            return ['success' => false, 'message' => 'Duo not found'];
        }
        
        // Lane already assigned?
        if (!empty($duo['lane_number'])) {
            return [
                'success' => false,
                'message' => 'Lane already assigned for this duo.',
                'consensus' => true,
                'assigned_lane' => (int)$duo['lane_number']
            ];
        }
        
        // Has someone already voted?
        if (!empty($duo['lane_vote_player1']) || !empty($duo['lane_vote_player2'])) {
            return [
                'success' => false,
                'message' => 'Lane vote has already been cast for this duo.',
                'consensus' => false,
                'assigned_lane' => null
            ];
        }
        
        // Determine which player is voting (only used for audit columns)
        $isPlayer1 = ($userId == $duo['player1_id']);
        $voteColumn = $isPlayer1 ? 'lane_vote_player1' : 'lane_vote_player2';
        
        // Update vote
        $updateStmt = $pdo->prepare("
            UPDATE duo_teams 
            SET {$voteColumn} = ?, lane_number = ? 
            WHERE duo_id = ?
        ");
        $updateStmt->execute([$laneNumber, $laneNumber, $duoId]);
        
        return [
            'success' => true,
            'consensus' => true,
            'assigned_lane' => (int)$laneNumber,
            'player1_vote' => $isPlayer1 ? $laneNumber : null,
            'player2_vote' => $isPlayer1 ? null : $laneNumber
        ];
        
    } catch (PDOException $e) {
        error_log("Error recording lane vote: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error'];
    }
}
?>

