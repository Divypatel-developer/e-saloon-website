<?php
require_once 'config.php';

// Authentication check
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Fetch customers from database
try {
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching customers: " . $e->getMessage());
}

$page_title = "Manage Customers";
require_once 'header.php';
?>

<div class="container">
    <h2>Customer Management</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                <td><?= htmlspecialchars($customer['first_name']) ?></td>
                <td><?= htmlspecialchars($customer['last_name']) ?></td>
                <td><?= htmlspecialchars($customer['email']) ?></td>
                <td><?= htmlspecialchars($customer['phone']) ?></td>
                <td><?= htmlspecialchars($customer['address'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($customer['created_at']) ?></td>
                <td>
                    <a href="view_customer.php?id=<?= $customer['customer_id'] ?>" class="btn btn-info btn-sm">View</a>
                    <a href="edit_customer.php?id=<?= $customer['customer_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="delete_customer.php?id=<?= $customer['customer_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>