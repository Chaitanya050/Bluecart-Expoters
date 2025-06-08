<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Get notification counts
try {
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= low_stock_threshold")->fetchColumn();
    $new_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
} catch (Exception $e) {
    $pending_orders = 0;
    $low_stock = 0;
    $new_customers = 0;
}
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4 p-3">
            <div class="admin-avatar mx-auto mb-2" style="width: 60px; height: 60px; font-size: 24px;">
                <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 2)); ?>
            </div>
            <h6 class="text-primary fw-bold"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h6>
            <small class="text-muted">Administrator</small>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                    <i class="fas fa-shopping-cart me-2"></i>Orders
                    <?php if ($pending_orders > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo $pending_orders; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="products.php">
                    <i class="fas fa-box me-2"></i>Products
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>" href="inventory.php">
                    <i class="fas fa-warehouse me-2"></i>Inventory
                    <?php if ($low_stock > 0): ?>
                        <span class="badge bg-warning ms-auto"><?php echo $low_stock; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'customers.php' ? 'active' : ''; ?>" href="customers.php">
                    <i class="fas fa-users me-2"></i>Customers
                    <?php if ($new_customers > 0): ?>
                        <span class="badge bg-info ms-auto"><?php echo $new_customers; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'shipping.php' ? 'active' : ''; ?>" href="shipping.php">
                    <i class="fas fa-shipping-fast me-2"></i>Shipping
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cogs me-2"></i>Settings
                </a>
            </li>
        </ul>
        
        <hr class="my-3">
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-success" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>View Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
        
        <div class="mt-4 p-3 bg-white rounded mx-2">
            <small class="text-muted d-block mb-1">Quick Stats</small>
            <div class="d-flex justify-content-between">
                <small><i class="fas fa-shopping-cart text-primary"></i> <?php echo $pending_orders; ?></small>
                <small><i class="fas fa-exclamation-triangle text-warning"></i> <?php echo $low_stock; ?></small>
                <small><i class="fas fa-user-plus text-info"></i> <?php echo $new_customers; ?></small>
            </div>
        </div>
    </div>
</nav>
