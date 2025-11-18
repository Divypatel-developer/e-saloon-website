<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $email = $_SESSION['email'];

    $update = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expiry=NULL WHERE email=?");
    $update->bind_param("ss", $new_password, $email);

    if ($update->execute()) {
        echo "Password reset successful! <a href='login.php'>Login</a>";
    } else {
        echo "Failed to reset password.";
    }
}
?>
