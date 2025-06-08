<?php
$page_title = "Shopping Cart";
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=cart.php");
    exit();
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.description, p.price, p.image_primary 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.18;
$total = $subtotal + $tax;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 fw-bold text-center mb-5">
                <i class="fas fa-shopping-cart me-3"></i>Shopping Cart
            </h1>
        </div>
    </div>

    <?php if (empty($cart_items)): ?>
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card p-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h3 class="fw-bold mb-3">Your cart is empty</h3>
                    <p class="text-muted mb-4">Discover our premium products and add them to your cart!</p>
                    <a href="products.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Browse Products
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <h4 class="fw-bold mb-4">Cart Items (<?php echo count($cart_items); ?>)</h4>
                
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo htmlspecialchars($item['image_primary']); ?>" 
                                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="col-md-4">
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($item['description']); ?></p>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-bold text-success" id="price-<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>">
                                ₹<?php echo number_format($item['price'], 2); ?>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="mx-2 fw-bold" id="quantity-<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <span class="fw-bold" id="total-<?php echo $item['id']; ?>">
                                ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </span>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18%):</span>
                            <span id="tax">₹<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total">₹<?php echo number_format($total, 2); ?></strong>
                        </div>
                        
                        <button class="btn btn-success w-100 mb-3" onclick="window.location.href='checkout.php'">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </button>
                        
                        <a href="products.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body text-center">
                        <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
                        <h6 class="fw-bold">Free Shipping</h6>
                        <small class="text-muted">On all orders. Estimated delivery: 3-5 business days.</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
