<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$page_title = 'Inventory Management';

// Get filter parameters
$category_filter = $_GET['category'] ?? '';
$stock_filter = $_GET['stock_filter'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT p.*, 
          CASE 
            WHEN p.stock_quantity <= p.low_stock_threshold THEN 'low'
            WHEN p.stock_quantity = 0 THEN 'out'
            ELSE 'normal'
          END as stock_status
          FROM products p WHERE 1=1";

$params = [];

if ($category_filter) {
    $query .= " AND p.category = ?";
    $params[] = $category_filter;
}

if ($stock_filter) {
    switch ($stock_filter) {
        case 'low':
            $query .= " AND p.stock_quantity <= p.low_stock_threshold AND p.stock_quantity > 0";
            break;
        case 'out':
            $query .= " AND p.stock_quantity = 0";
            break;
        case 'normal':
            $query .= " AND p.stock_quantity > p.low_stock_threshold";
            break;
    }
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY p.stock_quantity ASC, p.name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories for filter
$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $categories_stmt->fetchAll();

// Get low stock alerts count
$alerts_stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity <= low_stock_threshold AND stock_quantity > 0");
$low_stock_count = $alerts_stmt->fetch()['count'];

// Get out of stock count
$out_stock_stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0");
$out_stock_count = $out_stock_stmt->fetch()['count'];

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Inventory Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                        <i class="fas fa-boxes"></i> Bulk Update Stock
                    </button>
                </div>
            </div>

            <!-- Inventory Alerts -->
            <?php if ($low_stock_count > 0 || $out_stock_count > 0): ?>
            <div class="row mb-4">
                <?php if ($low_stock_count > 0): ?>
                <div class="col-md-6">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Low Stock Alert!</strong> <?php echo $low_stock_count; ?> products are running low on stock.
                            <a href="?stock_filter=low" class="alert-link">View Low Stock Items</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($out_stock_count > 0): ?>
                <div class="col-md-6">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        <div>
                            <strong>Out of Stock!</strong> <?php echo $out_stock_count; ?> products are out of stock.
                            <a href="?stock_filter=out" class="alert-link">View Out of Stock Items</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Products</label>
                            <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Product name...">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['category']); ?>" 
                                            <?php echo $category_filter === $category['category'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_filter" class="form-label">Stock Status</label>
                            <select class="form-select" id="stock_filter" name="stock_filter">
                                <option value="">All Stock Levels</option>
                                <option value="normal" <?php echo $stock_filter === 'normal' ? 'selected' : ''; ?>>Normal Stock</option>
                                <option value="low" <?php echo $stock_filter === 'low' ? 'selected' : ''; ?>>Low Stock</option>
                                <option value="out" <?php echo $stock_filter === 'out' ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Products Inventory (<?php echo count($products); ?> items)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Low Stock Threshold</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/placeholder.svg?height=40&width=40" alt="<?php echo htmlspecialchars($product['name']); ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                <small class="text-muted">ID: <?php echo $product['id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td>
                                        <span class="fw-bold <?php echo $product['stock_status'] === 'out' ? 'text-danger' : ($product['stock_status'] === 'low' ? 'text-warning' : 'text-success'); ?>">
                                            <?php echo $product['stock_quantity']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $product['low_stock_threshold']; ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        switch ($product['stock_status']) {
                                            case 'out':
                                                $status_class = 'bg-danger';
                                                $status_text = 'Out of Stock';
                                                break;
                                            case 'low':
                                                $status_class = 'bg-warning';
                                                $status_text = 'Low Stock';
                                                break;
                                            default:
                                                $status_class = 'bg-success';
                                                $status_text = 'In Stock';
                                        }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($product['last_stock_update'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="updateStock(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['stock_quantity']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="viewHistory(<?php echo $product['id']; ?>)">
                                                <i class="fas fa-history"></i>
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

<!-- Stock Update Modal -->
<div class="modal fade" id="stockUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="productId" name="product_id">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="currentStock" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="updateType" class="form-label">Update Type</label>
                        <select class="form-select" id="updateType" name="update_type" required>
                            <option value="">Select update type</option>
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                            <option value="set">Set Exact Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Reason for stock update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Stock Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Upload a CSV file with columns: product_id, quantity, reason
                </div>
                <form id="bulkUpdateForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                    </div>
                    <div class="mb-3">
                        <label for="bulkUpdateType" class="form-label">Update Type</label>
                        <select class="form-select" id="bulkUpdateType" name="update_type" required>
                            <option value="add">Add to Current Stock</option>
                            <option value="set">Set Exact Stock</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkUpdate()">Upload & Update</button>
            </div>
        </div>
    </div>
</div>

<script>
function updateStock(productId, productName, currentStock) {
    document.getElementById('productId').value = productId;
    document.getElementById('productName').value = productName;
    document.getElementById('currentStock').value = currentStock;
    document.getElementById('updateType').value = '';
    document.getElementById('quantity').value = '';
    document.getElementById('reason').value = '';
    
    new bootstrap.Modal(document.getElementById('stockUpdateModal')).show();
}

function viewHistory(productId) {
    window.location.href = 'stock-history.php?product_id=' + productId;
}

document.getElementById('stockUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    fetch('actions/update-stock.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Create success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i> Stock updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert alert before the table
            const tableContainer = document.querySelector('.card-body.p-0').parentNode;
            tableContainer.parentNode.insertBefore(alertDiv, tableContainer);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('stockUpdateModal')).hide();
            
            // Reload page after a short delay
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating stock.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function submitBulkUpdate() {
    const formData = new FormData(document.getElementById('bulkUpdateForm'));
    const submitBtn = document.querySelector('#bulkUpdateModal .btn-primary');
    const originalText = submitBtn.innerHTML;
    
    // Validate file input
    const fileInput = document.getElementById('csvFile');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please select a CSV file');
        return;
    }
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    fetch('actions/bulk-update-stock.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Create success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert alert before the table
            const tableContainer = document.querySelector('.card-body.p-0').parentNode;
            tableContainer.parentNode.insertBefore(alertDiv, tableContainer);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('bulkUpdateModal')).hide();
            
            // Reload page after a short delay
            setTimeout(() => location.reload(), 1500);
        } else {
            let errorMessage = data.message;
            if (data.errors && data.errors.length > 0) {
                errorMessage += '\n\nDetails:\n' + data.errors.join('\n');
            }
            alert('Error: ' + errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during bulk update.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
</script>

<?php include 'includes/footer.php'; ?>
