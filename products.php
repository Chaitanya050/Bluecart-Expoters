<?php
$page_title = "Products";
include 'includes/header.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories
$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 fw-bold text-center mb-4">Our Products</h1>
            <p class="lead text-center text-muted">Discover our premium selection of electronic devices and accessories</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3>No products found</h3>
                <p class="text-muted">Try adjusting your search criteria</p>
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card product-card h-100">
                    <img src="https://via.placeholder.com/400x250/0d6efd/ffffff?text=<?php echo urlencode($product['name']); ?>" 
                         class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($product['category']); ?></span>
                        </div>
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
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
