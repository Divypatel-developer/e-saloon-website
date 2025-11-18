<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$page_title = "Manage Customers";
require_once 'header.php';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete Customer
if ($action === 'delete' && $customer_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $_SESSION['success_message'] = "Customer deleted successfully";
    header('Location: customers.php');
    exit;
}

// Fetch all customers
$stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Customers</h2>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success">
    <?php echo $_SESSION['success_message']; ?>
    <?php unset($_SESSION['success_message']); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($customers)): ?>
        <p class="text-muted">No customers found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
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
                        <td><?php echo $customer['customer_id']; ?></td>
                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . htmlspecialchars($customer['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                        <td><?php echo date('d M, Y', strtotime($customer['created_at'])); ?></td>
                        <td>
                            <a href="customer_details.php?id=<?php echo $customer['customer_id']; ?>" class="btn btn-sm btn-info">View</a>
                            <a href="customers.php?action=delete&id=<?php echo $customer['customer_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>