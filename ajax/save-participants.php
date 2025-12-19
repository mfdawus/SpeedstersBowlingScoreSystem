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
require_once '../includes/duo-helper.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    error_log("Save participants - POST data: " . json_encode($_POST));
    
    $sessionId = $_POST['session_id'] ?? null;
    $participantIds = json_decode($_POST['participant_ids'] ?? '[]', true);
    $pairingMode = $_POST['pairing_mode'] ?? 'auto';
    $groupAssignments = json_decode($_POST['group_assignments'] ?? '{}', true);
    $manualPairs = json_decode($_POST['manual_pairs'] ?? '[]', true);
    
    error_log("Session ID: $sessionId");
    error_log("Participant IDs: " . json_encode($participantIds));
    error_log("Pairing Mode: $pairingMode");
    
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
    
    // Check if session exists and is Doubles mode
    $sessionStmt = $pdo->prepare("SELECT max_players, game_mode FROM game_sessions WHERE session_id = ?");
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
    
    // Update pairing mode for Doubles sessions (only if column exists)
    if ($session['game_mode'] === 'Doubles' && in_array($pairingMode, ['auto', 'semi_auto', 'manual'])) {
        // Check if pairing_mode column exists
        $checkColumnStmt = $pdo->prepare("SHOW COLUMNS FROM game_sessions LIKE 'pairing_mode'");
        $checkColumnStmt->execute();
        $columnExists = $checkColumnStmt->fetch();
        
        if ($columnExists) {
            $updateModeStmt = $pdo->prepare("UPDATE game_sessions SET pairing_mode = ? WHERE session_id = ?");
            $updateModeStmt->execute([$pairingMode, $sessionId]);
            error_log("Updated pairing mode to: $pairingMode");
        } else {
            error_log("Warning: pairing_mode column does not exist. Please run migration-pairing-modes.sql");
        }
    }
    
    // Clear existing participants and duo teams (for manual mode)
    if ($pairingMode === 'manual') {
        // Delete existing duo teams for this session
        $clearDuosStmt = $pdo->prepare("DELETE FROM duo_teams WHERE session_id = ?");
        $clearDuosStmt->execute([$sessionId]);
        error_log("Cleared existing duo teams");
    }
    
    $clearStmt = $pdo->prepare("DELETE FROM session_participants WHERE session_id = ?");
    $clearResult = $clearStmt->execute([$sessionId]);
    error_log("Clear existing participants: " . ($clearResult ? 'success' : 'failed'));
    
    // Add new participants with group assignments (for semi-auto)
    $stmt = $pdo->prepare("INSERT INTO session_participants (session_id, user_id, group_assignment, joined_at) VALUES (?, ?, ?, NOW())");
    
    $successCount = 0;
    foreach ($participantIds as $userId) {
        $groupId = null;
        if ($pairingMode === 'semi_auto' && isset($groupAssignments[$userId])) {
            $groupId = $groupAssignments[$userId]; // 'A' or 'B'
        }
        
        $result = $stmt->execute([$sessionId, (int)$userId, $groupId]);
        if ($result) {
            $successCount++;
        }
        error_log("Added participant $userId (group: " . ($groupId ?? 'none') . "): " . ($result ? 'success' : 'failed'));
    }
    
    // Handle manual pairing - create duo_teams entries
    if ($pairingMode === 'manual' && !empty($manualPairs)) {
        $duoStmt = $pdo->prepare("
            INSERT INTO duo_teams (session_id, duo_name, player1_id, player2_id, player1_avg, player2_avg, status)
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')
        ");
        
        $duoCount = 0;
        foreach ($manualPairs as $pair) {
            // Calculate averages for both players
            $avg1 = calculatePlayerAverage($pair['player1_id'], 5);
            $avg2 = calculatePlayerAverage($pair['player2_id'], 5);
            
            $result = $duoStmt->execute([
                $sessionId,
                $pair['duo_name'] ?? 'Duo ' . ($duoCount + 1),
                (int)$pair['player1_id'],
                (int)$pair['player2_id'],
                $avg1,
                $avg2
            ]);
            
            if ($result) {
                $duoCount++;
            }
            error_log("Created manual duo: " . ($result ? 'success' : 'failed'));
        }
        
        error_log("Created $duoCount manual duos");
    }
    
    if ($successCount === count($participantIds)) {
        $message = "Successfully saved $successCount participants";
        if ($pairingMode === 'manual' && isset($duoCount)) {
            $message .= " and created $duoCount duo(s)";
        }
        
        echo json_encode([
            'success' => true, 
            'message' => $message
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
