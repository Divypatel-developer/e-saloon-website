<?php
require_once 'config.php';
$page_title = "Book Appointment";
$css_files = ['booking.css']; // Custom CSS file
require_once 'header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

// Get service details if service_id is provided
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
$service = null;

if ($service_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ? AND is_active = TRUE");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)$_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $notes = trim($_POST['notes']);
    $payment_method = $_POST['payment_method'];
    
    // Validate inputs
    $errors = [];
    
    if (empty($service_id)) {
        $errors[] = "Please select a service";
    }
    
    if (empty($appointment_date)) {
        $errors[] = "Please select a date";
    } elseif (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
        $errors[] = "Appointment date cannot be in the past";
    }
    
    if (empty($appointment_time)) {
        $errors[] = "Please select a time";
    }
    
    if (empty($payment_method)) {
        $errors[] = "Please select a payment method";
    }
    
    if (empty($errors)) {
        // Check if the selected time slot is available
        $stmt = $pdo->prepare("SELECT * FROM appointments 
                              WHERE appointment_date = ? 
                              AND appointment_time = ? 
                              AND status IN ('pending', 'confirmed')");
        $stmt->execute([$appointment_date, $appointment_time]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = "The selected time slot is already booked. Please choose another time.";
        } else {
            // Get service price
            $stmt = $pdo->prepare("SELECT price FROM services WHERE service_id = ?");
            $stmt->execute([$service_id]);
            $service_price = $stmt->fetchColumn();
            
            // Insert the appointment with payment status
            $payment_status = ($payment_method === 'online') ? 'pending' : 'pending';
            $stmt = $pdo->prepare("INSERT INTO appointments 
                                  (customer_id, service_id, appointment_date, appointment_time, notes, payment_status) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $service_id,
                $appointment_date,
                $appointment_time,
                $notes,
                $payment_status
            ]);
            
            $appointment_id = $pdo->lastInsertId();
            
            // Record the payment
            $online_payment_type = isset($_POST['online_payment_type']) ? $_POST['online_payment_type'] : 'cash';
            $payment_method_value = ($payment_method === 'online') ? $online_payment_type : 'cash';
            
            $stmt = $pdo->prepare("INSERT INTO payments 
                                  (appointment_id, amount, payment_method, payment_status) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $appointment_id,
                $service_price,
                $payment_method_value,
                'pending'
            ]);
            
            // Redirect based on payment method
            if ($payment_method === 'online') {
                $_SESSION['payment_appointment_id'] = $appointment_id;
                header('Location: payment.php');
                exit;
            } else {
                $_SESSION['success_message'] = "Your appointment has been booked successfully! Please pay ₹" . 
                                              number_format($service_price, 2) . " in cash when the beautician arrives.";
                header('Location: my_booking.php');
                exit;
            }
        }
    }
}

// Fetch all active services for dropdown
$stmt = $pdo->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY name");
$all_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Booking Page Styles */
.booking-section {
    padding: 4rem 0;
    background-color: #f8f9fa;
}

.booking-header {
    text-align: center;
    margin-bottom: 3rem;
}

.booking-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    display: inline-block;
}

.booking-header h2::after {
    content: '';
    position: absolute;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #6a11cb, #2575fc);
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 3px;
}

.booking-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    padding: 2.5rem;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #6a11cb;
    box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.15);
}

.alert-danger {
    background-color: #fff5f5;
    border-color: #ffd6d6;
    color: #dc3545;
    border-radius: 8px;
}

