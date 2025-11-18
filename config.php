<?php
session_start();
require_once 'db.php';

// At the top after session_start()
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Base URL configuration
define('BASE_URL', 'http://localhost/e-saloon/');

// Default timezone
date_default_timezone_set('Asia/Kolkata');

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Database configuration
$servername = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password (empty)
$dbname = "e_saloon"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper encoding
$conn->set_charset("utf8mb4");

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (for protected pages)
function checkAdminLogin() {
    if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
        header("Location: admin_login.php");
        exit;
    }
}

?>
