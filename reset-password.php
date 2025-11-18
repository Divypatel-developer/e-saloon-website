<?php
session_start();
require_once 'db.php';
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['email']) || !isset($_SESSION['verified'])) {
    header("Location: forgot-password.php");
    exit;
}

$email = $_SESSION['email'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm)) {
        $error = "Both password fields are required";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE customers SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
        $stmt->execute([$hash, $email]);

        unset($_SESSION['email'], $_SESSION['verified']);
        $success = "Password reset successful. <a href='login.php'>Login here</a>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
</head>
<body>
<h2>Reset Password</h2>
<?php if($error) echo "<div style='color:red'>$error</div>"; ?>
<?php if($success) echo "<div style='color:green'>$success</div>"; ?>
<?php if(!$success): ?>
<form method="post">
<input type="password" name="password" placeholder="New Password" required><br><br>
<input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>
<button type="submit">Reset Password</button>
</form>
<?php endif; ?>
</body>
</html>
