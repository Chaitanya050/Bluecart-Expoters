<?php
session_start();
require_once '../config/database.php';
require_once 'config/auth.php';
require_once 'config/config.php';

// Check admin authentication
requireAdminLogin();

$page_title = "Admin Dashboard";

// Initialize variables with default values
$order_stats = [
    'total_orders' => 0,
    'total_revenue' => 0,
    'today_orders' => 0,
    'today_revenue' => 0
];

$product_stats = [
    'total_products' => 0,
    'low_stock' => 0
];

$customer_stats = [
    'total_customers' => 0,
    'new_customers' => 0
];

$recent_orders = [];
$top_products = [];

// Get quick statistics
try {
    // Orders statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_orders,
            SUM(total) as total_revenue,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_orders,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total ELSE 0 END) as today_revenue
        FROM orders 
        WHERE status != 'cancelled'
    ");
    $order_stats = $stmt->fetch();

    // Product statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_products,
            COUNT(CASE WHEN stock_quantity <= low_stock_threshold THEN 1 END) as low_stock
        FROM products
    ");
    $product_stats = $stmt->fetch();

    // Customer statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_customers,
            COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as new_customers
        FROM users 
        WHERE role = 'customer'
    ");
    $customer_stats = $stmt->fetch();

    // Recent orders
    $stmt = $pdo->query("
        SELECT o.*, u.name as customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll();

    // Top selling products
    $stmt = $pdo->query("
        SELECT 
            p.*, 
            SUM(oi.quantity) as total_sold,
            SUM(oi.subtotal) as revenue
        FROM products p
        JOIN order_items oi ON p.id = oi.product_id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status != 'cancelled'
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $top_products = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Error fetching dashboard data: " . $e->getMessage();
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Total Orders</h6>
                            <h2 class="text-white mb-0"><?php echo number_format($stats['orders']['total']); ?></h2>
                            <small class="text-white-50">
                                <?php 
                                $growth = $stats['orders']['monthly_growth'];
                                $class = $growth >= 0 ? 'text-success' : 'text-danger';
                                ?>
                                <i class="fas fa-arrow-<?php echo $growth >= 0 ? 'up' : 'down'; ?> me-1"></i>
                                <?php echo abs($growth); ?>% <?php echo $growth >= 0 ? 'growth' : 'decline'; ?>
                            </small>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Total Revenue</h6>
                            <h2 class="text-white mb-0"><?php echo formatCurrency($stats['orders']['total_revenue']); ?></h2>
                            <small class="text-white-50">
                                <?php echo number_format($stats['orders']['today_revenue']); ?> today
                            </small>
                        </div>
                        <i class="fas fa-dollar-sign fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Active Customers</h6>
                            <h2 class="text-white mb-0"><?php echo number_format($stats['customers']['active']); ?></h2>
                            <small class="text-white-50">
                                <?php echo number_format($stats['customers']['new_today']); ?> new today
                            </small>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Product Inventory</h6>
                            <h2 class="text-white mb-0"><?php echo number_format($stats['products']['total']); ?></h2>
                            <small class="text-white-50">
                                <?php echo number_format($stats['products']['low_stock']); ?> low stock
                            </small>
                        </div>
                        <i class="fas fa-box fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-line me-2"></i>Revenue Trend (Last 7 Days)
                    </h5>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="products.php?action=new" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-plus me-2"></i>Add Product
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="orders.php?status=pending" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-shopping-cart me-2"></i>Process Orders
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="customers.php" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-users me-2"></i>Manage Customers
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="reports.php" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-file-alt me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>Recent Orders
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                    <td><?php echo formatCurrency($order['total']); ?></td>
                                    <td><?php echo getStatusLabel($order['status']); ?></td>
                                    <td><?php echo formatDate($order['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-bar me-2"></i>Top Selling Products
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_products as $product): ?>
                                <tr>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo formatCurrency($product['price']); ?></td>
                                    <td><?php echo number_format($product['total_sold']); ?></td>
                                    <td><?php echo formatCurrency($product['total_quantity'] * $product['price']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo $notifications['pending_orders']; ?> pending orders need attention
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-box-open me-2"></i>
                        <?php echo $notifications['low_stock']; ?> products are running low on stock
                    </div>
                    <div class="alert alert-success">
                        <i class="fas fa-user-plus me-2"></i>
                        <?php echo $notifications['new_customers']; ?> new customers joined today
                    </div>
                </div>
            </div>
        </div>
    </div>

                    <!-- Quick Stats -->
                    <div class="row g-4 mb-4">
                        <!-- Orders Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card stat-card bg-primary bg-opacity-10">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="text-primary">Total Orders</h6>
                                            <h3><?php echo number_format($order_stats['total_orders'] ?? 0); ?></h3>
                                            <small class="text-muted">+<?php echo $order_stats['today_orders'] ?? 0; ?> today</small>
                                        </div>
                                        <div class="icon-container text-primary">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card stat-card bg-success bg-opacity-10">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="text-success">Total Revenue</h6>
                                            <h3><?php echo formatCurrency($order_stats['total_revenue'] ?? 0); ?></h3>
                                            <small class="text-muted">+<?php echo formatCurrency($order_stats['today_revenue'] ?? 0); ?> today</small>
                                        </div>
                                        <div class="icon-container text-success">
                                            <i class="fas fa-rupee-sign"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card stat-card bg-info bg-opacity-10">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="text-info">Total Products</h6>
                                            <h3><?php echo number_format($product_stats['total_products'] ?? 0); ?></h3>
                                            <small class="text-danger"><?php echo $product_stats['low_stock'] ?? 0; ?> low stock</small>
                                        </div>
                                        <div class="icon-container text-info">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customers Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card stat-card bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div>
                                            <h6 class="text-warning">Total Customers</h6>
                                            <h3><?php echo number_format($customer_stats['total_customers'] ?? 0); ?></h3>
                                            <small class="text-muted">+<?php echo $customer_stats['new_customers'] ?? 0; ?> this month</small>
                                        </div>
                                        <div class="icon-container text-warning">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Quick Actions</h6>
                            <div class="btn-group">
                                <a href="products.php?action=new" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add New Product
                                </a>
                                <a href="orders.php?status=pending" class="btn btn-warning">
                                    <i class="fas fa-clock me-2"></i>View Pending Orders
                                </a>
                                <a href="inventory.php?filter=low_stock" class="btn btn-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Items
                                </a>
                                <a href="reports.php" class="btn btn-success">
                                    <i class="fas fa-chart-bar me-2"></i>View Reports
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Orders -->
                        <div class="col-md-6 mb-4">
                            <div class="dashboard-table mb-4">
                                <div class="table-header">
                                    <h5><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                                    <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Customer</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_orders as $order): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="orders.php?action=view&id=<?php echo $order['id']; ?>" class="text-decoration-none">
                                                                <?php echo $order['order_number']; ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                        <td><?php echo formatCurrency($order['total']); ?></td>
                                                        <td>
                                                            <?php
                                                            $status_colors = [
                                                                'pending' => 'warning',
                                                                'processing' => 'info',
                                                                'shipped' => 'primary',
                                                                'delivered' => 'success',
                                                                'cancelled' => 'danger'
                                                            ];
                                                            $color = $status_colors[$order['status']] ?? 'secondary';
                                                            ?>
                                                            <span class="badge bg-<?php echo $color; ?>">
                                                                <?php echo ucfirst($order['status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Products -->
                        <div class="col-md-6 mb-4">
                            <div class="dashboard-table">
                                <div class="table-header">
                                    <h5><i class="fas fa-chart-line me-2"></i>Top Selling Products</h5>
                                    <a href="reports.php" class="btn btn-sm btn-outline-primary">View Report</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Sold</th>
                                                    <th>Revenue</th>
                                                    <th>Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_products as $product): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                                                <?php echo htmlspecialchars($product['name']); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo number_format($product['total_sold']); ?></td>
                                                        <td><?php echo formatCurrency($product['revenue']); ?></td>
                                                        <td>
                                                            <?php if ($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                                                                <span class="text-danger">
                                                                    <?php echo $product['stock_quantity']; ?> left
                                                                </span>
                                                            <?php else: ?>
                                                                <?php echo $product['stock_quantity']; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

require_once 'includes/footer.php';
?>
