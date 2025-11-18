<?php
require_once 'config.php';

// Authentication check
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get service ID
$id = $_GET['id'] ?? 0;

// Fetch service
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found!");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $duration = $_POST['duration'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle image upload
    $image = $service['image']; // keep old image if not replaced
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/services/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $image = $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE services 
            SET name = ?, description = ?, price = ?, duration = ?, image = ?, is_active = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $price, $duration, $image, $is_active, $id]);

        header("Location: services.php?success=Service updated successfully");
        exit;
    } catch (PDOException $e) {
        $error = "Error updating service: " . $e->getMessage();
    }
}

$page_title = "Edit Service";
require_once 'header.php';
?>

<div class="container">
    <h2>Edit Service</h2>
    <a href="services.php" class="btn btn-secondary mb-3">Back to Services</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Service Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($service['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($service['description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="price">Price ($)</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($service['price']) ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="duration">Duration (minutes)</label>
                <input type="number" class="form-control" id="duration" name="duration" min="1" value="<?= htmlspecialchars($service['duration']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Current Image</label><br>
            <?php if ($service['image']): ?>
                <img src="../uploads/services/<?= htmlspecialchars($service['image']) ?>" width="100"><br><br>
            <?php else: ?>
                <p>No image uploaded</p>
            <?php endif; ?>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?= $service['is_active'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Active Service</label>
        </div>

        <button type="submit" class="btn btn-primary">Update Service</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>
