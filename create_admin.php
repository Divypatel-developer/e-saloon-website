<?php
require_once 'config.php';

// Only allow this in development
if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '::1') {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $full_name = trim($_POST['full_name']);
    
    if (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO admins (username, password, full_name) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $full_name]);
            echo "Admin account created successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<h2>Create Admin Account</h2>
<form method="post">
    <div>
        <label>Username:</label>
        <input type="text" name="username" required>
    </div>
    <div>
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>Full Name:</label>
        <input type="text" name="full_name" required>
    </div>
    <button type="submit">Create Admin</button>
</form>