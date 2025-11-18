<?php
require_once 'config.php';

if (!isLoggedIn() || !isset($_SESSION['payment_appointment_id'])) {
    header('Location: booking.php');
    exit;
}

$appointment_id = $_SESSION['payment_appointment_id'];

// Get appointment details
$stmt = $pdo->prepare("SELECT a.*, s.name as service_name, s.price as service_price 
                      FROM appointments a
                      JOIN services s ON a.service_id = s.service_id
                      WHERE a.appointment_id = ? AND a.customer_id = ?");
$stmt->execute([$appointment_id, $_SESSION['user_id']]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    $_SESSION['error_message'] = "Appointment not found";
    header('Location: booking.php');
    exit;
}

$page_title = "Complete Payment";
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Invalid CSRF token";
        header('Location: booking.php');
        exit;
    }
    
    // In a real implementation, this would verify payment with a gateway
    $transaction_id = 'TXN' . time() . rand(1000, 9999);
    
    try {
        $pdo->beginTransaction();
        
        // Update payment record
        $stmt = $pdo->prepare("UPDATE payments SET 
                              payment_status = 'completed',
                              transaction_id = ?,
                              payment_date = NOW()
                              WHERE appointment_id = ?");
        $stmt->execute([$transaction_id, $appointment_id]);
        
        // Update appointment status
        $stmt = $pdo->prepare("UPDATE appointments SET payment_status = 'paid' WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);
        
        $pdo->commit();
        
        unset($_SESSION['payment_appointment_id']);
        header('Location: payment-success.php?transaction_id=' . $transaction_id);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Payment failed: " . $e->getMessage();
        header('Location: booking.php');
        exit;
    }
}
?>
<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --accent-color: #4895ef;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --success-color: #4bb543;
    --danger-color: #f44336;
    --border-radius: 12px;
    --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7ff;
    color: var(--dark-color);
    line-height: 1.6;
}

.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    transition: var(--transition);
    margin-bottom: 2rem;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 1.5rem;
    border-bottom: none;
}

.card-header h4 {
    font-weight: 600;
    margin: 0;
    color: white;
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.form-control {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: var(--transition);
    background-color: var(--light-color);
}

.form-control:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    border-radius: 8px;
    padding: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: var(--transition);
    text-transform: uppercase;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    transform: translateY(-2px);
}

.order-summary {
    background-color: #f8f9fa;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid var(--primary-color);
}

.order-summary h5 {
    color: var(--primary-color);
    margin-bottom: 1.25rem;
    font-weight: 600;
}

.payment-icon {
    font-size: 1.5rem;
    margin-right: 0.5rem;
    vertical-align: middle;
}

.credit-card {
    position: relative;
    margin-bottom: 2rem;
    height: 180px;
    perspective: 1000px;
}

.credit-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.6s;
    transform-style: preserve-3d;
    border-radius: var(--border-radius);
}

.credit-card:hover .credit-card-inner {
    transform: rotateY(180deg);
}

.credit-card-front, .credit-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    background: linear-gradient(135deg, #2b2d42, #4a4e69);
    color: white;
}

.credit-card-back {
    transform: rotateY(180deg);
    background: linear-gradient(135deg, #4a4e69, #2b2d42);
}

.card-logo {
    text-align: right;
    margin-bottom: 1.5rem;
}

.card-number {
    font-size: 1.25rem;
    letter-spacing: 2px;
    margin-bottom: 1.5rem;
    font-family: 'Courier New', monospace;
}

.card-details {
    display: flex;
    justify-content: space-between;
}

.card-cvv {
    background: white;
    color: var(--dark-color);
    padding: 0.5rem;
    border-radius: 4px;
    text-align: right;
    font-family: 'Courier New', monospace;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animated {
    animation: fadeIn 0.6s ease-out forwards;
}

.delay-1 { animation-delay: 0.2s; }
.delay-2 { animation-delay: 0.4s; }
.delay-3 { animation-delay: 0.6s; }

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .credit-card {
        height: 160px;
    }
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card animated">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i> Complete Payment</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="order-summary animated delay-1">
                            <h5><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service:</span>
                                <strong><?= htmlspecialchars($appointment['service_name']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Amount:</span>
                                <strong>₹<?= number_format($appointment['service_price'], 2) ?></strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h5">Total:</span>
                                <span class="h5 text-primary">₹<?= number_format($appointment['service_price'], 2) ?></span>
                            </div>
                        </div>
                        
                        <div class="credit-card animated delay-2">
                            <div class="credit-card-inner">
                                <div class="credit-card-front">
                                    <div class="card-logo">
                                        <i class="fab fa-cc-visa fa-2x"></i>
                                    </div>
                                    <div class="card-number">
                                        •••• •••• •••• 4242
                                    </div>
                                    <div class="card-details">
                                        <div>
                                            <small>Card Holder</small>
                                            <div>JOHN DOE</div>
                                        </div>
                                        <div>
                                            <small>Expires</small>
                                            <div>12/25</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="credit-card-back">
                                    <div class="mb-3" style="height: 40px; background: #1a1a1a;"></div>
                                    <div class="card-cvv">
                                        <small>CVV</small>
                                        <div>•••</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <form method="post" action="payment.php" class="animated delay-3">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-credit-card"></i></span>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456" required 
                                           pattern="[\d ]{16,19}" maxlength="19">
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Expiry Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        <input type="text" class="form-control" placeholder="MM/YY" required 
                                               pattern="\d{2}/\d{2}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CVV</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="text" class="form-control" placeholder="123" required 
                                               pattern="\d{3,4}" maxlength="4">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Name on Card</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-user"></i></span>
                                    <input type="text" class="form-control" placeholder="John Doe" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-lock me-2"></i> Pay ₹<?= number_format($appointment['service_price'], 2) ?>
                            </button>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i> Your payment is secured with 256-bit encryption
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4">
                                <img src="https://via.placeholder.com/50x30?text=VISA" alt="Visa" class="mx-2">
                                <img src="https://via.placeholder.com/50x30?text=MC" alt="Mastercard" class="mx-2">
                                <img src="https://via.placeholder.com/50x30?text=AMEX" alt="Amex" class="mx-2">
                                <img src="https://via.placeholder.com/50x30?text=DISCOVER" alt="Discover" class="mx-2">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>