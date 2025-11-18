<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid service ID</div>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM services WHERE is_active = TRUE AND service_id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo '<div class="alert alert-danger">Service not found</div>';
    exit;
}
?>

<div class="service-details-card bg-light p-3 rounded">
    <h5 class="text-primary mb-3"><?php echo htmlspecialchars($service['name']); ?></h5>
    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Price:</span>
        <span class="fw-bold">₹<?php echo number_format($service['price'], 2); ?></span>
    </div>
    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Duration:</span>
        <span class="fw-bold">
            <?php echo floor($service['duration'] / 60) . 'h ' . ($service['duration'] % 60) . 'm'; ?>
        </span>
    </div>
    <?php if (!empty($service['description'])): ?>
        <div class="mt-3">
            <p class="small text-muted mb-1">Description:</p>
            <p class="small"><?php echo htmlspecialchars($service['description']); ?></p>
        </div>
    <?php endif; ?>
</div>