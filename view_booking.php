<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $pdo->prepare("
        SELECT a.*, 
               c.first_name, c.last_name, c.email, c.phone, c.address,
               s.name as service_name, s.price, s.duration, s.description as service_description
        FROM appointments a
        JOIN customers c ON a.customer_id = c.customer_id
        JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        $_SESSION['error_message'] = "Booking not found";
        header('Location: bookings.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$page_title = "View Booking #" . $booking['appointment_id'];
require_once 'header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Booking Details #<?= $booking['appointment_id'] ?></h2>
        <a href="bookings.php" class="btn btn-secondary">Back to Bookings</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <p><strong>Name:</strong> <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($booking['address']) ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Service Information</h5>
                    <p><strong>Service:</strong> <?= htmlspecialchars($booking['service_name']) ?></p>
                    <p><strong>Price:</strong> $<?= number_format($booking['price'], 2) ?></p>
                    <p><strong>Duration:</strong> <?= $booking['duration'] ?> minutes</p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($booking['service_description']) ?></p>
                </div>
            </div>
            
            <hr>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Booking Details</h5>
                    <p><strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking['appointment_date'])) ?></p>
                    <p><strong>Time:</strong> <?= date('h:i A', strtotime($booking['appointment_time'])) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?= 
                            $booking['status'] === 'confirmed' ? 'success' : 
                            ($booking['status'] === 'pending' ? 'warning text-dark' : 
                            ($booking['status'] === 'completed' ? 'info' : 'danger')) 
                        ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </p>
                    <p><strong>Payment Status:</strong> 
                        <span class="badge bg-<?= 
                            $booking['payment_status'] === 'paid' ? 'success' : 
                            ($booking['payment_status'] === 'pending' ? 'warning text-dark' : 'danger') 
                        ?>">
                            <?= ucfirst($booking['payment_status']) ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Notes</h5>
                    <div class="border p-3">
                        <?= $booking['notes'] ? nl2br(htmlspecialchars($booking['notes'])) : 'No notes available' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>