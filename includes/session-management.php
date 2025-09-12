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
                CASE 
                    WHEN gs.game_mode = 'Solo' THEN (
                        SELECT COUNT(*) FROM users 
                        WHERE team_name = 'Speedsters' 
                        AND (user_role = 'Player' OR user_role = 'Admin') 
                        AND status = 'Active'
                    )
                    ELSE gs.max_players
                END as participant_count
            FROM game_sessions gs
            LEFT JOIN users u ON gs.created_by = u.user_id
            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND gsc.status = 'Completed'
            WHERE gs.status != 'Deleted'
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
                CASE 
                    WHEN gs.game_mode = 'Solo' THEN (
                        SELECT COUNT(*) FROM users 
                        WHERE team_name = 'Speedsters' 
                        AND (user_role = 'Player' OR user_role = 'Admin') 
                        AND status = 'Active'
                    )
                    ELSE gs.max_players
                END as participant_count
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
        
        // Check if session exists and is in Scheduled status
        $checkStmt = $pdo->prepare("SELECT session_id, status FROM game_sessions WHERE session_id = ?");
        $checkStmt->execute([$sessionId]);
        $session = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            error_log("Session not found: " . $sessionId);
            return false;
        }
        
        if ($session['status'] !== 'Scheduled') {
            error_log("Session not in Scheduled status: " . $session['status']);
            return false;
        }
        
        $stmt = $pdo->prepare("
            UPDATE game_sessions 
            SET status = 'Active', started_at = NOW() 
            WHERE session_id = ?
        ");
        
        $result = $stmt->execute([$sessionId]);
        error_log("Update query result: " . ($result ? 'true' : 'false'));
        
        return $result;
        
    } catch(PDOException $e) {
        error_log("Start session error: " . $e->getMessage());
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

function cancelSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Check if session exists and is not already cancelled
        $stmt = $pdo->prepare("SELECT status FROM game_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            return false;
        }
        
        if ($session['status'] === 'Cancelled') {
            return false; // Already cancelled
        }
        
        // Update session status to Cancelled
        $updateStmt = $pdo->prepare("UPDATE game_sessions SET status = 'Cancelled', ended_at = NOW() WHERE session_id = ?");
        return $updateStmt->execute([$sessionId]);
        
    } catch (Exception $e) {
        error_log("Error cancelling session: " . $e->getMessage());
        return false;
    }
}

function pauseSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Check if session exists and is active
        $stmt = $pdo->prepare("SELECT status FROM game_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session || $session['status'] !== 'Active') {
            return false;
        }
        
        // Update session status to Paused
        $updateStmt = $pdo->prepare("UPDATE game_sessions SET status = 'Paused' WHERE session_id = ?");
        return $updateStmt->execute([$sessionId]);
        
    } catch (Exception $e) {
        error_log("Error pausing session: " . $e->getMessage());
        return false;
    }
}

function restartSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Check if session exists
        $stmt = $pdo->prepare("SELECT status FROM game_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            return false;
        }
        
        // Reset session to Scheduled status and clear start/end times
        $updateStmt = $pdo->prepare("
            UPDATE game_sessions 
            SET status = 'Scheduled', started_at = NULL, ended_at = NULL 
            WHERE session_id = ?
        ");
        return $updateStmt->execute([$sessionId]);
        
    } catch (Exception $e) {
        error_log("Error restarting session: " . $e->getMessage());
        return false;
    }
}

// Note: Session participants functionality removed - all players can join any session automatically

