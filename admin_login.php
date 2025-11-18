<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header("Location: delete_customer.php");
    exit;
}

// Include configuration
include 'config.php';

// Initialize variables
$login_error = "";
$admin_username = "";

// Handle login request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Get form data
    $admin_username = trim($_POST['username']);
    $admin_password = trim($_POST['password']);
    
    // Validate input
    if (!empty($admin_username) && !empty($admin_password)) {
        // For demo purposes - in production, you should have a proper admin table
        if ($admin_username === "admin" && $admin_password === "admin123") {
            // Set session variables
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['admin_username'] = $admin_username;
            
            // Redirect to admin dashboard
            header("Location: delete_customer.php");
            exit;
        } else {
            $login_error = "Invalid username or password. Try admin/admin123 for demo.";
        }
    } else {
        $login_error = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - E-Saloon Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        .login-card {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            background: white;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #a29bfe, #6c5ce7);
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 0.75rem;
            border: none;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .form-group {
            position: relative;
        }
        .demo-credentials {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fas fa-lock me-2"></i>Admin Login</h2>
                <p class="mb-0">E-Saloon Management System</p>
            </div>
            <div class="login-body">
                <?php if (!empty($login_error)): ?>
                    <div class='alert alert-danger'><i class='fas fa-exclamation-circle me-2'></i><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin_username); ?>" required placeholder="Enter your username">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                            </div>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-login w-100 py-2 fw-bold">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <div class="demo-credentials">
                    <p class="mb-1"><strong>Demo Credentials:</strong></p>
                    <p class="mb-0">Username: <code>admin</code></p>
                    <p class="mb-0">Password: <code>admin123</code></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white mb-0">© 2023 E-Saloon Management System</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>