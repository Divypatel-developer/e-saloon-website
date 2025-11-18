<?php
require_once 'config.php';

// Authentication and session start
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Update booking status
if (($action === 'confirm' || $action === 'cancel' || $action === 'complete') && $booking_id > 0) {
    $new_status = '';
    switch ($action) {
        case 'confirm': $new_status = 'confirmed'; break;
        case 'cancel': $new_status = 'cancelled'; break;
        case 'complete': $new_status = 'completed'; break;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        if ($stmt->execute([$new_status, $booking_id])) {
            $_SESSION['success_message'] = "Booking #$booking_id status updated to " . ucfirst($new_status);
        } else {
            $_SESSION['error_message'] = "Failed to update booking status";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
    header('Location: bookings.php');
    exit;
}

// Delete booking
if ($action === 'delete' && $booking_id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE appointment_id = ?");
        if ($stmt->execute([$booking_id])) {
            $_SESSION['success_message'] = "Booking #$booking_id deleted successfully";
        } else {
            $_SESSION['error_message'] = "Failed to delete booking";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
    header('Location: bookings.php');
    exit;
}

// Filter by status if specified
$where = "";
$params = [];
if (!empty($status) && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
    $where = "WHERE a.status = ?";
    $params = [$status];
}

// Fetch bookings with customer and service details
try {
    $query = "
        SELECT a.*, 
               c.first_name, c.last_name, c.email, c.phone, 
               s.name as service_name, s.price, s.duration
        FROM appointments a
        JOIN customers c ON a.customer_id = c.customer_id
        JOIN services s ON a.service_id = s.service_id
        $where
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>");
}

$page_title = "Manage Bookings";
require_once 'header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Bookings</h2>
        <div class="btn-group">
            <a href="bookings.php" class="btn btn-sm <?= empty($status) ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
            <a href="bookings.php?status=pending" class="btn btn-sm <?= $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">Pending</a>
            <a href="bookings.php?status=confirmed" class="btn btn-sm <?= $status === 'confirmed' ? 'btn-success' : 'btn-outline-success' ?>">Confirmed</a>
            <a href="bookings.php?status=completed" class="btn btn-sm <?= $status === 'completed' ? 'btn-info' : 'btn-outline-info' ?>">Completed</a>
            <a href="bookings.php?status=cancelled" class="btn btn-sm <?= $status === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' ?>">Cancelled</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($bookings)): ?>
                <div class="alert alert-info">No bookings found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= $booking['appointment_id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($booking['email']) ?></small><br>
                                    <small><?= htmlspecialchars($booking['phone']) ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($booking['service_name']) ?><br>
                                    <small>$<?= number_format($booking['price'], 2) ?></small><br>
                                    <small><?= $booking['duration'] ?> mins</small>
                                </td>
                                <td>
                                    <?= date('d M, Y', strtotime($booking['appointment_date'])) ?><br>
                                    <small><?= date('h:i A', strtotime($booking['appointment_time'])) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $booking['status'] === 'confirmed' ? 'success' : 
                                        ($booking['status'] === 'pending' ? 'warning text-dark' : 
                                        ($booking['status'] === 'completed' ? 'info' : 'danger')) 
                                    ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $booking['payment_status'] === 'paid' ? 'success' : 
                                        ($booking['payment_status'] === 'pending' ? 'warning text-dark' : 'danger') 
                                    ?>">
                                        <?= ucfirst($booking['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <a href="bookings.php?action=confirm&id=<?= $booking['appointment_id'] ?>" class="btn btn-success">Confirm</a>
                                            <a href="bookings.php?action=cancel&id=<?= $booking['appointment_id'] ?>" class="btn btn-danger">Cancel</a>
                                        <?php elseif ($booking['status'] === 'confirmed'): ?>
                                            <a href="bookings.php?action=complete&id=<?= $booking['appointment_id'] ?>" class="btn btn-info">Complete</a>
                                        <?php endif; ?>
                                        <a href="view_booking.php?id=<?= $booking['appointment_id'] ?>" class="btn btn-primary">View</a>
                                        <a href="bookings.php?action=delete&id=<?= $booking['appointment_id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Delete booking #<?= $booking['appointment_id'] ?>?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>