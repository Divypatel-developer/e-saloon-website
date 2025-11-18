<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Save OTP in DB
        $update = $pdo->prepare("UPDATE customers SET otp = ?, otp_expiry = ? WHERE email = ?");
        $update->execute([$otp, $expiry, $email]);

        // Instead of sending email, display OTP for testing
        $_SESSION['email'] = $email;
        echo "✅ OTP generated successfully.<br>";
        echo "Your OTP is: <b>$otp</b><br>";
        echo "<a href='verify_otp.php'>Verify OTP</a>";
    } else {
        echo "❌ Email not found!";
    }
}
?>
