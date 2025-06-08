<?php
$page_title = "Customer Management";
include 'includes/header.php';

// Get customers with order statistics
$stmt = $pdo->query("
    SELECT u.*, 
           COUNT(o.id) as total_orders,
           COALESCE(SUM(o.total_amount), 0) as total_spent,
           MAX(o.created_at) as last_order_date
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id 
    WHERE u.role != 'admin'
    GROUP BY u.id 
    ORDER BY u.created_at DESC
");
$customers = $stmt->fetchAll();

// Get customer statistics
$stats = [
    'total_customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn(),
    'active_customers' => $pdo->query("SELECT COUNT(DISTINCT user_id) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    'new_this_month' => $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn()
];
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-users me-2"></i>Customer Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary" onclick="exportCustomers()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary"><?php echo number_format($stats['total_customers']); ?></h3>
                            <p class="card-text">Total Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success"><?php echo number_format($stats['active_customers']); ?></h3>
                            <p class="card-text">Active (30 days)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info"><?php echo number_format($stats['new_this_month']); ?></h3>
                            <p class="card-text">New This Month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Customers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="customersTable">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                <?php echo strtoupper(substr($customer['full_name'], 0, 2)); ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($customer['full_name']); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $customer['total_orders']; ?></span>
                                    </td>
                                    <td>₹<?php echo number_format($customer['total_spent'], 2); ?></td>
                                    <td>
                                        <?php if ($customer['last_order_date']): ?>
                                            <?php echo date('M d, Y', strtotime($customer['last_order_date'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">No orders</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['total_orders'] > 0): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="viewCustomer(<?php echo $customer['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info" onclick="customerOrders(<?php echo $customer['id']; ?>)">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
function viewCustomer(customerId) {
    // Implementation for viewing customer details
    alert('View customer: ' + customerId);
}

function customerOrders(customerId) {
    // Redirect to orders page filtered by customer
    window.location.href = 'orders.php?customer=' + customerId;
}

function exportCustomers() {
    // Implementation for exporting customer data
    alert('Exporting customer data...');
}

// Initialize DataTable if available
if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('#customersTable').DataTable({
        pageLength: 25,
        order: [[2, 'desc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });
}
</script>

<?php include 'includes/footer.php'; ?>
