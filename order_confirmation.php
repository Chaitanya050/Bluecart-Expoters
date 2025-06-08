<?php
$page_title = "Order Confirmation";
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order'])) {
    header("Location: index.php");
    exit();
}

$order_number = $_GET['order'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.order_number = ? AND o.user_id = ?
");
$stmt->execute([$order_number, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

// Get order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h1 class="display-4 fw-bold text-success mb-3">Order Confirmed!</h1>
                <p class="lead text-muted">Thank you for your order. We'll deliver it to your doorstep soon.</p>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-receipt me-2"></i>Order Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                            <p><strong>Payment Method:</strong> Cash on Delivery</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">Confirmed</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                            <p><strong>Total Amount:</strong> <span class="fw-bold text-success">₹<?php echo number_format($order['total_amount'], 2); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-shipping-fast me-2"></i>Shipping Address
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-box me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($order_items as $item): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['product_price'], 2); ?></small>
                        </div>
                        <span class="fw-bold">₹<?php echo number_format($item['item_total'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="text-end mt-3">
                        <h5 class="fw-bold text-success">Total: ₹<?php echo number_format($order['total_amount'], 2); ?></h5>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Important Reminders</h6>
                <ul class="mb-0 small">
                    <li><strong>This order cannot be cancelled</strong> as per our policy</li>
                    <li>Expected delivery: 1-3 business days</li>
                    <li>Please keep ₹<?php echo number_format($order['total_amount'], 2); ?> ready for COD payment</li>
                    <li>Ensure someone is available at the delivery address</li>
                    <li>Check products at the time of delivery</li>
                </ul>
            </div>

            <div class="text-center">
                <a href="dashboard.php" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-tachometer-alt me-2"></i>View Dashboard
                </a>
                <a href="products.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
