<?php
// logout.php
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>