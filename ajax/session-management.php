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
    
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'Admin') {
        throw new Exception('Admin access required');
    }
    
    require_once __DIR__ . '/../includes/session-management.php';

    $action = $_POST['action'] ?? '';
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
            
        case 'start':
            $sessionId = $_POST['session_id'] ?? 0;
            if ($sessionId) {
                if (startSession($sessionId)) {
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
            
            error_log("Add score request: sessionId=$sessionId, userId=$userId, gameNumber=$gameNumber, playerScore=$playerScore, strikes=$strikes, spares=$spares, openFrames=$openFrames");
            
            // Allow saving scores even without an active session (for general score entry)
            if ($userId && $playerScore > 0) {
                if ($sessionId && $sessionId !== 'null') {
                    // Save to specific session
                    $result = addScoreToSession($sessionId, $userId, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber);
                    error_log("addScoreToSession result: " . ($result ? 'true' : 'false'));
                    if ($result) {
                        $response = ['success' => true, 'message' => 'Score added to session successfully'];
                    } else {
                        $response['message'] = 'Failed to add score to session';
                    }
                } else {
                    // Save directly to game_scores table without session
                    try {
                        // Get player's team name
                        $stmt = $pdo->prepare("SELECT team_name FROM users WHERE user_id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        $teamName = $user['team_name'] ?? null;
                        
                        // Set game mode to 'Team' for today's matches
                        $gameMode = 'Team';
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO game_scores (user_id, session_id, game_mode, game_number, player_score, strikes, spares, open_frames, lane_number, game_date, game_time, status, created_at, team_name) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Completed', NOW(), ?)
                        ");
                        $result = $stmt->execute([$userId, $sessionId, $gameMode, $gameNumber, $playerScore, $strikes, $spares, $openFrames, $laneNumber, $teamName]);
                        error_log("Direct score insert result: " . ($result ? 'true' : 'false'));
                        if ($result) {
                            $response = ['success' => true, 'message' => 'Score saved successfully'];
                        } else {
                            $response['message'] = 'Failed to save score';
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
    
    // Build session condition based on selected date
    $sessionCondition = '';
    if ($selectedDate === 'today') {
        $sessionCondition = "gs.session_id IN (SELECT session_id FROM game_sessions WHERE DATE(session_date) = CURDATE())";
    } elseif ($selectedDate === 'all') {
        $sessionCondition = "1=1"; // All time
    } else {
        // Selected date is a specific date (YYYY-MM-DD format) - find sessions for that date
        $sessionCondition = "gs.session_id IN (SELECT session_id FROM game_sessions WHERE DATE(session_date) = '" . $selectedDate . "')";
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
        WHERE gs.status = 'Completed' AND ($sessionCondition)
        ORDER BY gs.user_id, gs.game_number, gs.created_at DESC
    ");
    $stmt->execute();
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
    
    $response = [
        'success' => true,
        'players' => $players,
        'selected_date' => $selectedDate,
        'session_id' => $sessionId,
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
                
                if (addScoreToSession($sessionId, $userId, $gameNumber, $playerScore, $strikes, $spares, $openFrames)) {
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