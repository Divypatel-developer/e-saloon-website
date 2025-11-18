<?php
require_once 'config.php';
$page_title = "My Bookings";
require_once 'header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Debug: Show user ID
echo "<!-- Debug: User ID = " . $_SESSION['user_id'] . " -->";

// Modified query with better error handling
try {
    $stmt = $pdo->prepare("SELECT 
        a.appointment_id, 
        a.appointment_date, 
        a.appointment_time, 
        a.status, 
        a.notes,
        s.service_id,
        s.name as service_name, 
        s.price as service_price, 
        p.payment_method, 
        p.payment_status, 
        p.transaction_id
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    LEFT JOIN payments p ON a.appointment_id = p.appointment_id
    WHERE a.customer_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Show number of appointments found
    echo "<!-- Debug: " . count($appointments) . " appointments found -->";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle cancellation
if (isset($_GET['cancel'])) {
    $appointment_id = (int)$_GET['cancel'];
    
    try {
        // Verify the appointment belongs to the user
        $stmt = $pdo->prepare("SELECT * FROM appointments 
                              WHERE appointment_id = ? AND customer_id = ?");
        $stmt->execute([$appointment_id, $_SESSION['user_id']]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($appointment) {
            // Update appointment status
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' 
                                  WHERE appointment_id = ?");
            $stmt->execute([$appointment_id]);
            
            // Update payment status if exists
            $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'refunded' 
                                  WHERE appointment_id = ? AND payment_status = 'completed'");
            $stmt->execute([$appointment_id]);
            
            $_SESSION['success_message'] = "Appointment cancelled successfully";
            header('Location: my_booking.php');
            exit;
        }
    } catch (PDOException $e) {
        die("Cancellation error: " . $e->getMessage());
    }
}
?>

<!-- Rest of your HTML remains the same -->
<h2 class="mb-4">My Bookings</h2>

<?php if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?php echo $_SESSION['success_message']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <?php unset($_SESSION['success_message']); ?>
</div>
<?php endif; ?>

<?php if (empty($appointments)): ?>
<div class="alert alert-info">
    You don't have any bookings yet. <a href="booking.php" class="alert-link">Book a service now</a>.
</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>Service</th>
                <th>Date & Time</th>
                <th>Price</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($appointment['service_name']); ?></strong>
                    <?php if (!empty($appointment['notes'])): ?>
                    <div class="text-muted small mt-1"><?php echo htmlspecialchars($appointment['notes']); ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo date('d M, Y', strtotime($appointment['appointment_date'])); ?><br>
                    <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                </td>
                <td>₹<?php echo number_format($appointment['service_price'], 2); ?></td>
                <td>
                    <span class="badge rounded-pill 
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
                <td>
                    <?php if (!empty($appointment['payment_status']) && $appointment['payment_status'] === 'paid'): ?>
                        <span class="badge bg-success">Paid</span>
                        <div class="text-muted small mt-1">
                            <?php echo !empty($appointment['payment_method']) ? ucfirst($appointment['payment_method']) : ''; ?>
                            <?php if (!empty($appointment['transaction_id'])): ?>
                                <div>Ref: <?php echo $appointment['transaction_id']; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php elseif (!empty($appointment['payment_status']) && $appointment['payment_status'] === 'failed'): ?>
                        <span class="badge bg-danger">Failed</span>
                        <a href="payment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-success mt-1">Retry</a>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                        <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                            <?php if (!empty($appointment['payment_method']) && $appointment['payment_method'] === 'cash'): ?>
                                <div class="text-muted small">Pay at service</div>
                            <?php else: ?>
                                <a href="payment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-success mt-1">Pay Now</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        
                        <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                            <a href="my_booking.php?cancel=<?php echo $appointment['appointment_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Cancel Booking"
                               onclick="return confirm('Are you sure you want to cancel this booking?')">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>