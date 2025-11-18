<?php
require_once 'config.php';

// Redirect logged in users
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$page_title = "Register";
require_once 'header.php';

// Initialize variables
$errors = [];
$first_name = $last_name = $email = $phone = $address = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $terms = isset($_POST['terms']);

    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required";
    } elseif (strlen($first_name) > 50) {
        $errors[] = "First name must be less than 50 characters";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required";
    } elseif (strlen($last_name) > 50) {
        $errors[] = "Last name must be less than 50 characters";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email must be less than 100 characters";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (!$terms) {
        $errors[] = "You must accept the terms and conditions";
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Email already registered";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // If no errors, insert into database and log user in
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, email, phone, password, address, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                $phone,
                $hashed_password,
                $address
            ]);
            
            // Get the newly created user ID
            $userId = $pdo->lastInsertId();
            
            // Set session variables to log the user in
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_first_name'] = $first_name;
            $_SESSION['user_last_name'] = $last_name;
            
            // Set a success flash message
            $_SESSION['success_message'] = "Registration successful! Welcome, $first_name!";
            
            // Redirect to the home page
            header('Location: index.php');
            exit;
            
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<style>
    :root {
        --primary-color: #6c63ff;
        --primary-dark: #564fd9;
        --primary-light: #a29dff;
        --secondary-color: #f8f9fc;
        --accent-color: #ff6584;
        --text-color: #2d3748;
        --light-gray: #f8f9fa;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        --gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    }
    
    body {
        background-color: #f5f7fa;
        background-image: radial-gradient(circle at 10% 20%, rgba(108, 99, 255, 0.05) 0%, transparent 20%),
                          radial-gradient(circle at 90% 80%, rgba(255, 101, 132, 0.05) 0%, transparent 20%);
        color: var(--text-color);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 1rem;
    }
    
    .registration-container {
        max-width: 800px;
        margin: 2rem auto;
    }
    
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        transition: var(--transition);
        border: 1px solid rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(5px);
        background-color: rgba(255, 255, 255, 0.95);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        background: var(--gradient);
        border-bottom: none;
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    
    .card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
    }
    
    .card-header h4 {
        font-weight: 700;
        margin: 0;
        font-size: 1.75rem;
        position: relative;
        letter-spacing: -0.5px;
    }
    
    .card-body {
        padding: 2.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        height: calc(2.5rem + 2px);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: var(--transition);
        font-size: 0.95rem;
        background-color: #fff;
    }
    
    .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
        outline: none;
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
    
    .btn-primary {
        background: var(--gradient);
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: var(--transition);
        border-radius: 8px;
        font-size: 1rem;
        width: 100%;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
        opacity: 0;
        z-index: -1;
        transition: var(--transition);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px -5px rgba(108, 99, 255, 0.5);
    }
    
    .btn-primary:hover::before {
        opacity: 1;
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .alert-danger {
        background-color: #fff5f5;
        border: 1px solid #fed7d7;
        color: #e53e3e;
        border-radius: var(--border-radius);
        padding: 1.25rem;
    }
    
    .alert-danger h5 {
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
        color: #e53e3e;
    }
    
    .alert-danger ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }
    
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        margin-top: 0.15em;
        border: 1px solid #e2e8f0;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .form-check-label {
        color: var(--text-color);
    }
    
    .form-check-label a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
        font-weight: 600;
    }
    
    .form-check-label a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    
    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        color: #4a5568;
    }
    
    .login-link a {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
    }
    
    .login-link a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    
    /* Password strength indicator */
    .password-strength-container {
        margin-top: 0.5rem;
    }
    
    .password-strength-text {
        font-size: 0.75rem;
        color: #4a5568;
        margin-bottom: 0.25rem;
    }
    
    .password-strength {
        height: 6px;
        background-color: #edf2f7;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    
    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.5s ease, background-color 0.5s ease;
    }
    
    /* Form group animation */
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }
    
    .form-group:focus-within {
        transform: translateX(3px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.75rem;
        }
        
        .card-header {
            padding: 1.5rem;
        }
        
        .card-header h4 {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .registration-container {
            padding: 0 1rem;
        }
    }
    
    /* Floating label effect */
    .floating-label-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .floating-label {
        position: absolute;
        top: 0.75rem;
        left: 1rem;
        font-size: 0.9rem;
        color: #718096;
        transition: all 0.2s ease;
        pointer-events: none;
        background: #fff;
        padding: 0 0.25rem;
        border-radius: 4px;
    }
    
    .form-control:focus ~ .floating-label,
    .form-control:not(:placeholder-shown) ~ .floating-label {
        top: -0.5rem;
        left: 0.75rem;
        font-size: 0.75rem;
        color: var(--primary-color);
        background: linear-gradient(to bottom, rgba(255,255,255,0.9) 50%, transparent 50%);
    }
    
    /* Animated border for form inputs */
    .input-border-effect {
        position: relative;
    }
    
    .input-border-effect::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--gradient);
        transition: width 0.4s ease;
    }
    
    .input-border-effect:focus-within::after {
        width: 100%;
    }
