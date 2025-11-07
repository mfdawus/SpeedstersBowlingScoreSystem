<?php
// Suppress output
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'filename' => ''];

try {
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../includes/auth.php';
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }
    
    $userId = $_SESSION['user_id'];
    
    // Check if file was uploaded
    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('No file uploaded');
    }
    
    $file = $_FILES['profile_picture'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    // Validate file size (max 5MB)
    $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $maxFileSize) {
        throw new Exception('File size must be less than 5MB');
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed');
    }
    
    // Validate image dimensions (optional - check if it's a valid image)
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('File is not a valid image');
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = 'user_' . $userId . '_' . time() . '.' . $extension;
    
    // Set upload directory
    $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
    $uploadPath = $uploadDir . $newFilename;
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Get old profile picture to delete later
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $oldPicture = $stmt->fetchColumn();
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Update database
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
    $result = $stmt->execute([$newFilename, $userId]);
    
    if (!$result) {
        // If database update fails, remove the uploaded file
        unlink($uploadPath);
        throw new Exception('Failed to update database');
    }
    
    // Delete old profile picture if it exists and is not the default
    if ($oldPicture && $oldPicture !== 'default-avatar.png' && $oldPicture !== $newFilename) {
        $oldPath = $uploadDir . $oldPicture;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }
    
    // Update session if profile picture is stored there
    if (isset($_SESSION['profile_picture'])) {
        $_SESSION['profile_picture'] = $newFilename;
    }
    
    $response = [
        'success' => true,
        'message' => 'Profile picture updated successfully',
        'filename' => $newFilename,
        'url' => '../uploads/profile_pictures/' . $newFilename
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Profile picture upload error: " . $e->getMessage());
}

ob_clean();
echo json_encode($response);
exit;
?>

