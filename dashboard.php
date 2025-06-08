<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'db_connect.php';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Set page title
$page_title = "Dashboard";

// Include header
include('includes/header.php');

// Get recent orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent_orders = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="row">
        <!-- User Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">User Profile</h5>
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3" style="width: 100px; height: 100px; background: linear-gradient(135deg, #4CAF50, #2196F3); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 2.5rem; font-weight: bold;">
                                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                            </span>
                        </div>
                        <h4 class="mb-0"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    
                    <div class="d-grid">
                        <a href="edit-profile.php" class="btn btn-primary">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Overview -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview
                    </h5>
                    <p class="card-text">
                        Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?>!
                    </p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-clock me-2"></i>Recent Orders
                    </h5>
                    
                    <?php if (empty($recent_orders)): ?>
                        <div class="text-center py-4">
                            <img src="assets/images/no-orders.svg" alt="No Orders" style="width: 120px; opacity: 0.5;" class="mb-3">
                            <p class="text-muted mb-3">You haven't placed any orders yet.</p>
                            <a href="products.php" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
