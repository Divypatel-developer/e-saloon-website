
<?php
// header.php (or wherever your navigation is)
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></title>
    <style>
        /* Basic styling for the admin panel */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .admin-header {
            background-color: #343a40;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav {
            background-color: #2c3e50;
            padding: 10px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .admin-container {
            padding: 20px;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h2>Admin Panel</h2>
        <?php if (isset($_SESSION['admin_id'])): ?>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <form action="logout.php" method="post" style="display: inline-block; margin-left: 15px;">
                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </header>
    
    <nav class="admin-nav">
        <!-- Your navigation links here -->
        <a href="dashboard.php">Dashboard</a>
    </nav>
    
    <main class="admin-container">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Saloon - <?php echo $page_title ?? 'Your Online Beauty Destination'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">E-Saloon</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>services.php">Services</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>my_booking.php">My Bookings</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isAdminLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin Panel</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>my_booking.php">My Bookings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>register.php">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        