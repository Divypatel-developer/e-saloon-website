<?php
require_once 'config.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Display messages
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}

// Fetch all customers
try {
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching customers: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Customers</h2>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                        <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                        <td><?= date('M j, Y', strtotime($customer['created_at'])) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="edit_customer.php?id=<?= $customer['customer_id'] ?>" 
                                   class="btn btn-primary" title="Edit">
                                   <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="delete_customer.php?id=<?= $customer['customer_id'] ?>" 
                                   class="btn btn-danger" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this customer?')">
                                   <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>