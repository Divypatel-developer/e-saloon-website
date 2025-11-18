
<?php
require_once 'config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location:login.php');
    exit;
}

$page_title = "Admin Dashboard";
require_once 'header.php';

// Get counts for dashboard
$stmt = $pdo->query("SELECT COUNT(*) FROM customers");
$customer_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM services WHERE is_active = TRUE");
$service_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
$pending_appointments = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'confirmed'");
$confirmed_appointments = $stmt->fetchColumn();

// Get recent appointments
$stmt = $pdo->query("SELECT a.*, c.first_name, c.last_name, s.name as service_name 
                    FROM appointments a
                    JOIN customers c ON a.customer_id = c.customer_id
                    JOIN services s ON a.service_id = s.service_id
                    ORDER BY a.created_at DESC LIMIT 5");
$recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    <div class="text-muted">Welcome, <?php echo $_SESSION['admin_name']; ?></div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Customers</h5>
                <p class="h2"><?php echo $customer_count; ?></p>
                <a href="customers.php" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Services</h5>
                <p class="h2"><?php echo $service_count; ?></p>
                <a href="services.php" class="text-white">Manage</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Pending</h5>
                <p class="h2"><?php echo $pending_appointments; ?></p>
                <a href="bookings.php?status=pending" class="text-dark">View</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Confirmed</h5>
                <p class="h2"><?php echo $confirmed_appointments; ?></p>
                <a href="bookings.php?status=confirmed" class="text-white">View</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Recent Appointments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_appointments)): ?>
                <p class="text-muted">No recent appointments found</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                <td><?php echo date('d M, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch ($appointment['status']) {
                                            case 'confirmed': echo 'bg-success'; break;
                                            case 'pending': echo 'bg-warning text-dark'; break;
                                            case 'completed': echo 'bg-info'; break;
                                            case 'cancelled': echo 'bg-danger'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="bookings.php" class="btn btn-sm btn-primary">View All Appointments</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="services.php?action=add" class="btn btn-primary">Add New Service</a>
                    <a href="bookings.php?action=add" class="btn btn-secondary">Create Booking</a>
                    <a href="customers.php" class="btn btn-info">Manage Customers</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// dashboard.php
require_once 'header.php';
?>

<h1>Dashboard</h1>
<p>Welcome to your admin dashboard.</p>

<?php
require_once 'footer.php';
?>

<?php require_once 'footer.php'; ?>  