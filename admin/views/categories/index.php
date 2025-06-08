<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Categories</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-1"></i> Add New Category
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Products</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo $category['product_count']; ?> products
                                        </span>
                                    </td>
                                    <td><?php echo $this->formatDate($category['created_at'], 'Y-m-d'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-category" 
                                                data-id="<?php echo $category['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                data-description="<?php echo htmlspecialchars($category['description'] ?? ''); ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editCategoryModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($category['product_count'] == 0): ?>
                                        <button type="button" class="btn btn-sm btn-danger delete-category"
                                                data-id="<?php echo $category['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($category['name']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="categories.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="categories.php">
                <input type="hidden" name="id" id="edit_category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" id="edit_category_name" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_category_description" rows="3" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    const table = new DataTable('#categoriesTable', {
        pageLength: 10,
        order: [[0, 'asc']]
    });
    
    // Handle edit category
    document.querySelectorAll('.edit-category').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_category_id').value = this.dataset.id;
            document.getElementById('edit_category_name').value = this.dataset.name;
            document.getElementById('edit_category_description').value = this.dataset.description;
        });
    });
    
    // Handle delete category
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            if (confirm(`Are you sure you want to delete the category "${name}"?`)) {
                window.location.href = `categories.php?action=delete&id=${id}`;
            }
        });
    });
});
</script> 