// Add score to session
function addScoreToSession($sessionId, $userId, $gameNumber, $playerScore, $strikes = 0, $spares = 0, $openFrames = 0, $laneNumber = null, $gameMode = 'Team') {
    try {
        $pdo = getDBConnection();
        
        // Check if score already exists for this user, session, and game number
        $checkStmt = $pdo->prepare("
            SELECT score_id FROM game_scores 
            WHERE user_id = ? AND session_id = ? AND game_number = ? AND status = 'Completed'
        ");
        $checkStmt->execute([$userId, $sessionId, $gameNumber]);
        $existingScore = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingScore) {
            // Update existing score instead of creating duplicate
            $updateStmt = $pdo->prepare("
                UPDATE game_scores 
                SET player_score = ?, strikes = ?, spares = ?, open_frames = ?, 
                    lane_number = ?, updated_at = NOW()
                WHERE score_id = ?
            ");
            return $updateStmt->execute([
                $playerScore, $strikes, $spares, $openFrames, $laneNumber, $existingScore['score_id']
            ]);
        }
        
        // Get player's team name and determine game mode
        $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $teamName = $user['team_name'] ?? null;
        
        // Use the game_mode parameter passed to the function
        
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
        error_log("addScoreToSession error: " . $e->getMessage());
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

// Delete a game session and all related data
function deleteGameSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Delete all scores for this session
        $stmt = $pdo->prepare("DELETE FROM game_scores WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        // Delete the session itself
        $stmt = $pdo->prepare("DELETE FROM game_sessions WHERE session_id = ?");
        $result = $stmt->execute([$sessionId]);
        
        if ($result) {
            $pdo->commit();
            return true;
        } else {
            $pdo->rollback();
            return false;
        }
        
    } catch(PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("Error deleting session: " . $e->getMessage());
        return false;
    }
}

// Soft delete a game session (mark as deleted but keep data)
function softDeleteGameSession($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("UPDATE game_sessions SET status = 'Deleted' WHERE session_id = ?");
        return $stmt->execute([$sessionId]);
        
    } catch(PDOException $e) {
        error_log("Error soft deleting session: " . $e->getMessage());
        return false;
    }
}

// Get all available teams
function getAllTeams() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT DISTINCT team_name 
            FROM users 
            WHERE team_name IS NOT NULL AND team_name != '' AND status = 'Active'
            ORDER BY team_name
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
        
    } catch(PDOException $e) {
        error_log("Error getting teams: " . $e->getMessage());
        return [];
    }
}

// Get team data for a specific date
function getTeamDataForDate($selectedDate) {
    try {
        $pdo = getDBConnection();
        
        // Get all teams
        $teams = getAllTeams();
        $teamData = [];
        
        foreach ($teams as $teamName) {
            // Get players in this team
            $stmt = $pdo->prepare("
                SELECT user_id, first_name, last_name 
                FROM users 
                WHERE team_name = ? AND status = 'Active'
                ORDER BY first_name, last_name
            ");
            $stmt->execute([$teamName]);
            $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($players)) continue;
            
            $playerIds = array_column($players, 'user_id');
            $playerNames = array_map(function($p) { return $p['first_name'] . ' ' . $p['last_name']; }, $players);
            
            // Get team scores for the selected date
            $dateCondition = '';
            $params = [];
            
            if ($selectedDate === 'today') {
                $dateCondition = "AND DATE(gs.session_date) = CURDATE()";
            } elseif ($selectedDate === 'all') {
                $dateCondition = "";
            } else {
                $dateCondition = "AND DATE(gs.session_date) = ?";
                $params[] = $selectedDate;
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    gs.session_id,
                    gs.session_name,
                    gs.session_date,
                    gc.game_number,
                    gc.player_score,
                    gc.strikes,
                    gc.spares,
                    gc.open_frames,
                    gc.created_at
                FROM game_scores gc
                INNER JOIN game_sessions gs ON gc.session_id = gs.session_id
                WHERE gc.user_id IN (" . implode(',', array_fill(0, count($playerIds), '?')) . ")
                AND gc.status = 'Completed'
                $dateCondition
                ORDER BY gc.created_at DESC
            ");
            
            $params = array_merge($playerIds, $params);
            $stmt->execute($params);
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate team statistics
            $totalScore = 0;
            $gamesPlayed = 0;
            $bestScore = 0;
            $totalStrikes = 0;
            $totalSpares = 0;
            $gameScores = [];
            
            foreach ($scores as $score) {
                $totalScore += $score['player_score'];
                $totalStrikes += $score['strikes'];
                $totalSpares += $score['spares'];
                $gamesPlayed++;
                
                if ($score['player_score'] > $bestScore) {
                    $bestScore = $score['player_score'];
                }
                
                // Group by game number
                $gameNum = $score['game_number'];
                if (!isset($gameScores[$gameNum])) {
                    $gameScores[$gameNum] = [
                        'team_score' => 0,
                        'strikes' => 0,
                        'spares' => 0,
                        'open_frames' => 0,
                        'created_at' => $score['created_at']
                    ];
                }
                
                $gameScores[$gameNum]['team_score'] += $score['player_score'];
                $gameScores[$gameNum]['strikes'] += $score['strikes'];
                $gameScores[$gameNum]['spares'] += $score['spares'];
                $gameScores[$gameNum]['open_frames'] += $score['open_frames'];
            }
            
            $avgScore = $gamesPlayed > 0 ? round($totalScore / $gamesPlayed, 1) : 0;
            $lastUpdated = !empty($scores) ? $scores[0]['created_at'] : null;
            
            $teamData[] = [
                'team_name' => $teamName,
                'team_type' => 'Team',
                'player_names' => implode(', ', $playerNames),
                'player_avatars' => generatePlayerAvatars($players),
                'total_score' => $totalScore,
                'avg_score' => $avgScore,
                'games_played' => $gamesPlayed,
                'best_score' => $bestScore,
                'total_strikes' => $totalStrikes,
                'total_spares' => $totalSpares,
                'last_updated' => $lastUpdated ? date('M j, g:i A', strtotime($lastUpdated)) : 'Never',
                'game_1_score' => $gameScores[1] ?? null,
                'game_2_score' => $gameScores[2] ?? null,
                'game_3_score' => $gameScores[3] ?? null,
                'game_4_score' => $gameScores[4] ?? null,
                'game_5_score' => $gameScores[5] ?? null
            ];
        }
        
        // Sort by total score descending
        usort($teamData, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });
        
        return $teamData;
        
    } catch(PDOException $e) {
        error_log("Error getting team data: " . $e->getMessage());
        return [];
    }
}