</style>

<div class="registration-container">
    <div class="card">
        <div class="card-header">
            <h4>Create Your Account</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <h5>Please fix the following issues:</h5>
                <ul class="mb-0 pl-3">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form method="post" action="register.php" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group input-border-effect">
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" 
                                   placeholder=" " required>
                            <label class="floating-label" for="first_name">First Name *</label>
                            <div class="invalid-feedback">
                                Please provide your first name.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group input-border-effect">
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>" 
                                   placeholder=" " required>
                            <label class="floating-label" for="last_name">Last Name *</label>
                            <div class="invalid-feedback">
                                Please provide your last name.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group input-border-effect">
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                           placeholder=" " required>
                    <label class="floating-label" for="email">Email Address *</label>
                    <div class="invalid-feedback">
                        Please provide a valid email address.
                    </div>
                </div>
                
                <div class="form-group input-border-effect">
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" 
                           placeholder=" " required>
                    <label class="floating-label" for="phone">Phone Number *</label>
                    <div class="invalid-feedback">
                        Please provide your phone number.
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group input-border-effect">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder=" " required
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                   oninput="updatePasswordStrength(this.value)">
                            <label class="floating-label" for="password">Password *</label>
                            <div class="password-strength-container">
                                <div class="password-strength-text">
                                    Password strength: <span id="password-strength-text">Weak</span>
                                </div>
                                <div class="password-strength">
                                    <div class="password-strength-bar" id="password-strength-bar"></div>
                                </div>
                                <small class="form-text text-muted">
                                    Must be at least 8 characters with uppercase, lowercase, and number
                                </small>
                            </div>
                            <div class="invalid-feedback">
                                Password must be at least 8 characters with uppercase, lowercase, and number.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group input-border-effect">
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" placeholder=" " required>
                            <label class="floating-label" for="confirm_password">Confirm Password *</label>
                            <div class="invalid-feedback">
                                Passwords must match.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group input-border-effect">
                    <textarea class="form-control" id="address" name="address" rows="3" placeholder=" "><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                    <label class="floating-label" for="address">Address</label>
                </div>
                
                <div class="form-group form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a> *</label>
                    <div class="invalid-feedback">
                        You must agree to the terms and conditions.
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </button>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced Password strength indicator
function updatePasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength += 1;
    if (password.length >= 12) strength += 1;
    
    // Character type checks
    if (password.match(/[a-z]/)) strength += 1;
    if (password.match(/[A-Z]/)) strength += 1;
    if (password.match(/[0-9]/)) strength += 1;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 2;
    
    // Calculate percentage (max strength is 7 in this example)
    const percentage = Math.min(100, (strength / 7) * 100);
    strengthBar.style.width = percentage + '%';
    
    // Update color and text based on strength
    if (percentage < 40) {
        strengthBar.style.backgroundColor = '#e53e3e'; // red
        strengthText.textContent = 'Weak';
        strengthText.style.color = '#e53e3e';
    } else if (percentage < 70) {
        strengthBar.style.backgroundColor = '#dd6b20'; // orange
        strengthText.textContent = 'Moderate';
        strengthText.style.color = '#dd6b20';
    } else if (percentage < 90) {
        strengthBar.style.backgroundColor = '#38a169'; // green
        strengthText.textContent = 'Strong';
        strengthText.style.color = '#38a169';
    } else {
        strengthBar.style.backgroundColor = '#2f855a'; // dark green
        strengthText.textContent = 'Very Strong';
        strengthText.style.color = '#2f855a';
    }
}

// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Add floating label functionality for browsers that don't support :placeholder-shown
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        // Check if value exists (for form validation)
        if (input.value) {
            input.parentElement.querySelector('.floating-label').classList.add('active');
        }
        
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('.floating-label').classList.add('active');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.querySelector('.floating-label').classList.remove('active');
            }
        });
    });
});
</script>

<?php require_once 'footer.php'; ?>



