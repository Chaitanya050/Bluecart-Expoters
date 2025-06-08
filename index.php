<?php
$page_title = "Home";
include 'includes/header.php';

// Get featured products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
$featured_products = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center hero-content">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Premium Electronics Store</h1>
                <p class="lead mb-4">Your trusted destination for high-quality smartphones, laptops, accessories and more. Bringing you the latest tech since 2015.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="products.php" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-shopping-bag me-2"></i>Browse Products
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="assets/images/hero-banner.php" alt="Latest Electronics" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold mb-3">Why Choose BlueCrate Exports?</h2>
                <p class="lead text-muted">We provide exceptional quality and service to tech enthusiasts nationwide</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Authentic Products</h5>
                    <p class="text-muted">100% genuine products with manufacturer warranty and support</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Fast Delivery</h5>
                    <p class="text-muted">Express shipping available with 1-3 day delivery nationwide</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Expert Support</h5>
                    <p class="text-muted">Dedicated tech support team available 7 days a week</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Secure Shopping</h5>
                    <p class="text-muted">Safe and secure payment options with buyer protection</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">10K+</span>
                    <h5 class="fw-bold">Happy Customers</h5>
                    <p class="text-muted">Trusted by tech enthusiasts</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <h5 class="fw-bold">Products</h5>
                    <p class="text-muted">Curated selection</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">8+</span>
                    <h5 class="fw-bold">Years Experience</h5>
                    <p class="text-muted">Tech industry expertise</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <h5 class="fw-bold">Customer Support</h5>
                    <p class="text-muted">Always available</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold mb-3">Featured Products</h2>
                <p class="lead text-muted">Discover our latest tech offerings</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card product-card h-100">
                    <img src="assets/images/products/placeholder.php" 
                         class="card-img-top product-image" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h5 text-success fw-bold mb-0">₹<?php echo number_format($product['price'], 2); ?></span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-success" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-success">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login to Buy
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-eye me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
