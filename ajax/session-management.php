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
        'add_score', 'save_multiple_scores', 'get_team_data', 'get_teams', 'create_team_session'
    ];
    
    // Check if user is admin for admin-only actions
    if (in_array($action, $adminActions) && $_SESSION['user_role'] !== 'Admin') {
        throw new Exception('Admin access required for this action');
    }
    
    require_once __DIR__ . '/../includes/session-management.php';

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
            $gameMode = $_POST['game_mode'] ?? 'Solo'; // Default to Solo for backward compatibility
            
            error_log("Add score request: sessionId=$sessionId, userId=$userId, gameNumber=$gameNumber, playerScore=$playerScore, strikes=$strikes, spares=$spares, openFrames=$openFrames");
            error_log("SessionId type: " . gettype($sessionId) . ", value: " . var_export($sessionId, true));
            
            // Debug session_id validation
            if ($sessionId && $sessionId !== 'null' && $sessionId !== '') {
                // Check if session exists
                $checkSessionStmt = $pdo->prepare("SELECT session_id FROM game_sessions WHERE session_id = ?");
                $checkSessionStmt->execute([$sessionId]);
                $sessionExists = $checkSessionStmt->fetch(PDO::FETCH_ASSOC);
                error_log("Session validation: sessionId=$sessionId, exists=" . ($sessionExists ? 'true' : 'false'));
            } else {
                error_log("Session validation: sessionId is null or invalid, will use NULL");
            }
            
            // Allow saving scores even without an active session (for general score entry)
            if ($userId && $playerScore > 0) {
                if ($sessionId && $sessionId !== 'null' && $sessionId !== '' && $sessionId !== null) {
                    // Save to specific session
                    $result = addScoreToSession($sessionId, $userId, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber, $gameMode);
                    error_log("addScoreToSession result: " . ($result ? 'true' : 'false'));
                    if ($result) {
                        $response = ['success' => true, 'message' => 'Score added to session successfully'];
                    } else {
                        $response['message'] = 'Failed to add score to session';
                    }
                } else {
                    // Save directly to game_scores table without session
                    try {
                        // Check if score already exists for this user and game number (without session)
                        $checkStmt = $pdo->prepare("
                            SELECT score_id FROM game_scores 
                            WHERE user_id = ? AND game_number = ? AND DATE(game_date) = CURDATE() AND status = 'Completed'
                            ORDER BY created_at DESC LIMIT 1
                        ");
                        $checkStmt->execute([$userId, $gameNumber]);
                        $existingScore = $checkStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($existingScore) {
                            // Update existing score instead of creating duplicate
                            $updateStmt = $pdo->prepare("
                                UPDATE game_scores 
                                SET player_score = ?, strikes = ?, spares = ?, open_frames = ?, 
                                    lane_number = ?, updated_at = NOW()
                                WHERE score_id = ?
                            ");
                            $result = $updateStmt->execute([
                                $playerScore, $strikes, $spares, $openFrames, $laneNumber, $existingScore['score_id']
                            ]);
                            error_log("Direct score update result: " . ($result ? 'true' : 'false'));
                            if ($result) {
                                $response = ['success' => true, 'message' => 'Score updated successfully'];
                            } else {
                                $response['message'] = 'Failed to update score';
                            }
                        } else {
                            // Get player's team name
                            $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
                            $stmt->execute([$userId]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            $teamName = $user['team_name'] ?? null;
                            
                            // Use the game_mode parameter passed from the frontend
                            
                            // Set session_id to NULL if it's invalid or null
                            $validSessionId = ($sessionId && $sessionId !== 'null' && $sessionId !== '') ? $sessionId : null;
                            
                            $stmt = $pdo->prepare("
                                INSERT INTO game_scores (user_id, session_id, game_mode, game_number, player_score, strikes, spares, open_frames, lane_number, game_date, game_time, status, created_at, team_name) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Completed', NOW(), ?)
                            ");
                            $result = $stmt->execute([$userId, $validSessionId, $gameMode, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber, $teamName]);
                            error_log("Direct score insert result: " . ($result ? 'true' : 'false'));
                            if ($result) {
                                $response = ['success' => true, 'message' => 'Score saved successfully'];
                            } else {
                                $response['message'] = 'Failed to save score';
                            }
                        }
                    } catch (PDOException $e) {
                        error_log("Direct score insert error: " . $e->getMessage());
                        $response['message'] = 'Database error: ' . $e->getMessage();
                    }
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
    
    // Get all players first (simple query) - include both Player and Admin roles
    $query1Start = microtime(true);
    $stmt = $pdo->prepare("
        SELECT user_id, username, first_name, last_name, email, phone, skill_level, user_role, status, team_name, created_at
        FROM users 
        WHERE (user_role = 'Player' OR user_role = 'Admin') AND status = 'Active'
        ORDER BY first_name, last_name
    ");
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $query1Time = (microtime(true) - $query1Start) * 1000;
    
    // Get all scores for the selected session date in one simple query
    $query2Start = microtime(true);
    $stmt = $pdo->prepare("
        SELECT gs.user_id, gs.game_number, gs.player_score, gs.strikes, gs.spares, gs.open_frames, gs.created_at
        FROM game_scores gs
        WHERE gs.status = 'Completed' AND gs.game_mode = ? AND ($sessionCondition)
        ORDER BY gs.user_id, gs.game_number, gs.created_at DESC
    ");
    $stmt->execute([$sessionType]);
    $allScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $query2Time = (microtime(true) - $query2Start) * 1000;
    
    // Process data in PHP (much faster than complex SQL)
    $processStart = microtime(true);
    $scoresByUser = [];
    $statsByUser = [];
    
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
    
    // Add stats and game scores to players
    foreach ($players as &$player) {
        $userId = $player['user_id'];
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