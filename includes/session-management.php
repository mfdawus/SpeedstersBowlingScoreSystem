<?php

require_once __DIR__ . '/../database.php';

// Create a new game session
function createGameSession($data) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO game_sessions (
                session_name, session_date, session_time, game_mode, 
                max_players, created_by, notes, lanes_count, players_per_lane, 
                lane_selection_open, assignment_locked, available_lanes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['session_name'],
            $data['session_date'],
            $data['session_time'],
            $data['game_mode'],
            $data['max_players'],
            $data['created_by'],
            $data['notes'] ?? '',
            $data['lanes_count'] ?? 8,
            $data['players_per_lane'] ?? 4,
            $data['lane_selection_open'] ?? 1,
            $data['assignment_locked'] ?? 0,
            $data['available_lanes'] ?? null
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
                COALESCE(sp_count.participant_count, 0) as participant_count
            FROM game_sessions gs
            LEFT JOIN users u ON gs.created_by = u.user_id
            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND gsc.status = 'Completed'
            LEFT JOIN (
                SELECT session_id, COUNT(*) as participant_count 
                FROM session_participants 
                GROUP BY session_id
            ) sp_count ON gs.session_id = sp_count.session_id
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
                COALESCE(sp_count.participant_count, 0) as participant_count
            FROM game_sessions gs
            LEFT JOIN users u ON gs.created_by = u.user_id
            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND DATE(gsc.created_at) = CURDATE() AND gsc.status = 'Completed'
            LEFT JOIN (
                SELECT session_id, COUNT(*) as participant_count 
                FROM session_participants 
                GROUP BY session_id
            ) sp_count ON gs.session_id = sp_count.session_id
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
function resetSessionStartTime($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            UPDATE game_sessions 
            SET started_at = NOW() 
            WHERE session_id = ? AND status = 'Active'
        ");
        
        $result = $stmt->execute([$sessionId]);
        return $result;
        
    } catch(PDOException $e) {
        error_log("Error resetting session start time: " . $e->getMessage());
        return false;
    }
}

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
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Update session status to Active
        $stmt = $pdo->prepare("
            UPDATE game_sessions 
            SET status = 'Active', started_at = NOW() 
            WHERE session_id = ?
        ");
        
        $result = $stmt->execute([$sessionId]);
        if (!$result) {
            $pdo->rollBack();
            return false;
        }
        
        $pdo->commit();
        error_log("Session started: " . $sessionId);
        return true;
        
    } catch(PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
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
        
        // Single query to check participation, existing score, and get team name
        $checkStmt = $pdo->prepare("
            SELECT 
                u.team_name,
                sp.participant_id,
                gs.score_id
            FROM users u
            LEFT JOIN session_participants sp ON u.user_id = sp.user_id AND sp.session_id = ?
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.session_id = ? AND gs.game_number = ? AND gs.status = 'Completed'
            WHERE u.user_id = ?
        ");
        $checkStmt->execute([$sessionId, $sessionId, $gameNumber, $userId]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if user is selected for this session
        if (!$result || !$result['participant_id']) {
            error_log("User $userId not selected for session $sessionId");
            return false;
        }
        
        $teamName = $result['team_name'];
        
        if ($result['score_id']) {
            // Update existing score instead of creating duplicate
            $updateStmt = $pdo->prepare("
                UPDATE game_scores 
                SET player_score = ?, strikes = ?, spares = ?, open_frames = ?, 
                    lane_number = ?, updated_at = NOW()
                WHERE score_id = ?
            ");
            return $updateStmt->execute([
                $playerScore, $strikes, $spares, $openFrames, $laneNumber, $result['score_id']
            ]);
        }
        
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

// Get session scores - only for selected participants
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
            JOIN session_participants sp ON gs.user_id = sp.user_id AND gs.session_id = sp.session_id
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

// =====================================================
// NEW PARTICIPANT MANAGEMENT FUNCTIONS
// =====================================================

/**
 * Add selected participants to a session
 * Uses existing session_participants table structure
 */
function addParticipantsToSession($sessionId, $userIds) {
    try {
        $pdo = getDBConnection();
        
        // Clear existing participants first (in case of re-selection)
        $clearStmt = $pdo->prepare("DELETE FROM session_participants WHERE session_id = ?");
        $clearStmt->execute([$sessionId]);
        
        // Add new participants
        $stmt = $pdo->prepare("
            INSERT INTO session_participants (session_id, user_id, joined_at) 
            VALUES (?, ?, NOW())
        ");
        
        foreach ($userIds as $userId) {
            $stmt->execute([$sessionId, $userId]);
        }
        
        return true;
        
    } catch(PDOException $e) {
        error_log("Error adding participants: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all participants for a session with their details
 */
function getSessionParticipants($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                sp.*,
                u.username,
                u.first_name,
                u.last_name,
                u.email,
                u.skill_level,
                u.team_name
            FROM session_participants sp
            JOIN users u ON sp.user_id = u.user_id
            WHERE sp.session_id = ?
            ORDER BY sp.joined_at ASC
        ");
        $stmt->execute([$sessionId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting session participants: " . $e->getMessage());
        return [];
    }
}

/**
 * Replace a participant (simple - just update the record)
 */
function replaceParticipant($sessionId, $oldUserId, $newUserId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            UPDATE session_participants 
            SET user_id = ?, joined_at = NOW()
            WHERE session_id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$newUserId, $sessionId, $oldUserId]);
        
    } catch(PDOException $e) {
        error_log("Error replacing participant: " . $e->getMessage());
        return false;
    }
}

/**
 * Get available players with their average scores for selection
 * Prioritizes Speedsters team for solo matches
 */
function getAvailablePlayersWithAverages($excludeUserIds = [], $prioritizeSpeedsters = true) {
    try {
        $pdo = getDBConnection();
        
        // Build exclusion clause
        $exclusionClause = '';
        $params = [];
        if (!empty($excludeUserIds)) {
            $placeholders = str_repeat('?,', count($excludeUserIds) - 1) . '?';
            $exclusionClause = "AND u.user_id NOT IN ($placeholders)";
            $params = $excludeUserIds;
        }
        
        // Order by Speedsters first if prioritizing
        $orderClause = $prioritizeSpeedsters 
            ? "ORDER BY (u.team_name = 'Speedsters') DESC, u.first_name, u.last_name"
            : "ORDER BY u.first_name, u.last_name";
        
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.email,
                u.skill_level,
                u.team_name,
                COALESCE(ROUND(AVG(gs.player_score), 1), 0) as average_score,
                COUNT(gs.score_id) as games_played,
                MAX(gs.game_date) as last_played,
                CASE WHEN u.team_name = 'Speedsters' THEN 1 ELSE 0 END as is_speedsters
            FROM users u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
            WHERE u.status = 'Active' 
            AND (u.user_role = 'Player' OR u.user_role = 'Admin')
            $exclusionClause
            GROUP BY u.user_id
            $orderClause
        ");
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting available players: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if user can participate in session (is selected)
 */
function canUserParticipateInSession($userId, $sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM session_participants 
            WHERE session_id = ? AND user_id = ?
        ");
        $stmt->execute([$sessionId, $userId]);
        
        return $stmt->fetchColumn() > 0;
        
    } catch(PDOException $e) {
        error_log("Error checking participation: " . $e->getMessage());
        return false;
    }
}

/**
 * Get player's average score
 */
function getPlayerAverageScore($userId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(ROUND(AVG(player_score), 1), 0) as average_score,
                COUNT(score_id) as games_played,
                MAX(game_date) as last_played
            FROM game_scores 
            WHERE user_id = ? AND status = 'Completed'
        ");
        $stmt->execute([$userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting player average: " . $e->getMessage());
        return ['average_score' => 0, 'games_played' => 0, 'last_played' => null];
    }
}

/**
 * Create session with participants in one transaction
 */
function createSessionWithParticipants($sessionData, $participantIds) {
    try {
        $pdo = getDBConnection();
        $pdo->beginTransaction();
        
        // Create session
        $sessionId = createGameSession($sessionData);
        if (!$sessionId) {
            throw new Exception("Failed to create session");
        }
        
        // Add participants
        if (!empty($participantIds)) {
            $success = addParticipantsToSession($sessionId, $participantIds);
            if (!$success) {
                throw new Exception("Failed to add participants");
            }
        }
        
        $pdo->commit();
        return $sessionId;
        
    } catch(Exception $e) {
        $pdo->rollback();
        error_log("Error creating session with participants: " . $e->getMessage());
        return false;
    }
}

/**
 * Get session participant count
 */
function getSessionParticipantCount($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM session_participants 
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        
        return (int)$stmt->fetchColumn();
        
    } catch(PDOException $e) {
        error_log("Error getting participant count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Remove participant from session
 */
function removeParticipantFromSession($sessionId, $userId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            DELETE FROM session_participants 
            WHERE session_id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$sessionId, $userId]);
        
    } catch(PDOException $e) {
        error_log("Error removing participant: " . $e->getMessage());
        return false;
    }
}

/**
 * Get players filtered by team
 */
function getPlayersByTeamWithAverages($teamName = null, $excludeUserIds = []) {
    try {
        $pdo = getDBConnection();
        
        $teamFilter = '';
        $params = [];
        
        if ($teamName && $teamName !== 'all') {
            $teamFilter = "AND u.team_name = ?";
            $params[] = $teamName;
        }
        
        // Build exclusion clause
        if (!empty($excludeUserIds)) {
            $placeholders = str_repeat('?,', count($excludeUserIds) - 1) . '?';
            $teamFilter .= " AND u.user_id NOT IN ($placeholders)";
            $params = array_merge($params, $excludeUserIds);
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.email,
                u.skill_level,
                u.team_name,
                COALESCE(ROUND(AVG(gs.player_score), 1), 0) as average_score,
                COUNT(gs.score_id) as games_played,
                MAX(gs.game_date) as last_played
            FROM users u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
            WHERE u.status = 'Active' 
            AND (u.user_role = 'Player' OR u.user_role = 'Admin')
            $teamFilter
            GROUP BY u.user_id
            ORDER BY u.first_name, u.last_name
        ");
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting players by team: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all team names for filter dropdown
 */
function getAllTeamNames() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT DISTINCT team_name, COUNT(*) as player_count
            FROM users 
            WHERE team_name IS NOT NULL 
            AND team_name != '' 
            AND status = 'Active'
            AND (user_role = 'Player' OR user_role = 'Admin')
            GROUP BY team_name
            ORDER BY (team_name = 'Speedsters') DESC, team_name ASC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting team names: " . $e->getMessage());
        return [];
    }
}

/**
 * Get only selected participants for score entry
 */
function getSessionParticipantsForScoring($sessionId) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                sp.participant_id,
                sp.user_id,
                sp.lane_number,
                u.username,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.user_role,
                u.skill_level,
                u.status,
                u.team_name,
                u.created_at,
                0 as average_score
            FROM session_participants sp
            JOIN users u ON sp.user_id = u.user_id
            WHERE sp.session_id = ?
            ORDER BY u.first_name, u.last_name
        ");
        $stmt->execute([$sessionId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Error getting participants for scoring: " . $e->getMessage());
        return [];
    }
}

/**
 * Update game session
 */
function updateGameSession($sessionId, $data) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            UPDATE game_sessions 
            SET session_name = ?, session_date = ?, session_time = ?, 
                game_mode = ?, max_players = ?, status = ?, notes = ?,
                lanes_count = ?, players_per_lane = ?, lane_selection_open = ?, assignment_locked = ?, available_lanes = ?
            WHERE session_id = ?
        ");
        
        return $stmt->execute([
            $data['session_name'],
            $data['session_date'],
            $data['session_time'],
            $data['game_mode'],
            $data['max_players'],
            $data['status'],
            $data['notes'],
            $data['lanes_count'] ?? 8,
            $data['players_per_lane'] ?? 4,
            $data['lane_selection_open'] ?? 1,
            $data['assignment_locked'] ?? 0,
            $data['available_lanes'] ?? null,
            $sessionId
        ]);
        
    } catch(PDOException $e) {
        error_log("Error updating session: " . $e->getMessage());
        return false;
    }
}

// =====================================================
// LANE CONFIG & ASSIGNMENT HELPERS
// =====================================================

/**
 * Get lane configuration for a session with sensible defaults
 */
function getLaneConfig($sessionId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT lanes_count, players_per_lane, assignment_locked, lane_selection_open, available_lanes FROM game_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return [
                'lanes_count' => 8,
                'players_per_lane' => 4,
                'assignment_locked' => 0,
                'lane_selection_open' => 1,
                'available_lanes' => null,
            ];
        }
        return [
            'lanes_count' => (int)($row['lanes_count'] ?? 8),
            'players_per_lane' => (int)($row['players_per_lane'] ?? 4),
            'assignment_locked' => (int)($row['assignment_locked'] ?? 0),
            'lane_selection_open' => (int)($row['lane_selection_open'] ?? 1),
            'available_lanes' => $row['available_lanes'],
        ];
    } catch (PDOException $e) {
        return ['lanes_count' => 8, 'players_per_lane' => 4, 'assignment_locked' => 0, 'lane_selection_open' => 1];
    }
}

/**
 * Get available lane numbers for a session
 * Returns array of lane numbers (e.g., [7, 8, 9])
 */
function getAvailableLanes($sessionId) {
    $config = getLaneConfig($sessionId);
    
    // If specific lanes are set, use those
    if (!empty($config['available_lanes'])) {
        $lanes = json_decode($config['available_lanes'], true);
        if (is_array($lanes)) {
            return array_filter($lanes, 'is_numeric'); // Filter out any non-numeric values
        }
    }
    
    // Fallback to sequential lanes (1 to lanes_count)
    $lanes = [];
    for ($i = 1; $i <= $config['lanes_count']; $i++) {
        $lanes[] = $i;
    }
    
    return $lanes;
}

/**
 * Update lane configuration for a session
 */
function updateLaneConfig($sessionId, $lanesCount, $playersPerLane, $laneSelectionOpen = null, $assignmentLocked = null) {
    try {
        $pdo = getDBConnection();
        $fields = [
            'lanes_count' => max(1, (int)$lanesCount),
            'players_per_lane' => max(1, (int)$playersPerLane),
        ];
        if ($laneSelectionOpen !== null) $fields['lane_selection_open'] = (int)!!$laneSelectionOpen;
        if ($assignmentLocked !== null) $fields['assignment_locked'] = (int)!!$assignmentLocked;
        $sets = [];
        $params = [];
        foreach ($fields as $col => $val) { $sets[] = "$col = ?"; $params[] = $val; }
        $params[] = $sessionId;
        $sql = "UPDATE game_sessions SET " . implode(', ', $sets) . " WHERE session_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Get per-lane occupancy and remaining capacity
 */
function getLaneStatus($sessionId) {
    try {
        $config = getLaneConfig($sessionId);
        $availableLanes = getAvailableLanes($sessionId);
        $pdo = getDBConnection();

        // Get lane assignments with player information
        $stmt = $pdo->prepare("
            SELECT 
                sp.lane_number, 
                COUNT(*) as cnt,
                GROUP_CONCAT(
                    CONCAT(u.first_name, ' ', u.last_name) 
                    ORDER BY u.first_name, u.last_name
                    SEPARATOR '|'
                ) as player_names,
                GROUP_CONCAT(
                    u.user_id 
                    ORDER BY u.first_name, u.last_name
                    SEPARATOR '|'
                ) as player_ids
            FROM session_participants sp
            JOIN users u ON sp.user_id = u.user_id
            WHERE sp.session_id = ? AND sp.lane_number IS NOT NULL 
            GROUP BY sp.lane_number
        ");
        $stmt->execute([$sessionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create lane data structure
        $laneData = [];
        foreach ($rows as $row) {
            $laneData[$row['lane_number']] = [
                'assigned' => (int)$row['cnt'],
                'players' => array_map(function($name, $id) {
                    return [
                        'user_id' => $id,
                        'first_name' => explode(' ', $name)[0],
                        'last_name' => explode(' ', $name)[1] ?? ''
                    ];
                }, explode('|', $row['player_names']), explode('|', $row['player_ids']))
            ];
        }

        $lanes = [];
        foreach ($availableLanes as $laneNumber) {
            $assigned = isset($laneData[$laneNumber]) ? $laneData[$laneNumber]['assigned'] : 0;
            $players = isset($laneData[$laneNumber]) ? $laneData[$laneNumber]['players'] : [];
            
            $lanes[] = [
                'lane_number' => $laneNumber,
                'assigned' => $assigned,
                'capacity' => $config['players_per_lane'],
                'remaining' => max(0, $config['players_per_lane'] - $assigned),
                'players' => $players,
            ];
        }

        // Count unassigned
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM session_participants WHERE session_id = ? AND lane_number IS NULL");
        $stmt->execute([$sessionId]);
        $unassigned = (int)$stmt->fetchColumn();

        return [
            'lanes' => $lanes,
            'unassigned' => $unassigned,
            'lanes_count' => count($availableLanes),
            'players_per_lane' => $config['players_per_lane'],
            'assignment_locked' => $config['assignment_locked'],
            'lane_selection_open' => $config['lane_selection_open'],
            'available_lanes' => $availableLanes,
        ];
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Player chooses a lane preference (before session starts)
 */
function submitLanePreference($sessionId, $userId, $laneNumber) {
    try {
        $config = getLaneConfig($sessionId);
        
        // Only allow preferences if session is not started yet
        $pdo = getDBConnection();
        $sessionStmt = $pdo->prepare("SELECT status FROM game_sessions WHERE session_id = ?");
        $sessionStmt->execute([$sessionId]);
        $session = $sessionStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session || $session['status'] !== 'Scheduled') {
            return ['success' => false, 'message' => 'Lane preferences can only be set before session starts'];
        }
        
        $availableLanes = getAvailableLanes($sessionId);
        if (!in_array($laneNumber, $availableLanes)) {
            return ['success' => false, 'message' => 'Invalid lane'];
        }

        $pdo = getDBConnection();
        $pdo->beginTransaction();

        // Ensure the user is a participant in this session
        $stmt = $pdo->prepare("SELECT participant_id FROM session_participants WHERE session_id = ? AND user_id = ? FOR UPDATE");
        $stmt->execute([$sessionId, $userId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$participant) { 
            $pdo->rollBack(); 
            return ['success' => false, 'message' => 'Not a participant']; 
        }

        // Store lane preference (not final assignment)
        $stmt = $pdo->prepare("UPDATE session_participants SET lane_number = ? WHERE session_id = ? AND user_id = ?");
        $ok = $stmt->execute([$laneNumber, $sessionId, $userId]);
        
        if (!$ok) { 
            $pdo->rollBack(); 
            return ['success' => false, 'message' => 'Failed to save preference']; 
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Lane preference saved'];
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'message' => 'Database error'];
    }
}

/**
 * Randomize lane assignments when session starts (ignores player preferences)
 */
function randomizeLaneAssignments($sessionId) {
    try {
        $config = getLaneConfig($sessionId);
        $availableLanes = getAvailableLanes($sessionId);
        $pdo = getDBConnection();
        
        // Get all participants
        $stmt = $pdo->prepare("SELECT participant_id, user_id FROM session_participants WHERE session_id = ? ORDER BY joined_at ASC");
        $stmt->execute([$sessionId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($participants)) {
            return true; // No participants to assign
        }
        
        // Shuffle participants for randomization
        shuffle($participants);
        
        // Clear all existing lane assignments
        $clearStmt = $pdo->prepare("UPDATE session_participants SET lane_number = NULL WHERE session_id = ?");
        $clearStmt->execute([$sessionId]);
        
        // Assign participants to lanes sequentially using available lanes
        $laneIndex = 0;
        $playersInCurrentLane = 0;
        
        foreach ($participants as $participant) {
            // Move to next lane if current lane is full
            if ($playersInCurrentLane >= $config['players_per_lane']) {
                $laneIndex++;
                $playersInCurrentLane = 0;
                
                // Wrap around to first lane if we've used all lanes
                if ($laneIndex >= count($availableLanes)) {
                    $laneIndex = 0; // Wrap around to first lane
                    $playersInCurrentLane = 0;
                }
            }
            
            $laneNumber = $availableLanes[$laneIndex];
            
            // Assign participant to lane
            $assignStmt = $pdo->prepare("UPDATE session_participants SET lane_number = ? WHERE participant_id = ?");
            $assignStmt->execute([$laneNumber, $participant['participant_id']]);
            
            $playersInCurrentLane++;
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Error randomizing lane assignments: " . $e->getMessage());
        return false;
    }
}

/**
 * Admin auto-assign remaining participants randomly
 */
function autoAssignRemainingParticipants($sessionId) {
    try {
        $config = getLaneConfig($sessionId);
        $availableLanes = getAvailableLanes($sessionId);
        if ($config['assignment_locked']) return ['success' => false, 'message' => 'Assignment is locked'];

        $pdo = getDBConnection();
        $pdo->beginTransaction();

        // Current per-lane counts for available lanes only
        $laneCounts = [];
        foreach ($availableLanes as $laneNumber) {
            $laneCounts[$laneNumber] = 0;
        }
        $stmt = $pdo->prepare("SELECT lane_number, COUNT(*) as cnt FROM session_participants WHERE session_id = ? AND lane_number IS NOT NULL GROUP BY lane_number FOR UPDATE");
        $stmt->execute([$sessionId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lane = (int)$row['lane_number'];
            if (in_array($lane, $availableLanes)) {
                $laneCounts[$lane] = (int)$row['cnt'];
            }
        }

        // Fetch unassigned participants
        $stmt = $pdo->prepare("SELECT participant_id, user_id FROM session_participants WHERE session_id = ? AND lane_number IS NULL ORDER BY joined_at ASC FOR UPDATE");
        $stmt->execute([$sessionId]);
        $unassigned = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($unassigned)) { $pdo->commit(); return ['success' => true, 'assigned' => 0]; }

        // Shuffle for randomness
        shuffle($unassigned);

        $assignedCount = 0;
        $laneIndex = 0; // Track which lane we're currently assigning to
        
        foreach ($unassigned as $row) {
            // Find next lane with capacity from available lanes (round-robin style)
            $targetLane = null;
            $attempts = 0;
            
            // Try to find a lane with capacity, cycling through available lanes
            while ($attempts < count($availableLanes)) {
                $currentLane = $availableLanes[$laneIndex];
                if ($laneCounts[$currentLane] < $config['players_per_lane']) {
                    $targetLane = $currentLane;
                    break;
                }
                $laneIndex = ($laneIndex + 1) % count($availableLanes); // Move to next lane
                $attempts++;
            }
            
            if ($targetLane === null) break; // all lanes full

            $upd = $pdo->prepare("UPDATE session_participants SET lane_number = ? WHERE participant_id = ?");
            if ($upd->execute([$targetLane, $row['participant_id']])) {
                $laneCounts[$targetLane] += 1;
                $assignedCount += 1;
                // Move to next lane for next assignment
                $laneIndex = ($laneIndex + 1) % count($availableLanes);
            }
        }

        $pdo->commit();
        return ['success' => true, 'assigned' => $assignedCount];
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'message' => 'Database error'];
    }
}

?>