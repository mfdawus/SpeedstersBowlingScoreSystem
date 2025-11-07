<?php
/**
 * Profile Picture Helper Functions
 * Provides utility functions for managing user profile pictures
 */

/**
 * Get the full URL for a user's profile picture
 * @param string|null $filename The profile picture filename
 * @param bool $relative Return relative path instead of absolute
 * @param int|null $userId User ID for generating template avatar number
 * @return string The profile picture URL or template avatar
 */
function getProfilePictureUrl($filename = null, $relative = true, $userId = null) {
    $baseUrl = $relative ? './uploads/profile_pictures/' : '/uploads/profile_pictures/';
    
    // If filename is provided and file exists, use it
    if (!empty($filename)) {
        $filePath = __DIR__ . '/../uploads/profile_pictures/' . $filename;
        if (file_exists($filePath)) {
            return $baseUrl . $filename;
        }
    }
    
    // Use template avatar based on user ID (cycles through user-1.jpg to user-8.jpg)
    $avatarNumber = 1; // Default to user-1.jpg
    if ($userId !== null && is_numeric($userId)) {
        $avatarNumber = (($userId - 1) % 8) + 1;
    }
    
    $defaultAvatar = $relative ? './assets/images/profile/user-' . $avatarNumber . '.jpg' : '/assets/images/profile/user-' . $avatarNumber . '.jpg';
    return $defaultAvatar;
}

/**
 * Get profile picture for a specific user
 * @param int $userId The user ID
 * @return string The profile picture URL
 */
function getUserProfilePicture($userId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $filename = $stmt->fetchColumn();
        
        return getProfilePictureUrl($filename, true, $userId);
    } catch (PDOException $e) {
        error_log("Error getting user profile picture: " . $e->getMessage());
        return getProfilePictureUrl(null, true, $userId);
    }
}

/**
 * Delete a user's profile picture
 * @param int $userId The user ID
 * @return bool Success status
 */
function deleteUserProfilePicture($userId) {
    try {
        $pdo = getDBConnection();
        
        // Get current profile picture
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $filename = $stmt->fetchColumn();
        
        if ($filename && $filename !== 'default-avatar.png') {
            $filePath = __DIR__ . '/../uploads/profile_pictures/' . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // Update database to NULL or default
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = ?");
        return $stmt->execute([$userId]);
        
    } catch (PDOException $e) {
        error_log("Error deleting user profile picture: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate if file is a valid image
 * @param array $file The $_FILES array element
 * @return array ['valid' => bool, 'message' => string]
 */
function validateProfilePicture($file) {
    $result = ['valid' => false, 'message' => ''];
    
    // Check if file exists
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['message'] = 'No file uploaded';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'File upload error code: ' . $file['error'];
        return $result;
    }
    
    // Check file size (max 5MB)
    $maxFileSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxFileSize) {
        $result['message'] = 'File size must be less than 5MB';
        return $result;
    }
    
    // Check MIME type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $result['message'] = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed';
        return $result;
    }
    
    // Validate it's actually an image
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $result['message'] = 'File is not a valid image';
        return $result;
    }
    
    // Check dimensions (optional - prevent extremely large images)
    list($width, $height) = $imageInfo;
    $maxDimension = 4096; // 4K resolution max
    if ($width > $maxDimension || $height > $maxDimension) {
        $result['message'] = "Image dimensions too large. Max {$maxDimension}x{$maxDimension}px";
        return $result;
    }
    
    $result['valid'] = true;
    $result['message'] = 'Valid image';
    return $result;
}

/**
 * Get initials from user name for avatar placeholder
 * @param string $firstName
 * @param string $lastName
 * @return string Two-letter initials
 */
function getUserInitials($firstName, $lastName = '') {
    $initials = '';
    
    if (!empty($firstName)) {
        $initials .= strtoupper(substr($firstName, 0, 1));
    }
    
    if (!empty($lastName)) {
        $initials .= strtoupper(substr($lastName, 0, 1));
    } elseif (!empty($firstName) && strlen($firstName) > 1) {
        $initials .= strtoupper(substr($firstName, 1, 1));
    }
    
    return $initials ?: 'U';
}

/**
 * Resize image to standard dimensions for profile pictures
 * @param string $sourcePath Source image path
 * @param string $destPath Destination image path
 * @param int $maxWidth Maximum width
 * @param int $maxHeight Maximum height
 * @return bool Success status
 */
function resizeProfilePicture($sourcePath, $destPath, $maxWidth = 500, $maxHeight = 500) {
    try {
        list($origWidth, $origHeight, $type) = getimagesize($sourcePath);
        
        // Calculate new dimensions maintaining aspect ratio
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $newWidth = round($origWidth * $ratio);
        $newHeight = round($origHeight * $ratio);
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Load source image based on type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }
        
        // Resize
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        
        // Save based on type
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $destPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $destPath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $destPath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($newImage, $destPath, 90);
                break;
        }
        
        // Free memory
        imagedestroy($source);
        imagedestroy($newImage);
        
        return true;
    } catch (Exception $e) {
        error_log("Error resizing profile picture: " . $e->getMessage());
        return false;
    }
}
?>

