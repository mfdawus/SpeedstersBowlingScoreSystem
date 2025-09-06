<?php
session_start();
require_once __DIR__ . '/../database.php';

// Login function
function login($username, $password) {
    try {
        $pdo = getDBConnection();
        
        // Prepare SQL statement
        $stmt = $pdo->prepare("SELECT user_id, username, password, first_name, last_name, user_role, team_name FROM users WHERE username = ? AND status = 'Active'");
        $stmt->execute([$username]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['team_name'] = $user['team_name'];
            $_SESSION['logged_in'] = true;
            
            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => $user
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid username or password!'
            ];
        }
    } catch(PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

// Logout function
function logout() {
    session_destroy();
    return [
        'success' => true,
        'message' => 'Logged out successfully!'
    ];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../authentication-login.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../dashboard.php');
        exit();
    }
}

// Get current user info
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'first_name' => $_SESSION['first_name'],
            'last_name' => $_SESSION['last_name'],
            'user_role' => $_SESSION['user_role'],
            'team_name' => $_SESSION['team_name']
        ];
    }
    return null;
}
?>
