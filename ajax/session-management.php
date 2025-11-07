<?php
// Suppress all output except our JSON
ob_start();

// Disable error display to prevent HTML in JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Test database connection first
    require_once __DIR__ . '/../database.php';
    $pdo = getDBConnection();
    
    // Test auth
    require_once __DIR__ . '/../includes/auth.php';
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }
    
    // Get the action to determine if admin access is required
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    // Define actions that require admin access
    $adminActions = [
        'create_session', 'start_session', 'end_session', 'cancel_session', 'pause_session', 'restart_session',
        'add_score', 'save_multiple_scores', 'get_team_data', 'get_teams', 'create_team_session',
        'auto_assign_remaining', 'lock_assignment', 'update_lane_config'
    ];
    
    // Check if user is admin for admin-only actions
    if (in_array($action, $adminActions) && $_SESSION['user_role'] !== 'Admin') {
        throw new Exception('Admin access required for this action');
    }
    
    require_once __DIR__ . '/../includes/session-management.php';
    
    // Fallback function for direct score saving
    function saveScoreDirectly($userId, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber, $gameMode, $sessionId = null) {
        try {
            $pdo = getDBConnection();
            
            // Get player's team name
            $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $teamName = $user['team_name'] ?? null;
            
            // Check if score already exists for this user and game number
            $checkStmt = $pdo->prepare("
                SELECT score_id FROM game_scores 
                WHERE user_id = ? AND game_number = ? AND session_id = ? AND status = 'Completed'
                ORDER BY created_at DESC LIMIT 1
            ");
            $checkStmt->execute([$userId, $gameNumber, $sessionId]);
            $existingScore = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingScore) {
                // Update existing score
                $updateStmt = $pdo->prepare("
                    UPDATE game_scores 
                    SET player_score = ?, strikes = ?, spares = ?, open_frames = ?, 
                        lane_number = ?
                    WHERE score_id = ?
                ");
                $result = $updateStmt->execute([
                    $playerScore, $strikes, $spares, $openFrames, $laneNumber, $existingScore['score_id']
                ]);
                error_log("Direct score update result: " . ($result ? 'true' : 'false'));
                return $result;
            } else {
                // Insert new score
                $stmt = $pdo->prepare("
                    INSERT INTO game_scores (user_id, session_id, game_mode, game_number, player_score, strikes, spares, open_frames, lane_number, game_date, game_time, status, created_at, team_name) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Completed', NOW(), ?)
                ");
                $result = $stmt->execute([$userId, $sessionId, $gameMode, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber, $teamName]);
                error_log("Direct score insert result: " . ($result ? 'true' : 'false'));
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Direct score save error: " . $e->getMessage());
            return false;
        }
    }

    switch ($action) {
        case 'create':
            $data = [
                'session_name' => $_POST['session_name'] ?? '',
                'session_date' => $_POST['session_date'] ?? '',
                'session_time' => $_POST['session_time'] ?? '',
                'game_mode' => $_POST['game_mode'] ?? 'Solo',
                'max_players' => $_POST['max_players'] ?? 20,
                'created_by' => $_POST['created_by'] ?? $_SESSION['user_id'],
                'notes' => $_POST['notes'] ?? ''
            ];
            
            $sessionId = createGameSession($data);
            if ($sessionId) {
                $response = ['success' => true, 'message' => 'Session created successfully', 'session_id' => $sessionId];
            } else {
                $response['message'] = 'Failed to create session';
            }
            break;
            
        case 'create_team_session':
            $selectedTeamsJson = $_POST['selected_teams'] ?? '[]';
            $selectedTeams = json_decode($selectedTeamsJson, true);
            
            if (empty($selectedTeams) || !is_array($selectedTeams)) {
                throw new Exception('At least one team must be selected');
            }
            
            // Count total players from selected teams
            $totalPlayers = 0;
            foreach ($selectedTeams as $teamName) {
                $players = getPlayersByTeam($teamName);
                $totalPlayers += count($players);
            }
            
            $data = [
                'session_name' => $_POST['session_name'] ?? '',
                'session_date' => $_POST['session_date'] ?? '',
                'session_time' => $_POST['session_time'] ?? '',
                'selected_teams' => $selectedTeams,
                'max_players' => $totalPlayers,
                'created_by' => $_POST['created_by'] ?? $_SESSION['user_id'],
                'notes' => $_POST['notes'] ?? ''
            ];
            
            $sessionId = createTeamSession($data);
            if ($sessionId) {
                $response = ['success' => true, 'message' => 'Team session created successfully', 'session_id' => $sessionId];
            } else {
                $response['message'] = 'Failed to create team session';
            }
            break;
            
        case 'get_teams':
            $teams = getAllTeams();
            $response = ['success' => true, 'teams' => $teams];
            break;
            
        case 'get_team_data':
            $selectedDate = $_POST['selected_date'] ?? 'today';
            $teamData = getTeamDataForDate($selectedDate);
            $response = ['success' => true, 'teams' => $teamData];
            break;
            
        case 'start':
            $sessionId = $_POST['session_id'] ?? 0;
            error_log("Starting session: " . $sessionId);
            if ($sessionId) {
                $result = startSession($sessionId);
                error_log("Start session result: " . ($result ? 'true' : 'false'));
                if ($result) {
                    $response = ['success' => true, 'message' => 'Session started successfully'];
                } else {
                    $response['message'] = 'Failed to start session';
                }
            } else {
                $response['message'] = 'Invalid session ID';
            }
            break;
            
        case 'end':
            $sessionId = $_POST['session_id'] ?? 0;
            if ($sessionId) {
                if (endSession($sessionId)) {
                    $response = ['success' => true, 'message' => 'Session ended successfully'];
                } else {
                    $response['message'] = 'Failed to end session';
                }
            } else {
                $response['message'] = 'Invalid session ID';
            }
            break;
            
        case 'cancel':
            $sessionId = $_POST['session_id'] ?? 0;
            if ($sessionId) {
                if (cancelSession($sessionId)) {
                    $response = ['success' => true, 'message' => 'Session cancelled successfully'];
                } else {
                    $response['message'] = 'Failed to cancel session';
                }
            } else {
                $response['message'] = 'Invalid session ID';
            }
            break;
            
        case 'pause':
            $sessionId = $_POST['session_id'] ?? 0;
            if ($sessionId) {
                if (pauseSession($sessionId)) {
                    $response = ['success' => true, 'message' => 'Session paused successfully'];
                } else {
                    $response['message'] = 'Failed to pause session';
                }
            } else {
                $response['message'] = 'Invalid session ID';
            }
            break;
            
        case 'restart':
            $sessionId = $_POST['session_id'] ?? 0;
            if ($sessionId) {
                if (restartSession($sessionId)) {
                    $response = ['success' => true, 'message' => 'Session restarted successfully'];
                } else {
                    $response['message'] = 'Failed to restart session';
                }
            } else {
                $response['message'] = 'Invalid session ID';
            }
            break;
            
        case 'add_participant':
            // Session participants functionality removed - all players can join any session automatically
            $response = ['success' => true, 'message' => 'All players can join any session automatically'];
            break;
            
        case 'add_score':
            $sessionId = $_POST['session_id'] ?? null;
            $userId = $_POST['user_id'] ?? 0;
            $gameNumber = $_POST['game_number'] ?? 1;
            $playerScore = $_POST['player_score'] ?? 0;
            $strikes = $_POST['strikes'] ?? 0;
            $spares = $_POST['spares'] ?? 0;
            $openFrames = $_POST['open_frames'] ?? 0;
            $laneNumber = $_POST['lane_number'] ?? null;
            $gameMode = $_POST['game_mode'] ?? 'Solo';
            
            error_log("Simple score update request: sessionId=$sessionId, userId=$userId, gameNumber=$gameNumber, playerScore=$playerScore");
            
            if ($userId && $playerScore >= 0) {
                try {
                    // Simple approach: Just update or insert the score directly
                    $pdo = getDBConnection();
                    
                    // First, try to find and update existing score
                    $checkStmt = $pdo->prepare("
                        SELECT score_id FROM game_scores 
                        WHERE user_id = ? AND game_number = ? AND session_id = ? AND status = 'Completed'
                        ORDER BY created_at DESC LIMIT 1
                    ");
                    $checkStmt->execute([$userId, $gameNumber, $sessionId]);
                    $existingScore = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($existingScore) {
                        // Update existing score
                        $updateStmt = $pdo->prepare("
                            UPDATE game_scores 
                            SET player_score = ?, strikes = ?, spares = ?, open_frames = ?, 
                                lane_number = ?
                            WHERE score_id = ?
                        ");
                        $result = $updateStmt->execute([
                            $playerScore, $strikes, $spares, $openFrames, $laneNumber, $existingScore['score_id']
                        ]);
                        error_log("Simple update result: " . ($result ? 'true' : 'false'));
                        
                        if ($result) {
                            $response = ['success' => true, 'message' => 'Score updated successfully'];
                        } else {
                            $response['message'] = 'Failed to update existing score';
                        }
                    } else {
                        // Insert new score
                        $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        $teamName = $user['team_name'] ?? null;
                        
                        $insertStmt = $pdo->prepare("
                            INSERT INTO game_scores (user_id, session_id, game_mode, game_number, player_score, strikes, spares, open_frames, lane_number, game_date, game_time, status, created_at, team_name) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Completed', NOW(), ?)
                        ");
                        $result = $insertStmt->execute([$userId, $sessionId, $gameMode, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber, $teamName]);
                        error_log("Simple insert result: " . ($result ? 'true' : 'false'));
                        
                        if ($result) {
                            $response = ['success' => true, 'message' => 'Score saved successfully'];
                        } else {
                            $response['message'] = 'Failed to save new score';
                        }
                    }
                    
                } catch (PDOException $e) {
                    error_log("Simple score save error: " . $e->getMessage());
                    $response['message'] = 'Database error: ' . $e->getMessage();
                }
            } else {
                $response['message'] = 'Invalid user ID or score. Values: userId=' . $userId . ', playerScore=' . $playerScore;
            }
    break;

case 'get_players_data':
    $startTime = microtime(true);
    $selectedDate = $_POST['selected_date'] ?? 'today';
    $sessionType = $_POST['session_type'] ?? 'Solo'; // Default to Solo for backward compatibility
    
    // Build session condition based on selected date and session type
    $sessionCondition = '';
    if ($selectedDate === 'today') {
        if ($sessionType === 'Team') {
            $sessionCondition = "(gs.session_id IN (SELECT session_id FROM game_sessions WHERE DATE(session_date) = CURDATE() AND game_mode = 'Team') OR (gs.session_id IS NULL AND DATE(gs.game_date) = CURDATE() AND gs.game_mode = 'Team'))";
        } else {
            $sessionCondition = "(gs.session_id IN (SELECT session_id FROM game_sessions WHERE DATE(session_date) = CURDATE() AND game_mode = 'Solo') OR (gs.session_id IS NULL AND DATE(gs.game_date) = CURDATE() AND gs.game_mode = 'Solo'))";
        }
    } elseif ($selectedDate === 'all') {
        if ($sessionType === 'Team') {
            $sessionCondition = "(gs.session_id IN (SELECT session_id FROM game_sessions WHERE game_mode = 'Team') OR (gs.session_id IS NULL AND gs.game_mode = 'Team'))";
        } else {
            $sessionCondition = "(gs.session_id IN (SELECT session_id FROM game_sessions WHERE game_mode = 'Solo') OR (gs.session_id IS NULL AND gs.game_mode = 'Solo'))";
        }
    } else {
        // Selected date is a specific date (YYYY-MM-DD format) - find sessions for that date
        if ($sessionType === 'Team') {
            $sessionCondition = "(gs.session_id IN (SELECT session_id FROM game_sessions WHERE DATE(session_date) = '" . $selectedDate . "' AND game_mode = 'Team') OR (gs.session_id IS NULL AND DATE(gs.game_date) = '" . $selectedDate . "' AND gs.game_mode = 'Team'))";
        } else {
            $sessionCondition = "(gs.session_id IN (SELECT session_id FROM game_sessions WHERE DATE(session_date) = '" . $selectedDate . "' AND game_mode = 'Solo') OR (gs.session_id IS NULL AND DATE(gs.game_date) = '" . $selectedDate . "' AND gs.game_mode = 'Solo'))";
        }
    }
    
    // Get players based on date filter selection
    $query1Start = microtime(true);
    
    if ($selectedDate === 'all') {
        // For "All Time" - show all Speedsters players (even those without scores yet)
        // For Solo mode, prioritize Speedsters team; for Team mode, show all teams
        if ($sessionType === 'Solo') {
            // Solo: Show only Speedsters players
            $stmt = $pdo->prepare("
                SELECT 
                    u.user_id, u.username, u.first_name, u.last_name, u.email, u.phone, 
                    u.skill_level, u.user_role, u.status, u.team_name, u.created_at,
                    COALESCE(ROUND(AVG(gs.player_score), 1), 0) as avg_score,
                    COUNT(gs.score_id) as games_played,
                    COALESCE(MAX(gs.player_score), 0) as best_score,
                    COALESCE(SUM(gs.strikes), 0) as total_strikes,
                    COALESCE(SUM(gs.spares), 0) as total_spares,
                    MAX(gs.created_at) as last_updated,
                    NULL as lane_number
                FROM users u
                LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed' AND gs.game_mode = ?
                WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') 
                    AND u.status = 'Active'
                    AND u.team_name = 'Speedsters'
                GROUP BY u.user_id
                ORDER BY u.first_name, u.last_name
            ");
        } else {
            // Team: Show all players with scores in the selected game mode
            $stmt = $pdo->prepare("
                SELECT 
                    u.user_id, u.username, u.first_name, u.last_name, u.email, u.phone, 
                    u.skill_level, u.user_role, u.status, u.team_name, u.created_at,
                    COALESCE(ROUND(AVG(gs.player_score), 1), 0) as avg_score,
                    COUNT(gs.score_id) as games_played,
                    COALESCE(MAX(gs.player_score), 0) as best_score,
                    COALESCE(SUM(gs.strikes), 0) as total_strikes,
                    COALESCE(SUM(gs.spares), 0) as total_spares,
                    MAX(gs.created_at) as last_updated,
                    NULL as lane_number
                FROM users u
                LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed' AND gs.game_mode = ?
                WHERE (u.user_role = 'Player' OR u.user_role = 'Admin') AND u.status = 'Active'
                GROUP BY u.user_id
                HAVING games_played > 0
                ORDER BY u.first_name, u.last_name
            ");
        }
        $stmt->execute([$sessionType]);
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get game-specific averages for each player, filtered by game mode
        $gameAvgStmt = $pdo->prepare("
            SELECT 
                user_id, 
                game_number,
                ROUND(AVG(player_score), 1) as game_avg_score,
                ROUND(AVG(strikes), 1) as game_avg_strikes,
                ROUND(AVG(spares), 1) as game_avg_spares,
                COUNT(*) as game_count
            FROM game_scores 
            WHERE status = 'Completed' AND user_id = ? AND game_mode = ?
            GROUP BY user_id, game_number
            ORDER BY game_number
        ");
        
        // Add game-specific averages to each player
        foreach ($players as &$player) {
            $gameAvgStmt->execute([$player['user_id'], $sessionType]);
            $gameAverages = $gameAvgStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Initialize game averages
            for ($game = 1; $game <= 5; $game++) {
                $player["game_{$game}_avg_score"] = 0;
                $player["game_{$game}_avg_strikes"] = 0;
                $player["game_{$game}_avg_spares"] = 0;
                $player["game_{$game}_count"] = 0;
            }
            
            // Set actual averages
            foreach ($gameAverages as $gameAvg) {
                $gameNum = $gameAvg['game_number'];
                if ($gameNum >= 1 && $gameNum <= 5) {
                    $player["game_{$gameNum}_avg_score"] = $gameAvg['game_avg_score'];
                    $player["game_{$gameNum}_avg_strikes"] = $gameAvg['game_avg_strikes'];
                    $player["game_{$gameNum}_avg_spares"] = $gameAvg['game_avg_spares'];
                    $player["game_{$gameNum}_count"] = $gameAvg['game_count'];
                }
            }
        }
    } else {
        // For today or specific date - get session participants
        if ($selectedDate === 'today') {
            // Get today's active session
            $sessionStmt = $pdo->prepare("
                SELECT session_id 
                FROM game_sessions 
                WHERE status = 'Active' AND game_mode = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $sessionStmt->execute([$sessionType]);
        } else {
            // Get session for specific date
            $sessionStmt = $pdo->prepare("
                SELECT session_id 
                FROM game_sessions 
                WHERE DATE(session_date) = ? AND game_mode = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $sessionStmt->execute([$selectedDate, $sessionType]);
            
            // Debug: Check all sessions for this date
            $allSessionsStmt = $pdo->prepare("
                SELECT session_id, session_date, status, created_at
                FROM game_sessions 
                WHERE DATE(session_date) = ? AND game_mode = ? 
                ORDER BY created_at DESC
            ");
            $allSessionsStmt->execute([$selectedDate, $sessionType]);
            $allSessions = $allSessionsStmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("DEBUG: All sessions for date $selectedDate: " . json_encode($allSessions));
        }
        
        $activeSession = $sessionStmt->fetch(PDO::FETCH_ASSOC);
        error_log("DEBUG: Found session for date $selectedDate: " . json_encode($activeSession));
        
        if ($activeSession) {
            // Get session participants (players who were selected for this session)
            $players = getSessionParticipantsForScoring($activeSession['session_id']);
            
            // If no participants found, it means this session has no selected participants
            // This could happen for old sessions or improperly created sessions
            if (empty($players)) {
                error_log("WARNING: No session participants found for session " . $activeSession['session_id']);
                error_log("This session may not have participants selected. Please add participants to this session.");
                
                // Return empty array - don't show any players
                // Admin needs to add participants to this session first
                $players = [];
            } else {
                // Force clear any score data that might be contaminating
                foreach ($players as &$player) {
                    $player['avg_score'] = 0;
                    $player['average_score'] = 0;
                    $player['total_score'] = 0;
                    $player['best_score'] = 0;
                    $player['games_played'] = 0;
                    $player['total_strikes'] = 0;
                    $player['total_spares'] = 0;
                }
            }
        } else {
            // No session found, return empty array
            $players = [];
        }
    }
    $query1Time = (microtime(true) - $query1Start) * 1000;
    
    // Get all scores for the selected session date in one simple query (skip for All Time)
    $query2Start = microtime(true);
    $allScores = [];
    
    if ($selectedDate !== 'all') {
        // Only get scores if we have an active session for the selected date
        if (isset($activeSession) && $activeSession) {
            error_log("DEBUG: Looking for scores in session_id: " . $activeSession['session_id']);
            $stmt = $pdo->prepare("
                SELECT gs.user_id, gs.game_number, gs.player_score, gs.strikes, gs.spares, gs.open_frames, gs.created_at
                FROM game_scores gs
                WHERE gs.status = 'Completed' AND gs.session_id = ?
                ORDER BY gs.user_id, gs.game_number, gs.created_at DESC
            ");
            $stmt->execute([$activeSession['session_id']]);
            $allScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("DEBUG: Found " . count($allScores) . " scores for session " . $activeSession['session_id']);
            if (count($allScores) > 0) {
                error_log("DEBUG: Sample score: " . json_encode($allScores[0]));
            }
        }
        // If no active session, allScores remains empty array
    }
    $query2Time = (microtime(true) - $query2Start) * 1000;
    
    // Process data in PHP (much faster than complex SQL) - skip for All Time view
    $processStart = microtime(true);
    $scoresByUser = [];
    $statsByUser = [];
    
    if ($selectedDate !== 'all') {
        foreach ($allScores as $score) {
        $userId = $score['user_id'];
        $gameNumber = $score['game_number'];
        
        // Group by user and game
        if (!isset($scoresByUser[$userId])) {
            $scoresByUser[$userId] = [];
        }
        if (!isset($scoresByUser[$userId][$gameNumber])) {
            $scoresByUser[$userId][$gameNumber] = $score;
        }
        
        // Calculate stats
        if (!isset($statsByUser[$userId])) {
            $statsByUser[$userId] = [
                'total_score' => 0,
                'games_played' => 0,
                'total_strikes' => 0,
                'total_spares' => 0,
                'best_score' => 0,
                'last_updated' => null
            ];
        }
        
        $statsByUser[$userId]['total_score'] += $score['player_score'];
        $statsByUser[$userId]['games_played']++;
        $statsByUser[$userId]['total_strikes'] += $score['strikes'];
        $statsByUser[$userId]['total_spares'] += $score['spares'];
        $statsByUser[$userId]['best_score'] = max($statsByUser[$userId]['best_score'], $score['player_score']);
        $statsByUser[$userId]['last_updated'] = $score['created_at'];
        }
    }
    
    // Add stats and game scores to players
    foreach ($players as &$player) {
        $userId = $player['user_id'];
        
        if ($selectedDate === 'all') {
            // For All Time view, we already have the aggregate data from SQL
            // For Overall Rankings, show the average score instead of total
            $player['total_score'] = $player['avg_score'];
            $player['last_updated'] = $player['last_updated'] ? date('M j, g:i A', strtotime($player['last_updated'])) : 'Never';
            
            // No individual game scores for All Time view
            for ($game = 1; $game <= 5; $game++) {
                $player["game_{$game}_score"] = null;
            }
        } else {
            // For session-specific view, use processed stats
            $stats = $statsByUser[$userId] ?? [
                'total_score' => 0,
                'games_played' => 0,
                'total_strikes' => 0,
                'total_spares' => 0,
                'best_score' => 0,
                'last_updated' => null
            ];
            
            $player['total_score'] = $stats['total_score'];
            $player['avg_score'] = $stats['games_played'] > 0 ? round($stats['total_score'] / $stats['games_played'], 1) : 0;
            $player['best_score'] = $stats['best_score'];
            $player['games_played'] = $stats['games_played'];
            $player['total_strikes'] = $stats['total_strikes'];
            $player['total_spares'] = $stats['total_spares'];
            $player['last_updated'] = $stats['last_updated'] ? date('M j, g:i A', strtotime($stats['last_updated'])) : 'Never';
            
            // Add individual game scores
            for ($game = 1; $game <= 5; $game++) {
                $player["game_{$game}_score"] = $scoresByUser[$userId][$game] ?? null;
            }
        }
    }
    
    // Sort by total score
    usort($players, function($a, $b) {
        return $b['total_score'] <=> $a['total_score'];
    });
    
    $processTime = (microtime(true) - $processStart) * 1000;
    $totalTime = (microtime(true) - $startTime) * 1000;
    
    // Find the session for the selected date
    $sessionId = null;
    if ($selectedDate !== 'all') {
        $targetDate = ($selectedDate === 'today') ? date('Y-m-d') : $selectedDate;
        $stmt = $pdo->prepare("
            SELECT session_id, session_name 
            FROM game_sessions 
            WHERE DATE(session_date) = ? AND (status = 'Active' OR status = 'Completed')
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$targetDate]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($session) {
            $sessionId = $session['session_id'];
        }
    }
    
    // Get session name if we have a session_id
    $sessionName = null;
    if ($sessionId) {
        $stmt = $pdo->prepare("SELECT session_name FROM game_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $sessionData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sessionData) {
            $sessionName = $sessionData['session_name'];
        }
    }
    
    $response = [
        'success' => true,
        'players' => $players,
        'selected_date' => $selectedDate,
        'session_id' => $sessionId,
        'session_name' => $sessionName,
        'debug' => [
            'query1_time' => round($query1Time, 2),
            'query2_time' => round($query2Time, 2),
            'process_time' => round($processTime, 2),
            'total_time' => round($totalTime, 2),
            'players_count' => count($players),
            'scores_count' => count($allScores)
        ]
    ];
    break;
            
        case 'save_multiple_scores':
            $sessionId = $_POST['session_id'] ?? null;
            $scoresJson = $_POST['scores'] ?? null;
            $gameMode = $_POST['game_mode'] ?? 'Solo';
            
            if (!$sessionId || !$scoresJson) {
                $response['message'] = 'Missing required fields';
                break;
            }
            
            $scores = json_decode($scoresJson, true);
            if (!$scores || !is_array($scores)) {
                $response['message'] = 'Invalid scores data';
                break;
            }
            
            $savedCount = 0;
            $errors = [];
            
            foreach ($scores as $scoreData) {
                $userId = $scoreData['user_id'] ?? null;
                $gameNumber = $scoreData['game_number'] ?? null;
                $playerScore = $scoreData['player_score'] ?? null;
                $strikes = $scoreData['strikes'] ?? 0;
                $spares = $scoreData['spares'] ?? 0;
                $openFrames = $scoreData['open_frames'] ?? 0;
                
                if (!$userId || !$gameNumber || !$playerScore) {
                    $errors[] = "Missing data for user $userId";
                    continue;
                }
                
                if (addScoreToSession($sessionId, $userId, $gameNumber, $playerScore, $strikes, $spares, $openFrames, null, $gameMode)) {
                    $savedCount++;
                } else {
                    $errors[] = "Failed to save score for user $userId";
                }
            }
            
            if ($savedCount > 0) {
                $response = [
                    'success' => true, 
                    'message' => "Saved $savedCount scores successfully",
                    'saved_count' => $savedCount,
                    'errors' => $errors
                ];
            } else {
                $response['message'] = 'Failed to save any scores. Errors: ' . implode(', ', $errors);
            }
            break;

        // ===============================
        // Lane selection & assignment
        // ===============================

        case 'lane_status':
            $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? null;
            if (!$sessionId) { $response['message'] = 'Missing session_id'; break; }
            $status = getLaneStatus($sessionId);
            if (!$status) { $response['message'] = 'Failed to load lane status'; break; }

            // Include current user's lane if participating
            $userLane = null;
            try {
                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                $stmt->execute([$sessionId, $_SESSION['user_id']]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) { $userLane = $row['lane_number']; }
            } catch (Exception $e) {}

            $response = [
                'success' => true,
                'status' => $status,
                'user_lane' => $userLane,
            ];
            break;

        case 'reset_session_timer':
            $sessionId = $_POST['session_id'] ?? null;
            if (!$sessionId) { $response['message'] = 'Missing session_id'; break; }
            
            try {
                $result = resetSessionStartTime($sessionId);
                if ($result) {
                    $response = ['success' => true, 'message' => 'Session timer reset successfully'];
                } else {
                    $response['message'] = 'Failed to reset session timer';
                }
            } catch (Exception $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
            break;
            
        case 'draw_random_lane':
            $sessionId = $_POST['session_id'] ?? null;
            if (!$sessionId) { $response['message'] = 'Missing session_id'; break; }
            
            try {
                $pdo = getDBConnection();
                
                // Check if user already has a lane assigned
                $checkStmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                $checkStmt->execute([$sessionId, $_SESSION['user_id']]);
                $existingLane = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existingLane && $existingLane['lane_number']) {
                    $response['message'] = 'You already have a lane assigned';
                    break;
                }
                
                // Get session lane configuration
                $config = getLaneConfig($sessionId);
                $availableLanes = getAvailableLanes($sessionId);
                
                // Get available lanes (not full)
                $stmt = $pdo->prepare("
                    SELECT lane_number, COUNT(*) as player_count 
                    FROM session_participants 
                    WHERE session_id = ? AND lane_number IS NOT NULL 
                    GROUP BY lane_number
                ");
                $stmt->execute([$sessionId]);
                $laneCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                // Find available lanes from the specific lane list
                $availableLanesForAssignment = [];
                foreach ($availableLanes as $laneNumber) {
                    $currentCount = isset($laneCounts[$laneNumber]) ? $laneCounts[$laneNumber] : 0;
                    if ($currentCount < $config['players_per_lane']) {
                        $availableLanesForAssignment[] = $laneNumber;
                    }
                }
                
                if (empty($availableLanesForAssignment)) {
                    $response['message'] = 'No lanes available';
                    break;
                }
                
                // Randomly select a lane from available lanes
                $randomLane = $availableLanesForAssignment[array_rand($availableLanesForAssignment)];
                
                // Assign user to the random lane
                $assignStmt = $pdo->prepare("UPDATE session_participants SET lane_number = ? WHERE session_id = ? AND user_id = ?");
                $result = $assignStmt->execute([$randomLane, $sessionId, $_SESSION['user_id']]);
                
                if ($result) {
                    $response = ['success' => true, 'lane_number' => $randomLane, 'message' => 'Lane assigned successfully'];
                } else {
                    $response['message'] = 'Failed to assign lane';
                }
            } catch (Exception $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
            break;

        case 'auto_assign_remaining':
            $sessionId = $_POST['session_id'] ?? null;
            if (!$sessionId) { $response['message'] = 'Missing session_id'; break; }
            $result = autoAssignRemainingParticipants($sessionId);
            $response = array_merge(['success' => $result['success'] ?? false], $result);
            break;

        case 'lock_assignment':
            $sessionId = $_POST['session_id'] ?? null;
            $lock = isset($_POST['lock']) ? (int)$_POST['lock'] : 1;
            if (!$sessionId) { $response['message'] = 'Missing session_id'; break; }
            $ok = updateLaneConfig($sessionId, 
                getLaneConfig($sessionId)['lanes_count'], 
                getLaneConfig($sessionId)['players_per_lane'], 
                $lock ? 0 : 1, 
                $lock
            );
            $response = $ok ? ['success' => true, 'message' => ($lock ? 'Assignment locked' : 'Assignment unlocked')] : ['success' => false, 'message' => 'Failed to update lock'];
            break;

        case 'update_lane_config':
            $sessionId = $_POST['session_id'] ?? null;
            $lanesCount = isset($_POST['lanes_count']) ? (int)$_POST['lanes_count'] : null;
            $playersPerLane = isset($_POST['players_per_lane']) ? (int)$_POST['players_per_lane'] : null;
            $laneSelectionOpen = isset($_POST['lane_selection_open']) ? (int)$_POST['lane_selection_open'] : null;
            if (!$sessionId || !$lanesCount || !$playersPerLane) { $response['message'] = 'Missing required fields'; break; }
            $ok = updateLaneConfig($sessionId, $lanesCount, $playersPerLane, $laneSelectionOpen);
            $response = $ok ? ['success' => true, 'message' => 'Lane configuration updated'] : ['success' => false, 'message' => 'Failed to update lane configuration'];
            break;

        case 'get_player_history':
            $userId = $_POST['user_id'] ?? null;
            if (!$userId) {
                $response['message'] = 'Missing user ID';
                break;
            }
            
            try {
                $pdo = getDBConnection();
                
                // Get player's complete score history
                $stmt = $pdo->prepare("
                    SELECT 
                        gs.*,
                        gsess.session_name,
                        gsess.session_date
                    FROM game_scores gs
                    LEFT JOIN game_sessions gsess ON gs.session_id = gsess.session_id
                    WHERE gs.user_id = ? AND gs.status = 'Completed'
                    ORDER BY gs.game_date DESC, gs.game_number ASC
                    LIMIT 50
                ");
                $stmt->execute([$userId]);
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $response = [
                    'success' => true,
                    'history' => $history,
                    'count' => count($history)
                ];
                
            } catch (PDOException $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
            break;
            
        case 'update_player_lane':
            $userId = $_POST['user_id'] ?? null;
            $laneNumber = $_POST['lane_number'] ?? null;
            $sessionId = $_POST['session_id'] ?? null;
            
            error_log("Lane update request - User ID: $userId, Lane Number: $laneNumber, Session ID: $sessionId");
            
            if (!$userId) {
                $response['message'] = 'Missing user ID';
                error_log("Lane update failed: Missing user ID");
                break;
            }
            
            if (!$sessionId) {
                $response['message'] = 'Missing session ID';
                error_log("Lane update failed: Missing session ID");
                break;
            }
            
            try {
                $pdo = getDBConnection();
                
                // Check if the user is a participant in this session
                $checkStmt = $pdo->prepare("SELECT participant_id FROM session_participants WHERE session_id = ? AND user_id = ?");
                $checkStmt->execute([$sessionId, $userId]);
                $participant = $checkStmt->fetch();
                
                if (!$participant) {
                    error_log("Lane update failed: User $userId is not a participant in session $sessionId");
                    $response['message'] = 'User is not a participant in this session';
                    break;
                }
                
                // Update the lane number in session_participants table
                $stmt = $pdo->prepare("UPDATE session_participants SET lane_number = ? WHERE session_id = ? AND user_id = ?");
                $result = $stmt->execute([$laneNumber, $sessionId, $userId]);
                
                error_log("Lane update SQL result: " . ($result ? 'true' : 'false'));
                error_log("Lane update affected rows: " . $stmt->rowCount());
                
                if ($result && $stmt->rowCount() > 0) {
                    $response = ['success' => true, 'message' => 'Lane number updated successfully'];
                    error_log("Lane update successful for user $userId to lane $laneNumber in session $sessionId");
                } else {
                    $response['message'] = 'Failed to update lane number - no changes made';
                    error_log("Lane update failed: No rows affected for user $userId in session $sessionId");
                }
            } catch (PDOException $e) {
                error_log("Lane update error: " . $e->getMessage());
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
            break;
            
        case 'get_available_lanes':
            $sessionId = $_POST['session_id'] ?? null;
            
            if (!$sessionId) {
                $response['message'] = 'Missing session ID';
                break;
            }
            
            try {
                require_once __DIR__ . '/../includes/session-management.php';
                $availableLanes = getAvailableLanes($sessionId);
                $response = ['success' => true, 'lanes' => $availableLanes];
            } catch (Exception $e) {
                $response['message'] = 'Error getting available lanes: ' . $e->getMessage();
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
    $response['debug'] = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
}

// Clean any output buffer and send only JSON
ob_clean();
echo json_encode($response);
exit;
?>