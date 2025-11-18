<?php
require_once 'config.php';

// Admin authentication check

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Validate customer ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_customers.php?error=Invalid customer ID');
    exit;
}

$customer_id = $_GET['id'];

// Fetch customer data
try {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        header('Location: manage_customers.php?error=Customer not found');
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching customer: " . $e->getMessage());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Validate inputs
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = "Phone number must be 10-15 digits";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE customers SET 
                                 first_name = ?, last_name = ?, email = ?, 
                                 phone = ?, address = ?
                                 WHERE customer_id = ?");
            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                $phone,
                $address,
                $customer_id
            ]);
            
            $_SESSION['message'] = "Customer updated successfully";
            header("Location: manage_customers.php");
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $errors[] = "Email already exists";
            } else {
                $errors[] = "Error updating customer: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Customer</h2>
        <a href="manage_customers.php" class="btn btn-secondary mb-3">← Back to Customers</a>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="border p-4 rounded">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name*</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           value="<?= htmlspecialchars($customer['first_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name*</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           value="<?= htmlspecialchars($customer['last_name']) ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email*</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($customer['email']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="<?= htmlspecialchars($customer['phone']) ?>"
                       placeholder="10-15 digits only">
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($customer['address']) ?></textarea>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>