// Generate player avatars HTML
function generatePlayerAvatars($players) {
    $html = '';
    foreach ($players as $index => $player) {
        $avatarNum = ($player['user_id'] % 8) + 1;
        $html .= '<img src="assets/images/profile/user-' . $avatarNum . '.jpg" alt="Player" class="player-avatar">';
    }
    return $html;
}

// Get players by team
function getPlayersByTeam($teamName) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT user_id, username, first_name, last_name, user_role, team_name
            FROM users 
            WHERE team_name = ? AND status = 'Active'
            ORDER BY first_name, last_name
        ");
        $stmt->execute([$teamName]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting players by team: " . $e->getMessage());
        return [];
    }
}

// Create team session with selected teams
function createTeamSession($data) {
    try {
        $pdo = getDBConnection();
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Check if selected_teams column exists
        $stmt = $pdo->prepare("SHOW COLUMNS FROM game_sessions LIKE 'selected_teams'");
        $stmt->execute();
        $columnExists = $stmt->fetch();
        
        $selectedTeamsJson = json_encode($data['selected_teams']);
        
        if ($columnExists) {
            // Create the session with selected_teams column
            $stmt = $pdo->prepare("
                INSERT INTO game_sessions (
                    session_name, session_date, session_time, game_mode, 
                    max_players, created_by, notes, selected_teams
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['session_name'],
                $data['session_date'],
                $data['session_time'],
                'Team',
                $data['max_players'],
                $data['created_by'],
                $data['notes'] ?? '',
                $selectedTeamsJson
            ]);
        } else {
            // Create the session without selected_teams column (fallback)
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
                'Team',
                $data['max_players'],
                $data['created_by'],
                $data['notes'] ?? ''
            ]);
        }
        
        if ($result) {
            $sessionId = $pdo->lastInsertId();
            $pdo->commit();
            return $sessionId;
        } else {
            $pdo->rollback();
            return false;
        }
        
    } catch(PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("Error creating team session: " . $e->getMessage());
        return false;
    }
}

// Get team session participants
function getTeamSessionParticipants($sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Get session with selected teams
        $stmt = $pdo->prepare("
            SELECT selected_teams FROM game_sessions WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session || !$session['selected_teams']) {
            return [];
        }
        
        $selectedTeams = json_decode($session['selected_teams'], true);
        
        if (empty($selectedTeams)) {
            return [];
        }
        
        // Get players from selected teams
        $placeholders = str_repeat('?,', count($selectedTeams) - 1) . '?';
        $stmt = $pdo->prepare("
            SELECT user_id, username, first_name, last_name, user_role, team_name
            FROM users 
            WHERE team_name IN ($placeholders) AND status = 'Active'
            ORDER BY team_name, first_name, last_name
        ");
        $stmt->execute($selectedTeams);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting team session participants: " . $e->getMessage());
        return [];
    }
}

// Check if user can participate in team session
function canUserParticipateInTeamSession($userId, $sessionId) {
    try {
        $pdo = getDBConnection();
        
        // Get user's team
        $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !$user['team_name']) {
            return false;
        }
        
        // Get session selected teams
        $stmt = $pdo->prepare("SELECT selected_teams FROM game_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session || !$session['selected_teams']) {
            return false;
        }
        
        $selectedTeams = json_decode($session['selected_teams'], true);
        
        return in_array($user['team_name'], $selectedTeams);
        
    } catch(PDOException $e) {
        error_log("Error checking team session participation: " . $e->getMessage());
        return false;
    }
}
?>