.payment-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.payment-card-header {
    background: linear-gradient(90deg, #6a11cb, #2575fc);
    color: white;
    padding: 1.2rem 1.5rem;
    border-bottom: none;
}

.payment-card-header h5 {
    font-weight: 600;
    margin: 0;
}

.payment-option {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-option:hover {
    border-color: #6a11cb;
    background-color: #f9f5ff;
}

.payment-option.active {
    border-color: #6a11cb;
    background-color: #f3e9ff;
}

.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.2em;
}

.form-check-input:checked {
    background-color: #6a11cb;
    border-color: #6a11cb;
}

.online-payment-options {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 1rem;
    border: 1px solid #e0e0e0;
}

.booking-summary {
    border-top: 1px solid #eee;
    padding-top: 1.5rem;
}

.booking-summary h5 {
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.btn-confirm {
    background: linear-gradient(90deg, #6a11cb, #2575fc);
    border: none;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(106, 17, 203, 0.3);
    width: 100%;
}

.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(106, 17, 203, 0.4);
}

.service-summary-item {
    margin-bottom: 1rem;
}

.service-summary-item strong {
    color: #2c3e50;
    display: inline-block;
    width: 100px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .booking-header h2 {
        font-size: 2rem;
    }
    
    .booking-form {
        padding: 1.5rem;
    }
}
</style>

<section class="booking-section">
    <div class="container">
        <div class="booking-header">
            <h2>Book an Appointment</h2>
            <p class="lead">Schedule your beauty service with ease</p>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <form method="post" action="booking.php" class="booking-form">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-4">
                        <label for="service_id" class="form-label">Select Service</label>
                        <select class="form-select" id="service_id" name="service_id" required>
                            <option value="">-- Select a Service --</option>
                            <?php foreach ($all_services as $s): ?>
                            <option value="<?php echo $s['service_id']; ?>" 
                                <?php if ($service && $s['service_id'] == $service['service_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($s['name']); ?> (₹<?php echo number_format($s['price'], 2); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="appointment_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                            min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="appointment_time" class="form-label">Time</label>
                        <select class="form-select" id="appointment_time" name="appointment_time" required>
                            <option value="">-- Select Time --</option>
                            <?php 
                            $start_time = strtotime('09:00');
                            $end_time = strtotime('18:00');
                            $interval = 30 * 60; // 30 minutes in seconds
                            
                            for ($time = $start_time; $time <= $end_time; $time += $interval) {
                                echo '<option value="' . date('H:i', $time) . '">' . date('h:i A', $time) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Any special requests or instructions..."></textarea>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="payment-card card mb-4">
                        <div class="payment-card-header">
                            <h5><i class="fas fa-credit-card me-2"></i> Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="payment-option" onclick="document.getElementById('cashPayment').click()">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cashPayment" value="cash" checked>
                                        <label class="form-check-label" for="cashPayment">
                                            <i class="fas fa-money-bill-wave me-2"></i> Pay Cash at Service
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="payment-option" onclick="document.getElementById('onlinePayment').click()">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="onlinePayment" value="online">
                                        <label class="form-check-label" for="onlinePayment">
                                            <i class="fas fa-credit-card me-2"></i> Pay Online Now
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Online Payment Options (hidden by default) -->
                                <div id="onlinePaymentOptions" class="online-payment-options" style="display: none;">
                                    <h6 class="mb-3">Select Payment Method:</h6>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="online_payment_type" id="cardPayment" value="card" checked>
                                        <label class="form-check-label" for="cardPayment">
                                            <i class="far fa-credit-card me-2"></i> Credit/Debit Card
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="online_payment_type" id="upiPayment" value="upi">
                                        <label class="form-check-label" for="upiPayment">
                                            <i class="fas fa-mobile-alt me-2"></i> UPI Payment
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="online_payment_type" id="netbankingPayment" value="netbanking">
                                        <label class="form-check-label" for="netbankingPayment">
                                            <i class="fas fa-university me-2"></i> Net Banking
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Booking Summary -->
                            <div class="booking-summary">
                                <h5><i class="fas fa-receipt me-2"></i> Booking Summary</h5>
                                <div id="service_summary" class="mb-4">
                                    <?php if ($service): ?>
                                    <div class="service-summary-item">
                                        <strong>Service:</strong> <?php echo htmlspecialchars($service['name']); ?>
                                    </div>
                                    <div class="service-summary-item">
                                        <strong>Price:</strong> ₹<?php echo number_format($service['price'], 2); ?>
                                    </div>
                                    <div class="service-summary-item">
                                        <strong>Duration:</strong> <?php echo floor($service['duration'] / 60) . 'h ' . ($service['duration'] % 60) . 'm'; ?>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted">Select a service to see details</p>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn-confirm">
                                    <i class="fas fa-calendar-check me-2"></i> Confirm Booking
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
// Show/hide online payment options based on selection
document.getElementById('onlinePayment').addEventListener('change', function() {
    document.getElementById('onlinePaymentOptions').style.display = this.checked ? 'block' : 'none';
});
document.getElementById('cashPayment').addEventListener('change', function() {
    document.getElementById('onlinePaymentOptions').style.display = 'none';
});

// Update service summary when service is selected
document.getElementById('service_id').addEventListener('change', function() {
    const serviceId = this.value;
    if (!serviceId) {
        document.getElementById('service_summary').innerHTML = '<p class="text-muted">Select a service to see details</p>';
        return;
    }
    
    // Fetch service details via AJAX
   fetch('services.php?id=' + serviceId)
  .then(response => {
      console.log("Raw response:", response);
      return response.text();  // first get raw text instead of JSON
  })
  .then(text => {
      console.log("Response text:", text);
      let data;
      try {
          data = JSON.parse(text); // manually parse JSON
      } catch (e) {
          throw new Error("Invalid JSON: " + e.message + " | Response was: " + text);
      }

      if (data.error) {
          document.getElementById('service_summary').innerHTML =
              '<p class="text-danger">' + data.error + '</p>';
      } else {
          const durationHours = Math.floor(data.duration / 60);
          const durationMinutes = data.duration % 60;
          let durationStr = '';
          if (durationHours > 0) durationStr += durationHours + 'h ';
          if (durationMinutes > 0) durationStr += durationMinutes + 'm';

          document.getElementById('service_summary').innerHTML = `
              <div class="service-summary-item"><strong>Service:</strong> ${data.name}</div>
              <div class="service-summary-item"><strong>Price:</strong> ₹${Number(data.price).toFixed(2)}</div>
              <div class="service-summary-item"><strong>Duration:</strong> ${durationStr}</div>
          `;
      }
  })
  .catch(error => {
      document.getElementById('service_summary').innerHTML =
          <p class="text-danger">Error loading service details: ${error}</p>;
      console.error("Full error:", error);
  });
});

// Highlight selected payment option
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('active');
        });
        this.classList.add('active');
    });
});
</script>

<?php require_once 'footer.php'; ?>