<?php
require_once 'config.php';

// Authentication check
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: customers.php');
    exit;
}

$customer_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        header('Location: customers.php?error=Customer not found');
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching customer: " . $e->getMessage());
}

$page_title = "Customer Details";
require_once 'header.php';
?>

<div class="container">
    <h2>Customer Details</h2>
    <a href="customers.php" class="btn btn-secondary mb-3">Back to Customers</a>
    
    <div class="card">
        <div class="card-header">
            <h4><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Customer ID:</strong> <?= htmlspecialchars($customer['customer_id']) ?></p>
                    <p><strong>First Name:</strong> <?= htmlspecialchars($customer['first_name']) ?></p>
                    <p><strong>Last Name:</strong> <?= htmlspecialchars($customer['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($customer['address'] ?? 'N/A') ?></p>
                    <p><strong>Registered At:</strong> <?= htmlspecialchars($customer['created_at']) ?></p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="edit_customer.php?id=<?= $customer['customer_id'] ?>" class="btn btn-primary">Edit Customer</a>
            <a href="delete_customer.php?id=<?= $customer['customer_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete Customer</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>