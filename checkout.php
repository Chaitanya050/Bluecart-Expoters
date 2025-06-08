<?php
$page_title = "Checkout";
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

// Get cart items - Fixed SQL query to handle missing weight column gracefully
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.description, p.price, p.image_primary, p.stock_quantity,
           COALESCE(p.weight, 0.5) as weight
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Redirect if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Get shipping methods - Check if shipping_methods table exists
try {
    $shipping_methods = $pdo->query("SELECT * FROM shipping_methods WHERE is_active = 1 ORDER BY delivery_days_min")->fetchAll();
} catch (PDOException $e) {
    // If shipping_methods table doesn't exist, create default shipping options
    $shipping_methods = [
        [
            'id' => 1,
            'method_name' => 'Standard Delivery',
            'description' => 'Delivery within 3-5 business days',
            'base_cost' => 0,
            'per_kg_cost' => 0,
            'delivery_days_min' => 3,
            'delivery_days_max' => 5
        ],
        [
            'id' => 2,
            'method_name' => 'Express Delivery',
            'description' => 'Fast delivery within 1-2 business days',
            'base_cost' => 50,
            'per_kg_cost' => 10,
            'delivery_days_min' => 1,
            'delivery_days_max' => 2
        ]
    ];
}

// Calculate totals
$subtotal = 0;
$total_weight = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $total_weight += floatval($item['weight']) * $item['quantity'];
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get website settings - Handle if settings table doesn't exist
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM website_settings WHERE setting_key IN ('tax_rate', 'free_shipping_threshold')");
    $settings = [];
    foreach ($stmt->fetchAll() as $setting) {
        $settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (PDOException $e) {
    // Default settings if table doesn't exist
    $settings = [
        'tax_rate' => '18',
        'free_shipping_threshold' => '500'
    ];
}

$tax_rate = floatval($settings['tax_rate'] ?? 18) / 100;
$free_shipping_threshold = floatval($settings['free_shipping_threshold'] ?? 500);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 fw-bold text-center mb-5">
                <i class="fas fa-credit-card me-3"></i>Checkout
            </h1>
        </div>
    </div>

    <form id="checkoutForm" action="actions/place_order.php" method="POST">
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                       placeholder="+91 9876543210" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Complete Address *</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                      rows="4" placeholder="House/Flat No., Street, Area, City, State, PIN Code" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pincode" class="form-label">PIN Code *</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" 
                                       pattern="[0-9]{6}" maxlength="6" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="order_notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control" id="order_notes" name="order_notes" 
                                      rows="2" placeholder="Any special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="fas fa-truck me-2"></i>Shipping Method</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($shipping_methods as $index => $method): ?>
                        <div class="form-check p-3 border rounded mb-3 shipping-method" 
                             data-cost="<?php echo $method['base_cost']; ?>" 
                             data-per-kg="<?php echo $method['per_kg_cost']; ?>">
                            <input class="form-check-input" type="radio" name="shipping_method" 
                                   id="shipping_<?php echo $method['id']; ?>" value="<?php echo $method['id']; ?>" 
                                   <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold w-100" for="shipping_<?php echo $method['id']; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-truck me-2 text-primary"></i>
                                        <?php echo htmlspecialchars($method['method_name']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($method['description']); ?></small>
                                        <br>
                                        <small class="text-info"><?php echo $method['delivery_days_min']; ?>-<?php echo $method['delivery_days_max']; ?> business days</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="shipping-cost fw-bold text-success">
                                            <?php 
                                            $shipping_cost = $method['base_cost'] + ($method['per_kg_cost'] * $total_weight);
                                            if ($subtotal >= $free_shipping_threshold && $method['base_cost'] == 0) {
                                                echo 'Free';
                                            } else {
                                                echo '₹' . number_format($shipping_cost, 2);
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check p-3 border rounded bg-light">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                <i class="fas fa-hand-holding-usd me-2 text-success"></i>Cash on Delivery (COD)
                            </label>
                            <p class="text-muted small mb-0 mt-2">Pay when your order is delivered to your doorstep. No advance payment required.</p>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="fw-bold mb-0 text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Important Notice</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <h6 class="fw-bold mb-2"><i class="fas fa-ban me-2"></i>No Cancellation Policy</h6>
                            <p class="mb-2">Once your order is placed, it <strong>CANNOT be cancelled</strong> under any circumstances. Please review your order carefully before proceeding.</p>
                        </div>
                        
                        <h6 class="fw-bold mb-2">Terms & Conditions:</h6>
                        <ul class="small text-muted">
                            <li>Orders once placed cannot be cancelled or modified</li>
                            <li>Cash on Delivery charges may apply for orders below ₹500</li>
                            <li>Please ensure someone is available at the delivery address</li>
                            <li>Delivery will be attempted 3 times. After that, order will be returned</li>
                            <li>Products should be checked at the time of delivery</li>
                            <li>Damaged or defective items can be returned within 24 hours of delivery</li>
                        </ul>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="agree_terms" required>
                            <label class="form-check-label fw-bold text-danger" for="agree_terms">
                                I understand and agree that this order CANNOT be cancelled once placed *
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card position-sticky" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">Items (<?php echo count($cart_items); ?>)</h6>
                            <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div class="flex-grow-1">
                                    <small class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></small>
                                    <br>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></small>
                                </div>
                                <small class="fw-bold">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span id="shipping-cost-display" class="text-success">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (<?php echo number_format($tax_rate * 100, 0); ?>%):</span>
                                <span id="tax-amount">₹<?php echo number_format($subtotal * $tax_rate, 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Amount:</strong>
                                <strong class="text-success" id="total-amount">₹<?php echo number_format($subtotal + ($subtotal * $tax_rate), 2); ?></strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg mb-3">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>
                        
                        <a href="cart.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Back to Cart
                        </a>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure Checkout
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const subtotal = <?php echo $subtotal; ?>;
const taxRate = <?php echo $tax_rate; ?>;
const freeShippingThreshold = <?php echo $free_shipping_threshold; ?>;
const totalWeight = <?php echo $total_weight; ?>;

// Update shipping cost when method changes
document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
    radio.addEventListener('change', updateShippingCost);
});

function updateShippingCost() {
    const selectedMethod = document.querySelector('input[name="shipping_method"]:checked');
    const methodDiv = selectedMethod.closest('.shipping-method');
    const baseCost = parseFloat(methodDiv.dataset.cost);
    const perKgCost = parseFloat(methodDiv.dataset.perKg);
    
    let shippingCost = baseCost + (perKgCost * totalWeight);
    
    // Apply free shipping if eligible
    if (subtotal >= freeShippingThreshold && baseCost === 0) {
        shippingCost = 0;
    }
    
    // Update display
    const shippingDisplay = document.getElementById('shipping-cost-display');
    if (shippingCost === 0) {
        shippingDisplay.textContent = 'Free';
        shippingDisplay.className = 'text-success';
    } else {
        shippingDisplay.textContent = '₹' + shippingCost.toFixed(2);
        shippingDisplay.className = 'text-primary';
    }
    
    // Update total
    const tax = subtotal * taxRate;
    const total = subtotal + shippingCost + tax;
    document.getElementById('total-amount').textContent = '₹' + total.toFixed(2);
}

// Initialize shipping cost calculation
updateShippingCost();

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    const agreeTerms = document.getElementById('agree_terms').checked;
    
    if (!agreeTerms) {
        alert('Please agree to the terms and conditions to proceed.');
        return false;
    }
    
    const confirmation = confirm(
        'IMPORTANT: Once placed, this order CANNOT be cancelled.\n\n' +
        'Are you sure you want to place this order?'
    );
    
    if (!confirmation) {
        return false;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Placing Order...';
    submitBtn.disabled = true;

    // Collect form data
    const formData = new FormData(e.target);

    // Submit form using fetch
    fetch('actions/place_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to success page
            window.location.href = data.redirect;
        } else {
            // Show error
            alert(data.error || 'Failed to place order. Please try again.');
            submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
        submitBtn.disabled = false;
    });
});
</script>

<?php include 'includes/footer.php'; ?>
