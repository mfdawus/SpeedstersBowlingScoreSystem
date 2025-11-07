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
        case 'change_password':
            $userId = $_POST['user_id'] ?? 0;
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!$userId) {
                $response['message'] = 'User ID is required';
                break;
            }
            
            if (empty($newPassword)) {
                $response['message'] = 'New password is required';
                break;
            }
            
            if ($newPassword !== $confirmPassword) {
                $response['message'] = 'Passwords do not match';
                break;
            }
            
            if (strlen($newPassword) < 6) {
                $response['message'] = 'Password must be at least 6 characters long';
                break;
            }
            
            try {
                $pdo = getDBConnection();
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $result = $stmt->execute([$hashedPassword, $userId]);
                
                if ($result) {
                    $response = ['success' => true, 'message' => 'Password changed successfully'];
                } else {
                    $response['message'] = 'Failed to change password';
                }
            } catch (Exception $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
            break;
            
        case 'view':
            $userId = $_POST['user_id'] ?? 0;
            error_log("View user request - User ID: " . $userId);
            
            if ($userId) {
                try {
                    $user = getUserById($userId);
                    error_log("getUserById result: " . json_encode($user));
                    
                    if ($user && !isset($user['error'])) {
                        try {
                            $recentGames = getUserRecentGames($userId, 5);
                            $user['recent_games'] = $recentGames;
                            
                            // Debug logging
                            error_log("User ID: " . $userId);
                            error_log("Recent games count: " . count($recentGames));
                            error_log("Recent games data: " . json_encode($recentGames));
                        } catch (Exception $e) {
                            error_log("Error getting recent games: " . $e->getMessage());
                            $user['recent_games'] = []; // Empty array as fallback
                        }
                        
                        // Temporary: Check if any users have games
                        $checkAnyGames = $pdo->prepare("SELECT COUNT(*) as total FROM game_scores");
                        $checkAnyGames->execute();
                        $totalGames = $checkAnyGames->fetch(PDO::FETCH_ASSOC)['total'];
                        error_log("Total games in database: " . $totalGames);
                        
                        $checkUserGames = $pdo->prepare("SELECT COUNT(*) as user_games FROM game_scores WHERE user_id = ?");
                        $checkUserGames->execute([$userId]);
                        $userGames = $checkUserGames->fetch(PDO::FETCH_ASSOC)['user_games'];
                        error_log("Games for user " . $userId . ": " . $userGames);
                        
                        $response = ['success' => true, 'data' => $user];
                    } else {
                        $errorMsg = 'User not found';
                        if (isset($user['error'])) {
                            $errorMsg .= ': ' . $user['error'];
                        }
                        error_log("Error in view action: " . $errorMsg);
                        $response['message'] = $errorMsg;
                        $response['debug'] = ['user_id' => $userId, 'user_data' => $user];
                    }
                } catch (Exception $e) {
                    error_log("Exception in view action: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    $response['message'] = 'Error fetching user: ' . $e->getMessage();
                    $response['debug'] = [
                        'user_id' => $userId,
                        'exception' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ];
                }
            } else {
                $response['message'] = 'Invalid user ID: ' . $userId;
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
                
                // Handle profile picture upload if present
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    $fileType = $_FILES['profile_picture']['type'];
                    $fileSize = $_FILES['profile_picture']['size'];
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $response['message'] = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.';
                        break;
                    }
                    
                    if ($fileSize > $maxSize) {
                        $response['message'] = 'File size exceeds 5MB limit.';
                        break;
                    }
                    
                    // Generate unique filename
                    $extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                    $newFilename = 'user_' . $userId . '_' . time() . '.' . $extension;
                    $targetPath = $uploadDir . $newFilename;
                    
                    // Delete old profile picture if exists
                    $oldUser = getUserById($userId);
                    if ($oldUser && !empty($oldUser['profile_picture'])) {
                        $oldFile = $uploadDir . $oldUser['profile_picture'];
                        if (file_exists($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                    
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                        $data['profile_picture'] = $newFilename;
                    } else {
                        $response['message'] = 'Failed to upload profile picture.';
                        break;
                    }
                }
                
                if (updateUser($userId, $data)) {
                    $response = ['success' => true, 'message' => 'User updated successfully'];
                } else {
                    $response['message'] = 'Failed to update user - check database connection';
                }
            } else {
                $response['message'] = 'Invalid user ID: ' . $userId;
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
