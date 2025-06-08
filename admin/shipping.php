<?php
$page_title = "Shipping Management";
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_zone':
                $stmt = $pdo->prepare("INSERT INTO shipping_zones (zone_name, states, base_rate, per_kg_rate, free_shipping_threshold, delivery_days_min, delivery_days_max) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['zone_name'],
                    $_POST['states'],
                    $_POST['base_rate'],
                    $_POST['per_kg_rate'],
                    $_POST['free_shipping_threshold'],
                    $_POST['delivery_days_min'],
                    $_POST['delivery_days_max']
                ]);
                $_SESSION['success'] = 'Shipping zone added successfully!';
                break;
                
            case 'add_method':
                $stmt = $pdo->prepare("INSERT INTO shipping_methods (method_name, description, base_cost, per_kg_cost, delivery_days_min, delivery_days_max) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['method_name'],
                    $_POST['description'],
                    $_POST['base_cost'],
                    $_POST['per_kg_cost'],
                    $_POST['delivery_days_min'],
                    $_POST['delivery_days_max']
                ]);
                $_SESSION['success'] = 'Shipping method added successfully!';
                break;
                
            case 'update_zone':
                $stmt = $pdo->prepare("UPDATE shipping_zones SET zone_name=?, states=?, base_rate=?, per_kg_rate=?, free_shipping_threshold=?, delivery_days_min=?, delivery_days_max=?, is_active=? WHERE id=?");
                $stmt->execute([
                    $_POST['zone_name'],
                    $_POST['states'],
                    $_POST['base_rate'],
                    $_POST['per_kg_rate'],
                    $_POST['free_shipping_threshold'],
                    $_POST['delivery_days_min'],
                    $_POST['delivery_days_max'],
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['zone_id']
                ]);
                $_SESSION['success'] = 'Shipping zone updated successfully!';
                break;
        }
        header("Location: shipping.php");
        exit();
    }
}

// Get shipping zones and methods
$zones = $pdo->query("SELECT * FROM shipping_zones ORDER BY zone_name")->fetchAll();
$methods = $pdo->query("SELECT * FROM shipping_methods ORDER BY method_name")->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-shipping-fast me-2"></i>Shipping Management</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Shipping Zones -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Shipping Zones</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                                <i class="fas fa-plus me-1"></i>Add Zone
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Zone Name</th>
                                            <th>States/Areas</th>
                                            <th>Base Rate</th>
                                            <th>Per KG Rate</th>
                                            <th>Free Shipping</th>
                                            <th>Delivery Days</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($zones as $zone): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($zone['zone_name']); ?></strong></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo strlen($zone['states']) > 50 ? substr(htmlspecialchars($zone['states']), 0, 50) . '...' : htmlspecialchars($zone['states']); ?>
                                                </small>
                                            </td>
                                            <td>₹<?php echo number_format($zone['base_rate'], 2); ?></td>
                                            <td>₹<?php echo number_format($zone['per_kg_rate'], 2); ?></td>
                                            <td>₹<?php echo number_format($zone['free_shipping_threshold'], 2); ?>+</td>
                                            <td><?php echo $zone['delivery_days_min']; ?>-<?php echo $zone['delivery_days_max']; ?> days</td>
                                            <td>
                                                <span class="badge bg-<?php echo $zone['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $zone['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editZone(<?php echo htmlspecialchars(json_encode($zone)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
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

            <!-- Shipping Methods -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Shipping Methods</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMethodModal">
                                <i class="fas fa-plus me-1"></i>Add Method
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Method Name</th>
                                            <th>Description</th>
                                            <th>Base Cost</th>
                                            <th>Per KG Cost</th>
                                            <th>Delivery Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($methods as $method): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($method['method_name']); ?></strong></td>
                                            <td><small class="text-muted"><?php echo htmlspecialchars($method['description']); ?></small></td>
                                            <td>₹<?php echo number_format($method['base_cost'], 2); ?></td>
                                            <td>₹<?php echo number_format($method['per_kg_cost'], 2); ?></td>
                                            <td><?php echo $method['delivery_days_min']; ?>-<?php echo $method['delivery_days_max']; ?> days</td>
                                            <td>
                                                <span class="badge bg-<?php echo $method['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $method['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editMethod(<?php echo htmlspecialchars(json_encode($method)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
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
        </main>
    </div>
</div>

<!-- Add Zone Modal -->
<div class="modal fade" id="addZoneModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_zone">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipping Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Zone Name *</label>
                            <input type="text" class="form-control" name="zone_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">States/Areas *</label>
                            <textarea class="form-control" name="states" rows="2" placeholder="Comma separated list" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Base Rate (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="base_rate" value="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Per KG Rate (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="per_kg_rate" value="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Free Shipping Threshold (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="free_shipping_threshold" value="500.00">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Min Delivery Days</label>
                            <input type="number" class="form-control" name="delivery_days_min" value="1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Max Delivery Days</label>
                            <input type="number" class="form-control" name="delivery_days_max" value="3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Zone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Method Modal -->
<div class="modal fade" id="addMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_method">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipping Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Method Name *</label>
                        <input type="text" class="form-control" name="method_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Base Cost (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="base_cost" value="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Per KG Cost (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="per_kg_cost" value="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Delivery Days</label>
                            <input type="number" class="form-control" name="delivery_days_min" value="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Delivery Days</label>
                            <input type="number" class="form-control" name="delivery_days_max" value="7">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Method</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Zone Modal -->
<div class="modal fade" id="editZoneModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editZoneForm">
                <input type="hidden" name="action" value="update_zone">
                <input type="hidden" name="zone_id" id="edit_zone_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Shipping Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Zone Name *</label>
                            <input type="text" class="form-control" name="zone_name" id="edit_zone_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">States/Areas *</label>
                            <textarea class="form-control" name="states" id="edit_states" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Base Rate (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="base_rate" id="edit_base_rate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Per KG Rate (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="per_kg_rate" id="edit_per_kg_rate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Free Shipping Threshold (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="free_shipping_threshold" id="edit_free_shipping_threshold">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Min Delivery Days</label>
                            <input type="number" class="form-control" name="delivery_days_min" id="edit_delivery_days_min">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Max Delivery Days</label>
                            <input type="number" class="form-control" name="delivery_days_max" id="edit_delivery_days_max">
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Zone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editZone(zone) {
    document.getElementById('edit_zone_id').value = zone.id;
    document.getElementById('edit_zone_name').value = zone.zone_name;
    document.getElementById('edit_states').value = zone.states;
    document.getElementById('edit_base_rate').value = zone.base_rate;
    document.getElementById('edit_per_kg_rate').value = zone.per_kg_rate;
    document.getElementById('edit_free_shipping_threshold').value = zone.free_shipping_threshold;
    document.getElementById('edit_delivery_days_min').value = zone.delivery_days_min;
    document.getElementById('edit_delivery_days_max').value = zone.delivery_days_max;
    document.getElementById('edit_is_active').checked = zone.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editZoneModal')).show();
}

function editMethod(method) {
    // Similar function for editing methods
    console.log('Edit method:', method);
}
</script>

<?php include 'includes/footer.php'; ?>
