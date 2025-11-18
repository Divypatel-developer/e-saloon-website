<?php
session_start();
require_once 'db.php';
date_default_timezone_set('Asia/Kolkata');

$page_title = "Forgot Password";
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Basic validation
    if (empty($email)) {
        $error = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        try {
            // Check if the email exists
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Email exists -> generate OTP and store in DB
                $otp = rand(100000, 999999);
                $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

                $update = $pdo->prepare("UPDATE customers SET otp = ?, otp_expiry = ? WHERE email = ?");
                $update->execute([$otp, $expiry, $email]);

                // Save email for verification step
                $_SESSION['email'] = $email;

                // For local testing we show the OTP. Replace with email sending in production.
                $message = "✅ OTP generated and saved. Your OTP is: <b>$otp</b> (expires in 15 minutes).";
                $message .= "<br><a href='verify_otp.php'>Verify OTP</a>";
            } else {
                // Email not found -> show explicit "not available" message
                $error = "Email not available";
            }
        } catch (PDOException $e) {
            // DB error
            $error = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($page_title); ?></title>
<style>
    body { font-family: Arial, sans-serif; background:#f5f5f5; display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .container { background:#fff; padding:36px; border-radius:10px; width:100%; max-width:450px; box-shadow:0 6px 18px rgba(0,0,0,0.08); text-align:center; }
    input[type="email"] { width:100%; padding:12px; margin:12px 0; border-radius:6px; border:1px solid #ccc; }
    button { width:100%; padding:12px; border:none; border-radius:6px; background:#4a90e2; color:#fff; cursor:pointer; }
    .message { background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:6px; margin:16px 0; }
    .error { background:#ffebee; color:#c62828; padding:12px; border-radius:6px; margin:16px 0; }
    a { color:#4a90e2; }
</style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="email" name="email" required placeholder="Enter your registered email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <button type="submit">Send OTP</button>
    </form>

    <p style="margin-top:14px;">Remembered your password? <a href="login.php">Login</a></p>
</div>
</body>
</html>
