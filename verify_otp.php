<?php
session_start();
require_once 'db.php';
date_default_timezone_set('Asia/Kolkata');

$message = '';
$error = '';

if (!isset($_SESSION['email'])) {
    header("Location: forgot-password.php");
    exit;
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp)) {
        $error = "OTP is required";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? AND otp = ? AND otp_expiry > NOW()");
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['verified'] = true; // mark OTP verified
            header("Location: reset-password.php");
            exit;
        } else {
            $error = "Invalid or expired OTP";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
</head>
<body>
<h2>Verify OTP</h2>
<?php if($error) echo "<div style='color:red'>$error</div>"; ?>
<?php if($message) echo "<div style='color:green'>$message</div>"; ?>
<form method="post">
<input type="text" name="otp" placeholder="Enter OTP" required>
<button type="submit">Verify OTP</button>
</form>
</body>
</html>
