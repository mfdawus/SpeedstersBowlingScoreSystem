<?php
// Start output buffering to catch any unexpected output
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

try {
    // Start session safely
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require_once '../includes/session-management.php';
    require_once '../includes/auth.php';

    // Check if user is logged in and is admin
    if (!isLoggedIn() || !isAdmin()) {
        ob_clean(); // Clear any output
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
} catch (Exception $e) {
    ob_clean();
    error_log("AJAX Session Creation - Include Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server configuration error']);
    exit();
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get the action
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create_session_draft':
        createSessionDraft();
        break;
        
    case 'create_session_with_participants':
        createSessionWithParticipantsHandler();
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
}

/**
 * Create a session draft that will be completed after participant selection
 */
function createSessionDraft() {
    try {
        // Validate required fields
        $required = ['session_name', 'session_date', 'session_time', 'max_players'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                return;
            }
        }
        
        // Validate max players
        $maxPlayers = (int)$_POST['max_players'];
        if ($maxPlayers < 1 || $maxPlayers > 50) {
            echo json_encode(['success' => false, 'message' => 'Max players must be between 1 and 50']);
            return;
        }
        
        // Validate date (must be today or future)
        $sessionDate = $_POST['session_date'];
        if (strtotime($sessionDate) < strtotime(date('Y-m-d'))) {
            echo json_encode(['success' => false, 'message' => 'Session date cannot be in the past']);
            return;
        }
        
        // Get current user ID
        $currentUserId = getCurrentUserId();
        if (!$currentUserId) {
            echo json_encode(['success' => false, 'message' => 'Unable to identify current user']);
            return;
        }
        
        // Prepare session data
        $sessionData = [
            'session_name' => trim($_POST['session_name']),
            'session_date' => $sessionDate,
            'session_time' => $_POST['session_time'],
            'game_mode' => $_POST['game_mode'] ?? 'Solo',
            'max_players' => $maxPlayers,
            'created_by' => $currentUserId,
            'notes' => trim($_POST['notes'] ?? ''),
            'lanes_count' => $_POST['lanes_count'] ?? 8,
            'players_per_lane' => $_POST['players_per_lane'] ?? 4,
            'lane_selection_open' => $_POST['lane_selection_open'] ?? 1,
            'assignment_locked' => $_POST['assignment_locked'] ?? 0,
            'available_lanes' => !empty($_POST['available_lanes']) ? json_encode(array_map('intval', explode(',', $_POST['available_lanes']))) : null
        ];
        
        // Create the session
        $sessionId = createGameSession($sessionData);
        
        // Clear any unexpected output and send clean JSON
        ob_clean();
        
        if ($sessionId) {
            echo json_encode([
                'success' => true, 
                'message' => 'Session created successfully',
                'session_id' => $sessionId,
                'redirect_url' => "select-participants.php?session_id=$sessionId"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create session']);
        }
        
    } catch (Exception $e) {
        ob_clean();
        error_log("Session creation error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while creating the session']);
    }
}

/**
 * Create session with participants in one go (for API/bulk operations)
 */
function createSessionWithParticipantsHandler() {
    try {
        // Validate required fields
        $required = ['session_name', 'session_date', 'session_time', 'max_players'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                return;
            }
        }
        
        // Get participant IDs
        $participantIds = [];
        if (!empty($_POST['participant_ids'])) {
            $participantIds = json_decode($_POST['participant_ids'], true);
            if (!is_array($participantIds)) {
                echo json_encode(['success' => false, 'message' => 'Invalid participant IDs format']);
                return;
            }
        }
        
        // Validate participant count
        $maxPlayers = (int)$_POST['max_players'];
        if (count($participantIds) > $maxPlayers) {
            echo json_encode(['success' => false, 'message' => 'Too many participants selected']);
            return;
        }
        
        // Get current user ID
        $currentUserId = getCurrentUserId();
        if (!$currentUserId) {
            echo json_encode(['success' => false, 'message' => 'Unable to identify current user']);
            return;
        }
        
        // Prepare session data
        $sessionData = [
            'session_name' => trim($_POST['session_name']),
            'session_date' => $_POST['session_date'],
            'session_time' => $_POST['session_time'],
            'game_mode' => $_POST['game_mode'] ?? 'Solo',
            'max_players' => $maxPlayers,
            'created_by' => $currentUserId,
            'notes' => trim($_POST['notes'] ?? '')
        ];
        
        // Create session with participants
        $sessionId = createSessionWithParticipants($sessionData, $participantIds);
        
        if ($sessionId) {
            echo json_encode([
                'success' => true, 
                'message' => 'Session created with participants successfully',
                'session_id' => $sessionId
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create session with participants']);
        }
        
    } catch (Exception $e) {
        error_log("Session with participants creation error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while creating the session']);
    }
}

/**
 * Get current user ID from session
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}
?>
