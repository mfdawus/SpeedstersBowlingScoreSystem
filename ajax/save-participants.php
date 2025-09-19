<?php
// Dedicated endpoint for saving participants
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

try {
    error_log("Save participants - POST data: " . json_encode($_POST));
    
    $sessionId = $_POST['session_id'] ?? null;
    $participantIds = json_decode($_POST['participant_ids'] ?? '[]', true);
    
    error_log("Session ID: $sessionId");
    error_log("Participant IDs: " . json_encode($participantIds));
    
    if (!$sessionId) {
        echo json_encode(['success' => false, 'message' => 'Session ID is required']);
        exit();
    }
    
    if (empty($participantIds)) {
        echo json_encode(['success' => false, 'message' => 'No participants selected']);
        exit();
    }
    
    // Get database connection
    $pdo = getDBConnection();
    
    // Check if session exists
    $sessionStmt = $pdo->prepare("SELECT max_players FROM game_sessions WHERE session_id = ?");
    $sessionStmt->execute([$sessionId]);
    $session = $sessionStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        exit();
    }
    
    if (count($participantIds) > $session['max_players']) {
        echo json_encode(['success' => false, 'message' => 'Too many participants selected']);
        exit();
    }
    
    // Clear existing participants first
    $clearStmt = $pdo->prepare("DELETE FROM session_participants WHERE session_id = ?");
    $clearResult = $clearStmt->execute([$sessionId]);
    error_log("Clear existing participants: " . ($clearResult ? 'success' : 'failed'));
    
    // Add new participants
    $stmt = $pdo->prepare("INSERT INTO session_participants (session_id, user_id, joined_at) VALUES (?, ?, NOW())");
    
    $successCount = 0;
    foreach ($participantIds as $userId) {
        $result = $stmt->execute([$sessionId, (int)$userId]);
        if ($result) {
            $successCount++;
        }
        error_log("Added participant $userId: " . ($result ? 'success' : 'failed'));
    }
    
    if ($successCount === count($participantIds)) {
        echo json_encode([
            'success' => true, 
            'message' => "Successfully saved $successCount participants"
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => "Only saved $successCount of " . count($participantIds) . " participants"
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error saving participants: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
