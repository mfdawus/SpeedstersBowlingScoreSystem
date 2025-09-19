<?php
// Simple session creation without maintenance bypass
header('Content-Type: application/json');

// Start session safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple auth check
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include database functions
require_once '../database.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get the action
$action = $_POST['action'] ?? '';

if ($action === 'create_session_draft') {
    try {
        // Log the received data for debugging
        error_log("Session creation attempt - POST data: " . json_encode($_POST));
        
        // Validate required fields
        $required = ['session_name', 'session_date', 'session_time', 'max_players'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                error_log("Missing required field: $field");
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                exit();
            }
        }
        
        // Get current user ID
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            echo json_encode(['success' => false, 'message' => 'Unable to identify current user']);
            exit();
        }
        
        // Create session directly
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            INSERT INTO game_sessions (
                session_name, session_date, session_time, game_mode, 
                max_players, created_by, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            trim($_POST['session_name']),
            $_POST['session_date'],
            $_POST['session_time'],
            $_POST['game_mode'] ?? 'Solo',
            (int)$_POST['max_players'],
            $currentUserId,
            trim($_POST['notes'] ?? '')
        ]);
        
        if ($result) {
            $sessionId = $pdo->lastInsertId();
            error_log("Session created successfully with ID: $sessionId");
            echo json_encode([
                'success' => true, 
                'message' => 'Session created successfully',
                'session_id' => $sessionId,
                'redirect_url' => "select-participants.php?session_id=$sessionId"
            ]);
        } else {
            error_log("Failed to create session - SQL execution failed");
            echo json_encode(['success' => false, 'message' => 'Failed to create session']);
        }
        
    } catch (Exception $e) {
        error_log("Session creation error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
