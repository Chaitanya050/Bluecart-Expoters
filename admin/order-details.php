<?php
$page_title = "Order Details";
include 'includes/header.php';

$order_id = $_GET['id'] ?? 0;

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.full_name as customer_name, u.email as customer_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-receipt me-2"></i>Order Details
                    <small class="text-muted">#<?php echo $order['order_number'] ?? 'ORD-' . $order['id']; ?></small>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="orders.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Orders
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Order Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Order Number:</strong> #<?php echo $order['order_number'] ?? 'ORD-' . $order['id']; ?></p>
                                    <p><strong>Order Date:</strong> <?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Payment Method:</strong> <?php echo $order['payment_method'] ?? 'Cash on Delivery'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info'); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </p>
                                    <p><strong>Total Amount:</strong> <span class="fw-bold text-success">₹<?php echo number_format($order['total_amount'], 2); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone'] ?? 'Not provided'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Shipping Address:</strong></p>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? 'Not provided')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Order Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['product_name'] ?? 'Product Deleted'); ?></strong>
                                            </td>
                                            <td>₹<?php echo number_format($item['product_price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="fw-bold">₹<?php echo number_format($item['item_total'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3" class="text-end">Total Amount:</th>
                                            <th class="text-success">₹<?php echo number_format($order['total_amount'], 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Order Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Order Actions</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="orders.php">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Update Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Update Status</button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Order Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Placed</h6>
                                        <small class="text-muted"><?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></small>
                                    </div>
                                </div>
                                
                                <?php if ($order['status'] !== 'pending'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Confirmed</h6>
                                        <small class="text-muted">Status updated</small>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] === 'completed'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Completed</h6>
                                        <small class="text-muted">Order delivered</small>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

@media print {
    .btn-toolbar, .sidebar, .navbar {
        display: none !important;
    }
    
    .col-md-9 {
        width: 100% !important;
        margin: 0 !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
