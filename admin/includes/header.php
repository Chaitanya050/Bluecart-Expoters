<?php
if (!defined('ADMIN_TITLE')) {
    define('ADMIN_TITLE', 'BlueCrate Admin');
}

// Check if user is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}
$_SESSION['last_activity'] = time();

// Get notifications
try {
    $notifications = [
        'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
        'low_stock' => $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= low_stock_threshold")->fetchColumn(),
        'new_customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn()
    ];
} catch (PDOException $e) {
    $notifications = ['pending_orders' => 0, 'low_stock' => 0, 'new_customers' => 0];
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo ADMIN_TITLE; ?></title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <!-- CSRF Token -->
    <?php
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    
    <!-- Error Handling -->
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        error_log("[$errno] $errstr in $errfile on line $errline");
        return true;
    });
    ?>
</head>
<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php">
            <?php echo ADMIN_TITLE; ?>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" 
                data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="w-100"></div>
        
        <div class="navbar-nav">
            <div class="nav-item text-nowrap d-flex align-items-center px-3">
                <div class="admin-avatar me-2">
                    <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 2)); ?>
                </div>
                <span class="text-white me-2"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                <a class="nav-link px-3" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'orders' ? 'active' : ''; ?>" href="orders.php">
                                <i class="fas fa-shopping-cart"></i> Orders
                                <?php if ($notifications['pending_orders']): ?>
                                    <span class="badge bg-warning float-end"><?php echo $notifications['pending_orders']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'products' ? 'active' : ''; ?>" href="products.php">
                                <i class="fas fa-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'inventory' ? 'active' : ''; ?>" href="inventory.php">
                                <i class="fas fa-warehouse"></i> Inventory
                                <?php if ($notifications['low_stock']): ?>
                                    <span class="badge bg-danger float-end"><?php echo $notifications['low_stock']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'customers' ? 'active' : ''; ?>" href="customers.php">
                                <i class="fas fa-users"></i> Customers
                                <?php if ($notifications['new_customers']): ?>
                                    <span class="badge bg-info float-end"><?php echo $notifications['new_customers']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'shipping' ? 'active' : ''; ?>" href="shipping.php">
                                <i class="fas fa-truck"></i> Shipping
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>" href="reports.php">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php
                // Initialize session variables if not set
                $_SESSION['admin_name'] = $_SESSION['admin_name'] ?? 'Admin';
                $_SESSION['admin_email'] = $_SESSION['admin_email'] ?? '';
                ?>
                <header class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><?php echo $page_title; ?></h4>
                </header>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
