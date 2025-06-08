<?php
session_start();
require_once '../config/database.php';
require_once 'config/auth.php';

// Check admin authentication
requireAdminLogin();

$page_title = "Manage Orders";

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $notify_customer = isset($_POST['notify_customer']);

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);

        // Get order details for notification
        if ($notify_customer) {
            $stmt = $pdo->prepare("
                SELECT o.*, u.email, u.full_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch();

            // Send email notification
            require_once 'config/email.php';
            $emailer = new EmailNotifier();
            $emailer->sendOrderStatusUpdate($order, $new_status);
        }

        // Log the activity
        logAdminActivity($pdo, $_SESSION['user_id'], 'UPDATE_ORDER', "Updated order #$order_id status to $new_status");

        $pdo->commit();
        $success = "Order status updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating order status: " . $e->getMessage();
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "
    SELECT o.*, u.full_name as customer_name, u.email as customer_email,
           COUNT(oi.id) as total_items,
           (SELECT GROUP_CONCAT(CONCAT(p.name, ' (', oi2.quantity, ')'))
            FROM order_items oi2 
            JOIN products p ON oi2.product_id = p.id 
            WHERE oi2.order_id = o.id) as items_list
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
";

$params = [];
$where = [];

if ($status_filter !== 'all') {
    $where[] = "o.status = ?";
    $params[] = $status_filter;
}

if ($date_from) {
    $where[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

if ($search) {
    $where[] = "(o.id LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " GROUP BY o.id ORDER BY o.created_at DESC";

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Get order statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'processing' => 0,
    'shipped' => 0,
    'delivered' => 0,
    'cancelled' => 0
];

$stmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM orders 
    GROUP BY status
");
while ($row = $stmt->fetch()) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Order Management
                        </h5>
                        <a href="export-orders.php" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export Orders
                        </a>
                    </div>

                    <!-- Order Statistics -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Orders</h6>
                                    <h3 class="mb-0"><?php echo $stats['total']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-warning">Pending</h6>
                                    <h3 class="mb-0"><?php echo $stats['pending']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-info bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-info">Processing</h6>
                                    <h3 class="mb-0"><?php echo $stats['processing']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-primary bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-primary">Shipped</h6>
                                    <h3 class="mb-0"><?php echo $stats['shipped']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-success">Delivered</h6>
                                    <h3 class="mb-0"><?php echo $stats['delivered']; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-danger bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-danger">Cancelled</h6>
                                    <h3 class="mb-0"><?php echo $stats['cancelled']; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form class="row g-3 mb-4">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" placeholder="To Date">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search orders...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </form>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $order['total_items']; ?> items</span>
                                            <div class="text-muted small"><?php echo htmlspecialchars($order['items_list']); ?></div>
                                        </td>
                                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="view-order.php?id=<?php echo $order['id']; ?>">
                                                            <i class="fas fa-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatus<?php echo $order['id']; ?>">
                                                            <i class="fas fa-edit me-2"></i>Update Status
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="print-invoice.php?id=<?php echo $order['id']; ?>" target="_blank">
                                                            <i class="fas fa-print me-2"></i>Print Invoice
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Status Update Modal -->
                                            <div class="modal fade" id="updateStatus<?php echo $order['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Order Status</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">New Status</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input" id="notify<?php echo $order['id']; ?>" name="notify_customer" checked>
                                                                    <label class="form-check-label" for="notify<?php echo $order['id']; ?>">
                                                                        Notify customer via email
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
