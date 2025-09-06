<?php
/**
 * Maintenance Bypass Utility
 * Shows popup for admin users, full maintenance for others
 */

function checkMaintenanceBypass($pageName, $pageTitle, $originalPage = null) {
    session_start();
    
    // If user is admin, show popup but allow access
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
        // Set flag to show admin popup
        $_SESSION['show_admin_popup'] = true;
        $_SESSION['admin_popup_message'] = "Sorry doh taufiq belum siap part ni";
        return true; // Admin can proceed
    }
    
    // Non-admin users get redirected to maintenance
    $originalPage = $originalPage ?? $_SERVER['PHP_SELF'];
    $redirectUrl = "maintenance-flexible.php?page={$pageName}&title=" . urlencode($pageTitle) . "&original=" . urlencode($originalPage);
    
    header('Location: ' . $redirectUrl);
    exit;
}

/**
 * Quick maintenance check for pages
 * Usage: requireMaintenanceBypass('doubles', 'Doubles Score Table');
 */
function requireMaintenanceBypass($pageName, $pageTitle) {
    checkMaintenanceBypass($pageName, $pageTitle, $_SERVER['PHP_SELF']);
}

/**
 * Check if admin popup should be shown
 */
function shouldShowAdminPopup() {
    return isset($_SESSION['show_admin_popup']) && $_SESSION['show_admin_popup'] === true;
}

/**
 * Clear admin popup flag
 */
function clearAdminPopup() {
    unset($_SESSION['show_admin_popup']);
    unset($_SESSION['admin_popup_message']);
}
?>
