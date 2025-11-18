<?php
// Include configuration first
require_once 'config.php';

// Now check authentication
checkAdminLogin();

// Initialize variables
$delete_success = false;
$delete_error = false;
$customer_id = "";
$search_term = "";

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_customer'])) {
    $customer_id = trim($_POST['customer_id']);
    
    // Validate customer ID
    if (!empty($customer_id) && is_numeric($customer_id)) {
        // Check if customer exists
        $check_sql = "SELECT * FROM customers WHERE customer_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $customer_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Customer exists, proceed with deletion
            $sql = "DELETE FROM customers WHERE customer_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $customer_id);
            
            if ($stmt->execute()) {
                $delete_success = true;
            } else {
                $delete_error = true;
            }
            $stmt->close();
        } else {
            $delete_error = true;
        }
        $check_stmt->close();
    } else {
        $delete_error = true;
    }
}

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_term = trim($_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Customer - E-Saloon Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        }
        .container {
            max-width: 1200px;
        }
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 1rem;
            margin-bottom: 2rem;
        }
        .card-header {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
        }
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(231, 76, 60, 0.3);
        }
        .btn-back {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(108, 92, 231, 0.1);
        }
        .search-box {
            position: relative;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6c757d;
        }
        .search-box input {
            padding-left: 40px;
        }
        .alert {
            border-radius: 0.75rem;
            border: none;
        }
        .alert-success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }
        .alert-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        .pagination .page-link {
            color: #6c5ce7;
        }
        .pagination .page-item.active .page-link {
            background-color: #6c5ce7;
            border-color: #6c5ce7;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-cut me-2"></i>E-Saloon Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="delete_customer.php"><i class="fas fa-trash-alt me-1"></i> Delete Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-list me-1"></i> View Customers</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        Welcome, <strong><?php echo $_SESSION['admin_username']; ?></strong>
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-3">
        <div class="row mb-4">
            <div class="col">
                <h1 class="text-center display-5 fw-bold text-primary">
                    <i class="fas fa-users-cog me-2"></i>Customer Management
                </h1>
                <p class="text-center lead">Admin panel for managing customer records</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title mb-0"><i class="fas fa-trash-alt me-2"></i>Delete Customer</h5>
            </div>
            <div class="card-body">
                <?php
                // Display success/error messages
                if ($delete_success) {
                    echo "<div class='alert alert-success'><i class='fas fa-check-circle me-2'></i>Customer deleted successfully!</div>";
                } elseif ($delete_error) {
                    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle me-2'></i>Error deleting customer. Please check the customer ID and try again.</div>";
                }
                ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="p-4 border rounded bg-light">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label fw-bold">Customer ID to Delete</label>
                                <input type="number" class="form-control" id="customer_id" name="customer_id" required min="1" placeholder="Enter customer ID">
                            </div>
                            <button type="submit" name="delete_customer" class="btn btn-delete w-100 py-2 fw-bold">
                                <i class="fas fa-trash-alt me-2"></i>Delete Customer
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded bg-light">
                            <h5 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Deletion Warning</h5>
                            <p class="text-danger">Deleting a customer is a permanent action and cannot be undone.</p>
                            <p>All customer data including personal information and history will be permanently removed from the database.</p>
                            <p>Please double-check the Customer ID before proceeding with deletion.</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row mb-4">
                    <div class="col">
                        <h5 class="fw-bold mb-3"><i class="fas fa-list me-2"></i>Current Customers</h5>
                        
                        <!-- Search Form -->
                        <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-4">
                            <div class="input-group search-box">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search customers by name or email..." value="<?php echo htmlspecialchars($search_term); ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </form>
                        
                        <!-- Customers Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Build query for customers list
                                    $sql = "SELECT customer_id, first_name, last_name, email, phone, created_at FROM customers";
                                    
                                    if (!empty($search_term)) {
                                        $sql .= " WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
                                    }
                                    
                                    $sql .= " ORDER BY created_at DESC LIMIT 10";
                                    
                                    $stmt = $conn->prepare($sql);
                                    
                                    if (!empty($search_term)) {
                                        $search_param = "%$search_term%";
                                        $stmt->bind_param("sss", $search_param, $search_param, $search_param);
                                    }
                                    
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row["customer_id"] . "</td>";
                                            echo "<td>" . htmlspecialchars($row["first_name"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["last_name"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                            echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                                            echo "<td>" . $row["created_at"] . "</td>";
                                            echo "<td>
                                                    <form method='POST' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' class='d-inline'>
                                                        <input type='hidden' name='customer_id' value='" . $row["customer_id"] . "'>
                                                        <button type='submit' name='delete_customer' class='btn btn-sm btn-delete' onclick=\"return confirm('Are you sure you want to delete customer #" . $row["customer_id"] . "?')\">
                                                            <i class='fas fa-trash-alt'></i>
                                                        </button>
                                                    </form>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center py-4'>No customers found</td></tr>";
                                    }
                                    $stmt->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="admin_dashboard.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <span class="text-muted">E-Saloon Management System v1.0</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>