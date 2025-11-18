<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];

    $query = $conn->prepare("SELECT * FROM users WHERE email=? AND otp=? AND otp_expiry >= NOW()");
    $query->bind_param("ss", $email, $otp);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        echo "OTP Verified! <a href='reset_password.php'>Reset Password</a>";
    } else {
        echo "Invalid or expired OTP.";
    }
}
?>
