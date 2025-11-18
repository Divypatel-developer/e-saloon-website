<?php
require_once 'config.php';
session_start();

// check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Handle update request
if (isset($_POST['update_status'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['payment_status'];

    $stmt = $pdo->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
    $stmt->execute([$status, $payment_id]);

    // if payment successful -> update appointment also
    if ($status === "completed") {
        $stmt = $pdo->prepare("UPDATE appointments SET payment_status = 'paid' 
                               WHERE appointment_id = (SELECT appointment_id FROM payments WHERE payment_id = ?)");
        $stmt->execute([$payment_id]);
    }
}

// Fetch all payments
$stmt = $pdo->query("SELECT p.*, a.customer_name, a.service, a.appointment_date 
                     FROM payments p 
                     JOIN appointments a ON p.appointment_id = a.appointment_id
                     ORDER BY p.payment_date DESC");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Payments</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin:20px 0; }
        th, td { border:1px solid #ddd; padding:8px; text-align:center; }
        th { background:#333; color:#fff; }
        form { margin:0; }
    </style>
</head>
<body>
    <h2>💳 Manage Payments</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Date</th>
            <th>Transaction ID</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($payments as $pay): ?>
        <tr>
            <td><?= $pay['payment_id'] ?></td>
            <td><?= htmlspecialchars($pay['customer_name']) ?></td>
            <td><?= htmlspecialchars($pay['service']) ?></td>
            <td><?= $pay['appointment_date'] ?></td>
            <td><?= htmlspecialchars($pay['transaction_id']) ?></td>
            <td><?= ucfirst($pay['payment_status']) ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="payment_id" value="<?= $pay['payment_id'] ?>">
                    <select name="payment_status">
                        <option value="pending" <?= $pay['payment_status']=='pending'?'selected':'' ?>>Pending</option>
                        <option value="completed" <?= $pay['payment_status']=='completed'?'selected':'' ?>>Completed</option>
                        <option value="failed" <?= $pay['payment_status']=='failed'?'selected':'' ?>>Failed</option>
                    </select>
                    <button type="submit" name="update_status">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
