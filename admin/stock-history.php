<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['product_id'] ?? '';
if (!$product_id) {
    header('Location: inventory.php');
    exit();
}

$page_title = 'Stock History';

// Get product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: inventory.php');
    exit();
}

// Get stock movements
$stmt = $pdo->prepare("
    SELECT sm.*, u.full_name as admin_name 
    FROM stock_movements sm 
    LEFT JOIN users u ON sm.admin_id = u.id 
    WHERE sm.product_id = ? 
    ORDER BY sm.created_at DESC
");
$stmt->execute([$product_id]);
$movements = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Stock History</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="inventory.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>
            </div>

            <!-- Product Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="mb-0">
                                <strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?> |
                                <strong>Current Stock:</strong> 
                                <span class="badge <?php echo $product['stock_quantity'] <= $product['low_stock_threshold'] ? 'bg-warning' : 'bg-success'; ?>">
                                    <?php echo $product['stock_quantity']; ?>
                                </span> |
                                <strong>Low Stock Threshold:</strong> <?php echo $product['low_stock_threshold']; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <img src="/placeholder.svg?height=100&width=100" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Movements -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stock Movement History</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($movements)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No stock movements recorded for this product.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Previous Stock</th>
                                    <th>New Stock</th>
                                    <th>Reason</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movements as $movement): ?>
                                <tr>
                                    <td>
                                        <small>
                                            <?php echo date('M j, Y', strtotime($movement['created_at'])); ?><br>
                                            <?php echo date('g:i A', strtotime($movement['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $type_class = '';
                                        $type_icon = '';
                                        $type_text = '';
                                        switch ($movement['movement_type']) {
                                            case 'in':
                                                $type_class = 'text-success';
                                                $type_icon = 'fas fa-plus-circle';
                                                $type_text = 'Stock In';
                                                break;
                                            case 'out':
                                                $type_class = 'text-danger';
                                                $type_icon = 'fas fa-minus-circle';
                                                $type_text = 'Stock Out';
                                                break;
                                            case 'adjustment':
                                                $type_class = 'text-warning';
                                                $type_icon = 'fas fa-edit';
                                                $type_text = 'Adjustment';
                                                break;
                                        }
                                        ?>
                                        <span class="<?php echo $type_class; ?>">
                                            <i class="<?php echo $type_icon; ?>"></i> <?php echo $type_text; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold <?php echo $movement['movement_type'] === 'in' ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $movement['movement_type'] === 'in' ? '+' : '-'; ?><?php echo $movement['quantity']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $movement['previous_stock']; ?></td>
                                    <td><?php echo $movement['new_stock']; ?></td>
                                    <td>
                                        <?php if ($movement['reason']): ?>
                                            <small><?php echo htmlspecialchars($movement['reason']); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">No reason provided</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo $movement['admin_name'] ? htmlspecialchars($movement['admin_name']) : 'System'; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
