<?php
require_once 'includes/auth.php';

// Logout the user
logout();

// Redirect to landing page
header('Location: index.php');
exit();
?>
