<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Product</h5>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Product Name *</label>
                                                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                                       name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required maxlength="255">
                                                <?php if (isset($errors['name'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">SKU *</label>
                                                <input type="text" class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>" 
                                                       name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required maxlength="50">
                                                <?php if (isset($errors['sku'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['sku']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" 
                                                          name="description" rows="4" maxlength="1000"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                                                <?php if (isset($errors['description'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing & Inventory -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Pricing & Inventory</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Price *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                                                    <input type="number" class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" 
                                                           name="price" value="<?php echo $product['price']; ?>" step="0.01" min="0" required>
                                                </div>
                                                <?php if (isset($errors['price'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['price']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Category *</label>
                                                <select class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" 
                                                        name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['id']; ?>" 
                                                                <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (isset($errors['category_id'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['category_id']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Stock Quantity *</label>
                                                <input type="number" class="form-control <?php echo isset($errors['stock_quantity']) ? 'is-invalid' : ''; ?>" 
                                                       name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" min="0" required>
                                                <?php if (isset($errors['stock_quantity'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['stock_quantity']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Reorder Level</label>
                                                <input type="number" class="form-control" 
                                                       name="reorder_level" value="<?php echo $product['reorder_level']; ?>" min="0">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Product Image -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Product Image</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($product['image']): ?>
                                            <div class="text-center mb-3">
                                                <img src="<?php echo UPLOAD_PATH . $product['image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="img-fluid rounded">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" 
                                               name="image" accept="image/*">
                                        <small class="text-muted d-block mt-2">
                                            Max file size: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?>MB
                                            <br>
                                            Allowed formats: <?php echo implode(', ', ALLOWED_IMAGES); ?>
                                        </small>
                                        <?php if (isset($errors['image'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Save Changes -->
                                <div class="card">
                                    <div class="card-body">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 