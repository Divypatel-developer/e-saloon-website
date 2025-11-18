<?php
require_once 'config.php';

if (!isset($_GET['transaction_id'])) {
    header('Location: booking.php');
    exit;
}

$page_title = "Payment Successful";
require_once 'header.php';
?>

<style>
.success-container {
    max-width: 600px;
    margin: 0 auto;
}

.success-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    background: white;
    animation: fadeInUp 0.6s ease-out;
}

.success-icon {
    font-size: 5rem;
    color: var(--success-color);
    margin-bottom: 1.5rem;
    animation: bounceIn 0.8s ease-out;
}

.success-alert {
    background-color: rgba(75, 181, 67, 0.1);
    border-left: 4px solid var(--success-color);
    border-radius: var(--border-radius);
}

.success-btn {
    border-radius: 8px;
    padding: 0.75rem;
    font-weight: 600;
    transition: var(--transition);
}

.success-btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
}

.success-btn-primary:hover {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    transform: translateY(-2px);
}

.success-btn-outline {
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

.success-btn-outline:hover {
    background: rgba(67, 97, 238, 0.1);
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes bounceIn {
    0% { transform: scale(0.5); opacity: 0; }
    60% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); }
}

.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: var(--accent-color);
    opacity: 0;
}

</style>

<div class="success-container">
    <div class="success-card">
        <div class="card-body p-5 text-center">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h2 class="mb-3">Payment Successful!</h2>
            <p class="lead text-muted mb-4">Thank you for your payment. Your appointment has been confirmed.</p>
            
            <div class="success-alert alert mb-4 text-start">
                <div class="d-flex align-items-center">
                    <i class="fas fa-receipt me-3 fa-lg text-success"></i>
                    <div>
                        <h5 class="mb-1">Transaction Receipt</h5>
                        <p class="mb-0 font-monospace"><?= htmlspecialchars($_GET['transaction_id']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-3 mt-5">
                <a href="my_booking.php" class="success-btn success-btn-primary py-3">
                    <i class="fas fa-calendar-alt me-2"></i> View My Bookings
                </a>
                <a href="services.php" class="success-btn success-btn-outline py-3">
                    <i class="fas fa-spa me-2"></i> Book Another Service
                </a>
            </div>
            
            <div class="mt-4 text-muted">
                <small>
                    <i class="fas fa-envelope me-1"></i> A confirmation has been sent to your email
                </small>
            </div>
        </div>
    </div>
</div>

<script>
// Simple confetti effect
document.addEventListener('DOMContentLoaded', function() {
    const colors = ['#4361ee', '#4895ef', '#3f37c9', '#4bb543'];
    const container = document.querySelector('.success-container');
    
    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        confetti.classList.add('confetti');
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.top = -10 + 'px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
        container.appendChild(confetti);
        
        const animationDuration = Math.random() * 3 + 2;
        
        confetti.style.animation = `fall ${animationDuration}s ease-in forwards`;
        
        // Create keyframes dynamically
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes fall {
                0% { transform: translateY(0) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                100% { 
                    transform: translateY(${Math.random() * 300 + 100}px) 
                    rotate(${Math.random() * 360}deg); 
                    opacity: 0; 
                }
            }
        `;
        document.head.appendChild(style);
    }
});
</script>

<?php require_once 'footer.php'; ?>