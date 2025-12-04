<?php
/**
 * Duo Match System - AJAX Handler
 * Handles all duo-related AJAX requests
 */

// Suppress output and errors
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Load dependencies
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../includes/auth.php';
    require_once __DIR__ . '/../includes/duo-helper.php';
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }
    
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['user_role'] ?? 'Player';
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    // Define admin-only actions
    // NOTE: auto_pair_now is intentionally NOT admin-only so that
    // the system/player flow can trigger automatic pairing.
    $adminActions = ['create_duo_session', 'override_lane', 'bulk_save_scores', 'admin_force_pair'];
    
    // Check admin access for admin-only actions
    if (in_array($action, $adminActions) && $userRole !== 'Admin') {
        throw new Exception('Admin access required');
    }
    
    // =============================================
    // ACTIONS HANDLER
    // =============================================
    
    switch ($action) {
        
        // =====================================
        // LOBBY ACTIONS
        // =====================================
        
        case 'calculate_average':
            // Calculate player's average score (last N games)
            $targetUserId = $_POST['user_id'] ?? $_GET['user_id'] ?? $userId;
            $numGames = $_POST['num_games'] ?? $_GET['num_games'] ?? 5;
            
            $avgScore = calculatePlayerAverage($targetUserId, $numGames);
            
            $response = [
                'success' => true,
                'average' => $avgScore,
                'user_id' => $targetUserId,
                'games_counted' => $numGames
            ];
            break;
            
        case 'get_user_stats':
            // Get user's game statistics
            $targetUserId = $_POST['user_id'] ?? $_GET['user_id'] ?? $userId;
            
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(DISTINCT score_id) as games_played,
                    MAX(recorded_at) as last_played
                FROM game_scores
                WHERE user_id = ? AND status = 'Completed'
            ");
            $stmt->execute([$targetUserId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $response = [
                'success' => true,
                'games_played' => (int)($stats['games_played'] ?? 0),
                'last_played' => $stats['last_played'] ?? null,
                'user_id' => $targetUserId
            ];
            break;
        
        case 'join_lobby':
            // Player joins duo session lobby
            $sessionId = $_POST['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            // Check if already in duo for this session
            $existingDuo = isPlayerInDuo($userId, $sessionId);
            if ($existingDuo) {
                $response = [
                    'success' => false,
                    'already_paired' => true,
                    'duo' => $existingDuo,
                    'message' => 'You are already in a duo for this session!'
                ];
                break;
            }
            
            // Check if already in lobby
            if (hasPlayerJoinedLobby($userId, $sessionId)) {
                $response = [
                    'success' => false,
                    'already_joined' => true,
                    'message' => 'You have already joined this lobby!'
                ];
                break;
            }
            
            // Calculate player's average
            $avgScore = calculatePlayerAverage($userId, 5);
            
            // Add to lobby
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO duo_join_lobby (session_id, user_id, avg_score)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$sessionId, $userId, $avgScore]);
            
            // Get updated lobby status
            $lobbyStatus = getLobbyStatus($sessionId);
            
            $response = [
                'success' => true,
                'message' => 'Successfully joined the lobby!',
                'avg_score' => $avgScore,
                'lobby_status' => $lobbyStatus
            ];
            break;
            
        case 'get_lobby_status':
            // Get current lobby status (for real-time updates)
            $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            $response = getLobbyStatus($sessionId);
            break;
            
        case 'leave_lobby':
            // Player leaves lobby before pairing
            $sessionId = $_POST['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                DELETE FROM duo_join_lobby 
                WHERE session_id = ? AND user_id = ? AND is_paired = FALSE
            ");
            $stmt->execute([$sessionId, $userId]);
            
            $response = [
                'success' => true,
                'message' => 'Left the lobby successfully'
            ];
            break;
            
        // =====================================
        // PAIRING ACTIONS
        // =====================================
        
        case 'auto_pair_now':
            // Auto-pairing triggered when all players have joined
            $sessionId = $_POST['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            // Check if all players have joined
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as players_in_lobby
                FROM duo_join_lobby
                WHERE session_id = ? AND is_paired = FALSE
            ");
            $stmt->execute([$sessionId]);
            $lobbyCount = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get total expected players
            $stmt = $pdo->prepare("
                SELECT max_players
                FROM game_sessions
                WHERE session_id = ?
            ");
            $stmt->execute([$sessionId]);
            $sessionInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $playersInLobby = (int)$lobbyCount['players_in_lobby'];
            $totalPlayers = (int)($sessionInfo['max_players'] ?? 8);
            
            // Only pair if all players have joined
            if ($playersInLobby >= $totalPlayers) {
                $response = autoPairPlayers($sessionId);
            } else {
                $response = [
                    'success' => false,
                    'message' => "Waiting for all players ({$playersInLobby}/{$totalPlayers})",
                    'players_in_lobby' => $playersInLobby,
                    'total_players' => $totalPlayers
                ];
            }
            break;
            
        case 'get_duos':
            // Get all duos for a session
            $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            $duos = getDuosBySession($sessionId);
            
            $response = [
                'success' => true,
                'duos' => $duos,
                'count' => count($duos)
            ];
            break;
            
        case 'get_all_duos':
            // Get all duos with aggregated scores (for score table)
            $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? null;
            
            $pdo = getDBConnection();
            
            // Build query
            $query = "
                SELECT 
                    dt.duo_id,
                    dt.duo_name,
                    dt.player1_id,
                    dt.player2_id,
                    dt.player1_avg,
                    dt.player2_avg,
                    dt.combined_total_score,
                    dt.lane_number,
                    dt.status,
                    dt.created_at,
                    dt.updated_at,
                    u1.first_name as player1_first_name,
                    u1.last_name as player1_last_name,
                    u1.profile_picture as player1_picture,
                    u2.first_name as player2_first_name,
                    u2.last_name as player2_last_name,
                    u2.profile_picture as player2_picture,
                    COUNT(DISTINCT gs.score_id) as games_played,
                    MAX(gs.score) as best_game,
                    SUM(gs.strikes) as combined_strikes,
                    MAX(gs.recorded_at) as last_updated
                FROM duo_teams dt
                LEFT JOIN users u1 ON dt.player1_id = u1.user_id
                LEFT JOIN users u2 ON dt.player2_id = u2.user_id
                LEFT JOIN game_scores gs ON gs.duo_id = dt.duo_id
            ";
            
            if ($sessionId) {
                $query .= " WHERE dt.session_id = ?";
            }
            
            $query .= " 
                GROUP BY dt.duo_id
                ORDER BY dt.combined_total_score DESC, dt.created_at ASC
            ";
            
            $stmt = $pdo->prepare($query);
            if ($sessionId) {
                $stmt->execute([$sessionId]);
            } else {
                $stmt->execute();
            }
            $duos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get game-specific scores
            $gameScoresQuery = "
                SELECT 
                    gs.score_id,
                    gs.duo_id,
                    gs.game_number,
                    gs.score,
                    gs.strikes,
                    gs.spares,
                    gs.user_id,
                    gs.recorded_at,
                    dt.duo_name,
                    dt.player1_id,
                    dt.player2_id,
                    u1.first_name as player1_first_name,
                    u1.last_name as player1_last_name,
                    u1.profile_picture as player1_picture,
                    u2.first_name as player2_first_name,
                    u2.last_name as player2_last_name,
                    u2.profile_picture as player2_picture,
                    TIME_FORMAT(gs.recorded_at, '%h:%i %p') as time
                FROM game_scores gs
                JOIN duo_teams dt ON gs.duo_id = dt.duo_id
                LEFT JOIN users u1 ON dt.player1_id = u1.user_id
                LEFT JOIN users u2 ON dt.player2_id = u2.user_id
                WHERE gs.duo_id IS NOT NULL
            ";
            
            if ($sessionId) {
                $gameScoresQuery .= " AND dt.session_id = ?";
            }
            
            $gameScoresQuery .= " ORDER BY gs.game_number ASC, gs.score DESC";
            
            $stmt = $pdo->prepare($gameScoresQuery);
            if ($sessionId) {
                $stmt->execute([$sessionId]);
            } else {
                $stmt->execute();
            }
            $allGameScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group scores by duo and game number, calculate combined scores
            $gameScoresGrouped = [];
            foreach ($allGameScores as $score) {
                $key = $score['duo_id'] . '_' . $score['game_number'];
                if (!isset($gameScoresGrouped[$key])) {
                    $gameScoresGrouped[$key] = [
                        'duo_id' => $score['duo_id'],
                        'duo_name' => $score['duo_name'],
                        'game_number' => $score['game_number'],
                        'player1_score' => 0,
                        'player2_score' => 0,
                        'combined_score' => 0,
                        'combined_strikes' => 0,
                        'player1_first_name' => $score['player1_first_name'],
                        'player1_last_name' => $score['player1_last_name'],
                        'player1_picture' => $score['player1_picture'],
                        'player2_first_name' => $score['player2_first_name'],
                        'player2_last_name' => $score['player2_last_name'],
                        'player2_picture' => $score['player2_picture'],
                        'time' => $score['time']
                    ];
                }
                
                if ($score['user_id'] == $score['player1_id']) {
                    $gameScoresGrouped[$key]['player1_score'] = $score['score'];
                } else {
                    $gameScoresGrouped[$key]['player2_score'] = $score['score'];
                }
                
                $gameScoresGrouped[$key]['combined_score'] += $score['score'];
                $gameScoresGrouped[$key]['combined_strikes'] += $score['strikes'];
            }
            
            $gameScores = array_values($gameScoresGrouped);
            
            $response = [
                'success' => true,
                'duos' => $duos,
                'game_scores' => $gameScores,
                'count' => count($duos)
            ];
            break;
            
        case 'get_my_duo':
            // Get current user's duo for a session
            $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            $duo = isPlayerInDuo($userId, $sessionId);
            
            if ($duo) {
                // Get full duo details
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    SELECT 
                        dt.*,
                        u1.first_name as player1_first_name,
                        u1.last_name as player1_last_name,
                        u1.profile_picture as player1_picture,
                        u2.first_name as player2_first_name,
                        u2.last_name as player2_last_name,
                        u2.profile_picture as player2_picture
                    FROM duo_teams dt
                    JOIN users u1 ON dt.player1_id = u1.user_id
                    JOIN users u2 ON dt.player2_id = u2.user_id
                    WHERE dt.duo_id = ?
                ");
                $stmt->execute([$duo['duo_id']]);
                $fullDuo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Determine if current user is player1 or player2
                $fullDuo['is_player1'] = ($userId == $fullDuo['player1_id']);
                $fullDuo['partner_id'] = $fullDuo['is_player1'] ? $fullDuo['player2_id'] : $fullDuo['player1_id'];
                $fullDuo['partner_name'] = $fullDuo['is_player1'] ? 
                    $fullDuo['player2_first_name'] . ' ' . $fullDuo['player2_last_name'] :
                    $fullDuo['player1_first_name'] . ' ' . $fullDuo['player1_last_name'];
                
                $response = [
                    'success' => true,
                    'duo' => $fullDuo,
                    'in_duo' => true
                ];
            } else {
                // Get player count in lobby
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as players_in_lobby
                    FROM duo_join_lobby
                    WHERE session_id = ? AND is_paired = FALSE
                ");
                $stmt->execute([$sessionId]);
                $lobbyCount = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Get total expected players from session
                $stmt = $pdo->prepare("
                    SELECT max_players
                    FROM game_sessions
                    WHERE session_id = ?
                ");
                $stmt->execute([$sessionId]);
                $sessionInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $response = [
                    'success' => true,
                    'in_duo' => false,
                    'message' => 'Not in a duo for this session',
                    'players_in_lobby' => (int)$lobbyCount['players_in_lobby'],
                    'total_players' => (int)($sessionInfo['max_players'] ?? 8)
                ];
            }
            break;
            
        case 'get_duo_details':
            // Get details of a specific duo by ID
            $duoId = $_POST['duo_id'] ?? $_GET['duo_id'] ?? 0;
            
            if (!$duoId) {
                throw new Exception('Duo ID required');
            }
            
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    dt.*,
                    u1.first_name as player1_first_name,
                    u1.last_name as player1_last_name,
                    u1.profile_picture as player1_picture,
                    u2.first_name as player2_first_name,
                    u2.last_name as player2_last_name,
                    u2.profile_picture as player2_picture
                FROM duo_teams dt
                JOIN users u1 ON dt.player1_id = u1.user_id
                JOIN users u2 ON dt.player2_id = u2.user_id
                WHERE dt.duo_id = ?
            ");
            $stmt->execute([$duoId]);
            $duo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($duo) {
                $response = [
                    'success' => true,
                    'duo' => $duo
                ];
            } else {
                throw new Exception('Duo not found');
            }
            break;
            
        case 'update_duo_name':
            // Update duo name (either player can do this)
            $duoId = $_POST['duo_id'] ?? 0;
            $duoName = $_POST['duo_name'] ?? '';
            
            if (!$duoId || empty($duoName)) {
                throw new Exception('Duo ID and name required');
            }
            
            // Verify player is in this duo
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT duo_id FROM duo_teams 
                WHERE duo_id = ? AND (player1_id = ? OR player2_id = ?)
            ");
            $stmt->execute([$duoId, $userId, $userId]);
            if (!$stmt->fetch()) {
                throw new Exception('You are not in this duo');
            }
            
            // Update name
            if (updateDuoName($duoId, $duoName)) {
                $response = [
                    'success' => true,
                    'message' => 'Duo name updated successfully!',
                    'duo_name' => trim($duoName)
                ];
            } else {
                throw new Exception('Failed to update duo name');
            }
            break;
            
        // =====================================
        // LANE VOTING ACTIONS
        // =====================================
        
        case 'vote_lane':
            // Player votes for lane preference
            $duoId = $_POST['duo_id'] ?? 0;
            $laneNumber = $_POST['lane_number'] ?? 0;
            
            if (!$duoId || !$laneNumber) {
                throw new Exception('Duo ID and lane number required');
            }
            
            // Verify player is in this duo
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT player1_id, player2_id FROM duo_teams WHERE duo_id = ?
            ");
            $stmt->execute([$duoId]);
            $duo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$duo || ($duo['player1_id'] != $userId && $duo['player2_id'] != $userId)) {
                throw new Exception('You are not in this duo');
            }
            
            // Record vote
            $response = recordLaneVote($duoId, $userId, $laneNumber);
            
            if ($response['success'] && $response['consensus']) {
                $response['message'] = "Consensus reached! Assigned to Lane {$response['assigned_lane']}";
            } else if ($response['success']) {
                $response['message'] = 'Your vote has been recorded. Waiting for partner...';
            }
            break;
            
        case 'override_lane':
            // Admin overrides lane assignment
            $duoId = $_POST['duo_id'] ?? 0;
            $laneNumber = $_POST['lane_number'] ?? 0;
            
            if (!$duoId || !$laneNumber) {
                throw new Exception('Duo ID and lane number required');
            }
            
            if (assignLaneToDuo($duoId, $laneNumber)) {
                $response = [
                    'success' => true,
                    'message' => "Lane {$laneNumber} assigned successfully"
                ];
            } else {
                throw new Exception('Failed to assign lane');
            }
            break;
            
        // =====================================
        // SCORING ACTIONS
        // =====================================
        
        case 'get_duo_scores':
            // Get scores for a duo
            $duoId = $_POST['duo_id'] ?? $_GET['duo_id'] ?? 0;
            $gameNumber = $_POST['game_number'] ?? $_GET['game_number'] ?? null;
            
            if (!$duoId) {
                throw new Exception('Duo ID required');
            }
            
            $scores = getDuoScores($duoId, $gameNumber);
            
            $response = [
                'success' => true,
                'scores' => $scores
            ];
            break;
            
        case 'save_duo_score':
            // Admin saves individual player score (which contributes to duo)
            $duoId = $_POST['duo_id'] ?? 0;
            $playerId = $_POST['user_id'] ?? 0;
            $gameNumber = $_POST['game_number'] ?? 0;
            $playerScore = $_POST['player_score'] ?? 0;
            $strikes = $_POST['strikes'] ?? 0;
            $spares = $_POST['spares'] ?? 0;
            $openFrames = $_POST['open_frames'] ?? 0;
            $sessionId = $_POST['session_id'] ?? 0;
            
            if (!$duoId || !$playerId || !$gameNumber || !$sessionId) {
                throw new Exception('Missing required fields');
            }
            
            // Verify player is in this duo
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT player1_id, player2_id, lane_number 
                FROM duo_teams 
                WHERE duo_id = ?
            ");
            $stmt->execute([$duoId]);
            $duo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$duo || ($duo['player1_id'] != $playerId && $duo['player2_id'] != $playerId)) {
                throw new Exception('Player not in this duo');
            }
            
            // Check if score already exists
            $checkStmt = $pdo->prepare("
                SELECT score_id FROM game_scores 
                WHERE user_id = ? AND game_number = ? AND duo_id = ? AND status = 'Completed'
            ");
            $checkStmt->execute([$playerId, $gameNumber, $duoId]);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                // Update existing score
                $updateStmt = $pdo->prepare("
                    UPDATE game_scores 
                    SET player_score = ?, strikes = ?, spares = ?, open_frames = ?, 
                        lane_number = ?
                    WHERE score_id = ?
                ");
                $updateStmt->execute([
                    $playerScore, $strikes, $spares, $openFrames,
                    $duo['lane_number'], $existing['score_id']
                ]);
            } else {
                // Insert new score
                $insertStmt = $pdo->prepare("
                    INSERT INTO game_scores (
                        session_id, duo_id, user_id, game_number, player_score,
                        strikes, spares, open_frames, lane_number, game_mode, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Doubles', 'Completed')
                ");
                $insertStmt->execute([
                    $sessionId, $duoId, $playerId, $gameNumber, $playerScore,
                    $strikes, $spares, $openFrames, $duo['lane_number']
                ]);
            }
            
            // Update duo's combined total score
            updateDuoCombinedScore($duoId);
            
            $response = [
                'success' => true,
                'message' => 'Score saved successfully'
            ];
            break;
            
        // =====================================
        // NOTIFICATIONS
        // =====================================
        
        case 'get_notifications':
            // Get unread notifications for current user
            $notifications = getUnreadNotifications($userId);
            
            $response = [
                'success' => true,
                'notifications' => $notifications,
                'count' => count($notifications)
            ];
            break;
            
        case 'mark_notification_read':
            // Mark notification as read
            $notificationId = $_POST['notification_id'] ?? 0;
            
            if (!$notificationId) {
                throw new Exception('Notification ID required');
            }
            
            if (markNotificationRead($notificationId)) {
                $response = [
                    'success' => true,
                    'message' => 'Notification marked as read'
                ];
            } else {
                throw new Exception('Failed to mark notification as read');
            }
            break;
            
        // =====================================
        // STATS & ANALYTICS
        // =====================================
        
        case 'get_duo_leaderboard':
            // Get duo leaderboard for a session
            $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            $duos = getDuosBySession($sessionId);
            
            // Add rank
            foreach ($duos as $index => &$duo) {
                $duo['rank'] = $index + 1;
            }
            
            $response = [
                'success' => true,
                'leaderboard' => $duos
            ];
            break;
        
        case 'update_duo_lane':
            // Update lane number for a duo (admin only)
            $duoId = $_POST['duo_id'] ?? 0;
            $laneNumber = $_POST['lane_number'] ?? 0;
            
            if (!$duoId || !$laneNumber) {
                throw new Exception('Duo ID and lane number required');
            }
            
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                UPDATE duo_teams 
                SET lane_number = ? 
                WHERE duo_id = ?
            ");
            $stmt->execute([$laneNumber, $duoId]);
            
            $response = [
                'success' => true,
                'message' => 'Lane updated successfully'
            ];
            break;
        
        case 'admin_force_pair':
            // Admin manually triggers auto-pairing (ignores player count check)
            $sessionId = $_POST['session_id'] ?? 0;
            
            if (!$sessionId) {
                throw new Exception('Session ID required');
            }
            
            // Force pairing regardless of player count
            $response = autoPairPlayers($sessionId);
            
            if ($response['success']) {
                $duoCount = is_array($response['duos']) ? count($response['duos']) : 0;
                $response['message'] = $duoCount . ' duo(s) created successfully!';
            }
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Duo management error: " . $e->getMessage());
}

// Clean output buffer and send JSON response
ob_clean();
echo json_encode($response);
exit;
?>

