<?php
$page_title = "Electronics Product Management";
include 'includes/header.php';

// Handle product actions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                try {
                    $stmt = $pdo->prepare("INSERT INTO products (
                        name, brand, model, description, short_description, category, subcategory, sku,
                        price, original_price, stock_quantity, low_stock_threshold, weight, dimensions, color,
                        storage_capacity, ram, processor, display_size, battery_capacity, operating_system,
                        connectivity, warranty_period, features, specifications, included_items, tags
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $connectivity = !empty($_POST['connectivity']) ? json_encode(explode(',', $_POST['connectivity'])) : '[]';
                    $features = !empty($_POST['features']) ? json_encode(explode(',', $_POST['features'])) : '[]';
                    
                    $stmt->execute([
                        $_POST['name'], $_POST['brand'], $_POST['model'], $_POST['description'], $_POST['short_description'],
                        $_POST['category'], $_POST['subcategory'], $_POST['sku'], $_POST['price'], $_POST['original_price'],
                        $_POST['stock_quantity'], $_POST['low_stock_threshold'], $_POST['weight'], $_POST['dimensions'],
                        $_POST['color'], $_POST['storage_capacity'], $_POST['ram'], $_POST['processor'],
                        $_POST['display_size'], $_POST['battery_capacity'], $_POST['operating_system'],
                        $connectivity, $_POST['warranty_period'], $features, $_POST['specifications'],
                        $_POST['included_items'], $_POST['tags']
                    ]);
                    
                    $_SESSION['success'] = 'Electronics product added successfully!';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Failed to add product: ' . $e->getMessage();
                }
                break;
        }
        header("Location: products-electronics.php");
        exit();
    }
}

// Get products with electronics-specific filters
$search = $_GET['search'] ?? '';
$brand_filter = $_GET['brand'] ?? '';
$category_filter = $_GET['category'] ?? '';
$price_range = $_GET['price_range'] ?? '';

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR brand LIKE ? OR model LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($brand_filter) {
    $query .= " AND brand = ?";
    $params[] = $brand_filter;
}

if ($category_filter) {
    $query .= " AND category = ?";
    $params[] = $category_filter;
}

