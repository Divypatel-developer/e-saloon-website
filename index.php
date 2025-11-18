<?php
require_once 'config.php';
$page_title = "Home";
$css_files = ['home.css']; // Custom CSS file
require_once 'header.php';
?>

<style>
/* Modern Color Palette */
:root {
    --primary-color: #6a11cb;
    --secondary-color: #2575fc;
    --accent-color: #ff5e62;
    --light-color: #f8f9fa;
    --dark-color: #2c3e50;
    --text-color: #4a4a4a;
    --text-light: #7f8c8d;
}

/* Base Styles */
body {
    font-family: 'Poppins', sans-serif;
    color: var(--text-color);
    line-height: 1.6;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 6rem 0;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('<?php echo BASE_URL; ?>assets/images/hero-pattern.png') center/cover;
    opacity: 0.1;
    z-index: 0;
}

.hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.hero-section h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hero-section p {
    font-size: 1.5rem;
    margin-bottom: 2.5rem;
    opacity: 0.9;
}

.btn-hero {
    background-color: white;
    color: var(--primary-color);
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: none;
}

.btn-hero:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    color: var(--primary-color);
}

/* Services Cards */
.services-section {
    padding: 5rem 0;
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-color);
    position: relative;
    display: inline-block;
}

.section-title h2::after {
    content: '';
    position: absolute;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 3px;
}

.service-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    margin-bottom: 30px;
    height: 100%;
    background: white;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
}

.service-icon {
    font-size: 3.5rem;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    transition: all 0.3s ease;
}

.service-card:hover .service-icon {
    color: var(--secondary-color);
    transform: scale(1.1);
}

.card-body {
    padding: 2rem;
    text-align: center;
}

.card-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--dark-color);
}

.card-text {
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.btn-service {
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
    padding: 8px 25px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-service:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
    color: white;
}

/* Features Section */
.features-section {
    padding: 4rem 0;
    background-color: #f9f9f9;
}

.feature-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
    background: white;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 1.2rem 1.5rem;
    border-bottom: none;
}

.card-header h4 {
    font-weight: 600;
    margin: 0;
}

.feature-list {
    padding: 0;
    list-style: none;
}

.feature-list li {
    padding: 10px 0;
    position: relative;
    padding-left: 30px;
}

.feature-list li:before {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    left: 0;
    color: var(--primary-color);
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .hero-section h1 {
        font-size: 3rem;
    }
    
    .hero-section p {
        font-size: 1.3rem;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 4rem 0;
    }
    
    .hero-section h1 {
        font-size: 2.5rem;
    }
    
    .section-title h2 {
        font-size: 2rem;
    }
}

@media (max-width: 576px) {
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .hero-section p {
        font-size: 1.1rem;
    }
    
    .btn-hero {
        padding: 10px 25px;
        font-size: 1rem;
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1>Welcome to E-Saloon</h1>
        <p>Premium beauty services delivered to your doorstep</p>
        <a href="booking.php" class="btn btn-hero">Book Now</a>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <div class="section-title">
            <h2>Our Services</h2>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="service-card card">
                    <div class="card-body">
                        <i class="fas fa-cut service-icon"></i>
                        <h4 class="card-title">Hair Services</h4>
                        <p class="card-text">Professional haircuts, styling, coloring and treatments by expert stylists in the comfort of your home.</p>
                        <a href="services.php" class="btn btn-service">View Services</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="service-card card">
                    <div class="card-body">
                        <i class="fas fa-spa service-icon"></i>
                        <h4 class="card-title">Skin Care</h4>
                        <p class="card-text">Revitalizing facials, deep cleanups and specialized skin treatments using premium products.</p>
                        <a href="services.php" class="btn btn-service">View Services</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="service-card card">
                    <div class="card-body">
                        <i class="fas fa-hand-sparkles service-icon"></i>
                        <h4 class="card-title">Hand & Foot Care</h4>
                        <p class="card-text">Luxurious manicures, pedicures and nail treatments for perfectly groomed hands and feet.</p>
                        <a href="services.php" class="btn btn-service">View Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="feature-card card">
                    <div class="card-header">
                        <h4><i class="fas fa-question-circle mr-2"></i> How It Works</h4>
                    </div>
                    <div class="card-body">
                        <ol class="feature-list">
                            <li>Browse our extensive service menu</li>
                            <li>Select your preferred date and time slot</li>
                            <li>Complete your booking in just a few clicks</li>
                            <li>Our certified professional arrives at your doorstep</li>
                            <li>Enjoy premium salon services at home</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="feature-card card">
                    <div class="card-header">
                        <h4><i class="fas fa-star mr-2"></i> Why Choose Us</h4>
                    </div>
                    <div class="card-body">
                        <ul class="feature-list">
                            <li>Licensed and experienced professionals</li>
                            <li>Strict hygiene and sanitation protocols</li>
                            <li>Premium quality products and equipment</li>
                            <li>Flexible scheduling to fit your routine</li>
                            <li>Competitive pricing with no hidden fees</li>
                            <li>100% satisfaction guarantee</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>