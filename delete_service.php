<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
$stmt->execute([$id]);

header("Location: services.php?success=Service deleted successfully");
exit;