if ($price_range) {
    switch ($price_range) {
        case 'under_25000':
            $query .= " AND price < 25000";
            break;
        case '25000_50000':
            $query .= " AND price BETWEEN 25000 AND 50000";
            break;
        case '50000_100000':
            $query .= " AND price BETWEEN 50000 AND 100000";
            break;
        case 'over_100000':
            $query .= " AND price > 100000";
            break;
    }
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get filter options
$brands = $pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand")->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-microchip me-2"></i>Electronics Product Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-1"></i>Add Electronics Product
                    </button>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Advanced Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Advanced Filters</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search Products</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, brand, model...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Brand</label>
                            <select class="form-select" name="brand">
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo htmlspecialchars($brand['brand']); ?>" 
                                            <?php echo $brand_filter === $brand['brand'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['brand']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['category']); ?>" 
                                            <?php echo $category_filter === $category['category'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price Range</label>
                            <select class="form-select" name="price_range">
                                <option value="">All Prices</option>
                                <option value="under_25000" <?php echo $price_range === 'under_25000' ? 'selected' : ''; ?>>Under ₹25,000</option>
                                <option value="25000_50000" <?php echo $price_range === '25000_50000' ? 'selected' : ''; ?>>₹25,000 - ₹50,000</option>
                                <option value="50000_100000" <?php echo $price_range === '50000_100000' ? 'selected' : ''; ?>>₹50,000 - ₹1,00,000</option>
                                <option value="over_100000" <?php echo $price_range === 'over_100000' ? 'selected' : ''; ?>>Over ₹1,00,000</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Electronics Products (<?php echo count($products); ?> found)</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success">In Stock: <?php echo count(array_filter($products, fn($p) => $p['stock_quantity'] > $p['low_stock_threshold'])); ?></span>
                        <span class="badge bg-warning">Low Stock: <?php echo count(array_filter($products, fn($p) => $p['stock_quantity'] <= $p['low_stock_threshold'] && $p['stock_quantity'] > 0)); ?></span>
                        <span class="badge bg-danger">Out of Stock: <?php echo count(array_filter($products, fn($p) => $p['stock_quantity'] == 0)); ?></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Details</th>
                                    <th>Brand & Model</th>
                                    <th>Specifications</th>
                                    <th>Pricing</th>
                                    <th>Stock Status</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $product['image_primary']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <strong class="d-block"><?php echo htmlspecialchars($product['name']); ?></strong>
                                                <small class="text-muted"><?php echo htmlspecialchars($product['category']); ?></small>
                                                <?php if ($product['is_featured']): ?>
                                                    <span class="badge bg-primary ms-1">Featured</span>
                                                <?php endif; ?>
                                                <?php if ($product['is_bestseller']): ?>
                                                    <span class="badge bg-success ms-1">Bestseller</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['brand']); ?></strong>
                                        <?php if ($product['model']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($product['model']); ?></small>
                                        <?php endif; ?>
                                        <br><small class="text-info"><?php echo htmlspecialchars($product['sku']); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($product['storage_capacity']): ?>
                                                <div><i class="fas fa-hdd"></i> <?php echo htmlspecialchars($product['storage_capacity']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($product['ram']): ?>
                                                <div><i class="fas fa-memory"></i> <?php echo htmlspecialchars($product['ram']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($product['display_size']): ?>
                                                <div><i class="fas fa-tv"></i> <?php echo htmlspecialchars($product['display_size']); ?></div>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₹<?php echo number_format($product['price'], 2); ?></div>
                                        <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                            <small class="text-muted text-decoration-line-through">₹<?php echo number_format($product['original_price'], 2); ?></small>
                                            <small class="text-danger d-block"><?php echo number_format($product['discount_percentage'], 1); ?>% OFF</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold <?php echo $product['stock_quantity'] <= $product['low_stock_threshold'] ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo $product['stock_quantity']; ?> units
                                        </div>
                                        <?php if ($product['stock_quantity'] == 0): ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php elseif ($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                                            <span class="badge bg-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="text-warning me-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?php echo $i <= $product['rating'] ? '' : '-o'; ?>"></i>
                                                <?php endfor; ?>
                                            </span>
                                            <small class="text-muted">(<?php echo $product['review_count']; ?>)</small>
                                        </div>
                                        <small class="text-muted"><?php echo number_format($product['rating'], 1); ?>/5.0</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="Edit Product">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Delete Product">
                                                <i class="fas fa-trash"></i>
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

<!-- Add Electronics Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_product">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-microchip me-2"></i>Add Electronics Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-12 mb-3">
                            <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Brand *</label>
                            <select class="form-select" name="brand" required>
                                <option value="">Select Brand</option>
                                <option value="Apple">Apple</option>
                                <option value="Samsung">Samsung</option>
                                <option value="Google">Google</option>
                                <option value="Sony">Sony</option>
                                <option value="Dell">Dell</option>
                                <option value="LG">LG</option>
                                <option value="OnePlus">OnePlus</option>
                                <option value="Xiaomi">Xiaomi</option>
                                <option value="ASUS">ASUS</option>
                                <option value="HP">HP</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" name="model">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Smartphones">Smartphones</option>
                                <option value="Laptops">Laptops</option>
                                <option value="Tablets">Tablets</option>
                                <option value="Audio">Audio</option>
                                <option value="Wearables">Wearables</option>
                                <option value="Gaming">Gaming</option>
                                <option value="Monitors">Monitors</option>
                                <option value="Storage">Storage</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subcategory</label>
                            <input type="text" class="form-control" name="subcategory" placeholder="e.g., Premium Smartphones">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea class="form-control" name="short_description" rows="2" placeholder="Brief product description for listings"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Detailed Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Comprehensive product description"></textarea>
                        </div>

                        <!-- Pricing & Inventory -->
                        <div class="col-12 mb-3 mt-3">
                            <h6 class="text-primary"><i class="fas fa-rupee-sign me-2"></i>Pricing & Inventory</h6>
                            <hr>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Current Price (₹) *</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Original Price (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="original_price">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock_quantity" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold" value="5">
                        </div>

                        <!-- Technical Specifications -->
                        <div class="col-12 mb-3 mt-3">
                            <h6 class="text-primary"><i class="fas fa-cogs me-2"></i>Technical Specifications</h6>
                            <hr>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Storage Capacity</label>
                            <input type="text" class="form-control" name="storage_capacity" placeholder="e.g., 256GB, 1TB SSD">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">RAM</label>
                            <input type="text" class="form-control" name="ram" placeholder="e.g., 8GB, 16GB">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Processor</label>
                            <input type="text" class="form-control" name="processor" placeholder="e.g., A17 Pro, Intel i7">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Display Size</label>
                            <input type="text" class="form-control" name="display_size" placeholder="e.g., 6.7&quot;, 15.6&quot;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Battery Capacity</label>
                            <input type="text" class="form-control" name="battery_capacity" placeholder="e.g., 4441mAh, 100Wh">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Operating System</label>
                            <input type="text" class="form-control" name="operating_system" placeholder="e.g., iOS 17, Windows 11">
                        </div>

                        <!-- Physical Properties -->
                        <div class="col-12 mb-3 mt-3">
                            <h6 class="text-primary"><i class="fas fa-cube me-2"></i>Physical Properties</h6>
                            <hr>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.001" class="form-control" name="weight" placeholder="e.g., 0.221">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dimensions (L x W x H cm)</label>
                            <input type="text" class="form-control" name="dimensions" placeholder="e.g., 15.9 x 7.7 x 0.8">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" placeholder="e.g., Space Black">
                        </div>

                        <!-- Additional Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h6 class="text-primary"><i class="fas fa-plus-circle me-2"></i>Additional Information</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU *</label>
                            <input type="text" class="form-control" name="sku" required placeholder="e.g., APL-IP15PM-256-NT">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warranty Period (months)</label>
                            <input type="number" class="form-control" name="warranty_period" value="12">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Connectivity Options</label>
                            <input type="text" class="form-control" name="connectivity" placeholder="5G, WiFi 6E, Bluetooth 5.3, NFC (comma separated)">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Key Features</label>
                            <input type="text" class="form-control" name="features" placeholder="Titanium Design, A17 Pro Chip, 5x Telephoto Camera (comma separated)">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">What's Included</label>
                            <textarea class="form-control" name="included_items" rows="2" placeholder="List items included in the box"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Detailed Specifications (JSON)</label>
                            <textarea class="form-control" name="specifications" rows="3" placeholder='{"display": "6.7-inch Super Retina XDR", "camera": "48MP Main + 12MP Ultra Wide"}'></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-control" name="tags" placeholder="iPhone, Apple, smartphone, 5G, camera (comma separated)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Add Electronics Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
