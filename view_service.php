<?php
require_once 'config.php';

// Authentication check
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: services.php');
    exit;
}

$service_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        header('Location: services.php?error=Service not found');
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching service: " . $e->getMessage());
}

$page_title = "Service Details";
require_once 'header.php';
?>

<div class="container">
    <h2>Service Details</h2>
    <a href="services.php" class="btn btn-secondary mb-3">Back to Services</a>
    
    <div class="card">
        <div class="card-header">
            <h4><?= htmlspecialchars($service['name']) ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Service ID:</strong> <?= htmlspecialchars($service['service_id']) ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($service['name']) ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($service['description']) ?></p>
                    <p><strong>Price:</strong> $<?= htmlspecialchars(number_format($service['price'], 2)) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Duration:</strong> <?= htmlspecialchars($service['duration']) ?> minutes</p>
                    <p><strong>Status:</strong> 
                        <span class="badge badge-<?= $service['is_active'] ? 'success' : 'danger' ?>">
                            <?= $service['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($service['created_at']) ?></p>
                    <p><strong>Updated At:</strong> <?= htmlspecialchars($service['updated_at']) ?></p>
                </div>
            </div>
            
            <?php if ($service['image']): ?>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Service Image</h5>
                    <img src="../uploads/services/<?= htmlspecialchars($service['image']) ?>" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <a href="edit_service.php?id=<?= $service['service_id'] ?>" class="btn btn-primary">Edit Service</a>
            <a href="delete_service.php?id=<?= $service['service_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete Service</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>