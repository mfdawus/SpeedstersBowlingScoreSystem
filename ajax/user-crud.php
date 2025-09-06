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
    
    require_once __DIR__ . '/../includes/user-management.php';

    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'view':
            $userId = $_POST['user_id'] ?? 0;
            if ($userId) {
                $user = getUserById($userId);
                if ($user && !isset($user['error'])) {
                    $user['recent_games'] = getUserRecentGames($userId, 5);
                    $response = ['success' => true, 'data' => $user];
                } else {
                    $response['message'] = 'User not found';
                }
            } else {
                $response['message'] = 'Invalid user ID';
            }
            break;
            
        case 'update':
            $userId = $_POST['user_id'] ?? 0;
            if ($userId) {
                $data = [
                    'username' => $_POST['username'] ?? '',
                    'first_name' => $_POST['first_name'] ?? '',
                    'last_name' => $_POST['last_name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'skill_level' => $_POST['skill_level'] ?? '',
                    'status' => $_POST['status'] ?? '',
                    'team_name' => $_POST['team_name'] ?? ''
                ];
                
                if (updateUser($userId, $data)) {
                    $response = ['success' => true, 'message' => 'User updated successfully'];
                } else {
                    $response['message'] = 'Failed to update user';
                }
            } else {
                $response['message'] = 'Invalid user ID';
            }
            break;
            
        case 'delete':
            $userId = $_POST['user_id'] ?? 0;
            if ($userId) {
                if (deleteUser($userId)) {
                    $response = ['success' => true, 'message' => 'User deleted successfully'];
                } else {
                    $response['message'] = 'Failed to delete user';
                }
            } else {
                $response['message'] = 'Invalid user ID';
            }
            break;
            
        case 'create':
            $data = [
                'username' => $_POST['username'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'skill_level' => $_POST['skill_level'] ?? '',
                'user_role' => $_POST['user_role'] ?? 'Player',
                'status' => $_POST['status'] ?? 'Active',
                'team_name' => $_POST['team_name'] ?? '',
                'password' => $_POST['password'] ?? 'password123'
            ];
            
            $userId = createUser($data);
            if ($userId) {
                $response = ['success' => true, 'message' => 'User created successfully', 'user_id' => $userId];
            } else {
                $response['message'] = 'Failed to create user';
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
