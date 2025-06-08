<?php
require_once __DIR__ . '/../includes/Controller.php';

class ProductController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->data['page_title'] = 'Product Management';
    }
    
    public function index() {
        // Get pagination parameters
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        
        // Build query
        $where = [];
        $params = [];
        
        if ($search) {
            $where[] = "(name LIKE ? OR description LIKE ? OR sku LIKE ?)";
            $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%"]);
        }
        
        if ($category) {
            $where[] = "category_id = ?";
            $params[] = $category;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Get total count for pagination
        $sql = "SELECT COUNT(*) FROM products {$whereClause}";
        $total = $this->db->query($sql, $params)->fetchColumn();
        
        // Get pagination data
        $pagination = $this->getPagination($total, $page);
        
        // Get products
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                {$whereClause} 
                ORDER BY p.name 
                LIMIT {$pagination['limit']} 
                OFFSET {$pagination['offset']}";
        
        $products = $this->db->query($sql, $params)->fetchAll();
        
        // Get categories for filter
        $categories = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
        
        $this->data['products'] = $products;
        $this->data['categories'] = $categories;
        $this->data['pagination'] = $pagination;
        $this->data['search'] = $search;
        $this->data['selected_category'] = $category;
        
        $this->view('products/index');
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rules = [
                'name' => 'required|max:255',
                'sku' => 'required|max:50',
                'price' => 'required',
                'category_id' => 'required',
                'description' => 'max:1000'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            if (empty($errors)) {
                try {
                    // Handle image upload
                    $image = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        try {
                            $image = $this->uploadFile($_FILES['image'], UPLOAD_PATH);
                        } catch (RuntimeException $e) {
                            $errors['image'] = $e->getMessage();
                        }
                    }
                    
                    if (empty($errors)) {
                        $data = [
                            'name' => trim($_POST['name']),
                            'sku' => trim($_POST['sku']),
                            'description' => trim($_POST['description'] ?? ''),
                            'price' => floatval($_POST['price']),
                            'category_id' => intval($_POST['category_id']),
                            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
                            'reorder_level' => intval($_POST['reorder_level'] ?? 0),
                            'image' => $image,
                            'status' => $_POST['status'] ?? 'active',
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $this->db->insert('products', $data);
                        $this->redirect('products.php', 'Product added successfully');
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $errors['sku'] = 'This SKU already exists.';
                    } else {
                        throw $e;
                    }
                }
            }
            
            $this->data['errors'] = $errors;
            $this->data['old'] = $_POST;
        }
        
        // Get categories for dropdown
        $this->data['categories'] = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
        
        $this->view('products/create');
    }
    
    public function edit($id) {
        $product = $this->db->query("SELECT * FROM products WHERE id = ?", [$id])->fetch();
        
        if (!$product) {
            $this->redirect('products.php', 'Product not found', 'error');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rules = [
                'name' => 'required|max:255',
                'sku' => 'required|max:50',
                'price' => 'required',
                'category_id' => 'required',
                'description' => 'max:1000'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            if (empty($errors)) {
                try {
                    // Handle image upload
                    $image = $product['image'];
                    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        try {
                            $newImage = $this->uploadFile($_FILES['image'], UPLOAD_PATH);
                            // Delete old image if exists
                            if ($image && file_exists(UPLOAD_PATH . $image)) {
                                unlink(UPLOAD_PATH . $image);
                            }
                            $image = $newImage;
                        } catch (RuntimeException $e) {
                            $errors['image'] = $e->getMessage();
                        }
                    }
                    
                    if (empty($errors)) {
                        $data = [
                            'name' => trim($_POST['name']),
                            'sku' => trim($_POST['sku']),
                            'description' => trim($_POST['description'] ?? ''),
                            'price' => floatval($_POST['price']),
                            'category_id' => intval($_POST['category_id']),
                            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
                            'reorder_level' => intval($_POST['reorder_level'] ?? 0),
                            'image' => $image,
                            'status' => $_POST['status'] ?? 'active',
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $this->db->update('products', $data, ['id' => $id]);
                        $this->redirect('products.php', 'Product updated successfully');
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $errors['sku'] = 'This SKU already exists.';
                    } else {
                        throw $e;
                    }
                }
            }
            
            $this->data['errors'] = $errors;
            $this->data['product'] = array_merge($product, $_POST);
        } else {
            $this->data['product'] = $product;
        }
        
        // Get categories for dropdown
        $this->data['categories'] = $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
        
        $this->view('products/edit');
    }
    
    public function delete($id) {
        $product = $this->db->query("SELECT * FROM products WHERE id = ?", [$id])->fetch();
        
        if (!$product) {
            $this->redirect('products.php', 'Product not found', 'error');
        }
        
        // Check if product has any orders
        $sql = "SELECT COUNT(*) FROM order_items WHERE product_id = ?";
        $count = $this->db->query($sql, [$id])->fetchColumn();
        
        if ($count > 0) {
            $this->redirect('products.php', 'Cannot delete product: It has associated orders', 'error');
        }
        
        // Delete product image if exists
        if ($product['image'] && file_exists(UPLOAD_PATH . $product['image'])) {
            unlink(UPLOAD_PATH . $product['image']);
        }
        
        $this->db->delete('products', ['id' => $id]);
        $this->redirect('products.php', 'Product deleted successfully');
    }
    
    public function search() {
        $term = $_GET['term'] ?? '';
        $sql = "SELECT id, name, sku, price FROM products WHERE name LIKE ? OR sku LIKE ? ORDER BY name LIMIT 10";
        $params = ["%{$term}%", "%{$term}%"];
        $products = $this->db->query($sql, $params)->fetchAll();
        $this->json($products);
    }
    
    public function updateStock() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('products.php', 'Invalid request', 'error');
        }
        
        $id = $_POST['id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        
        if (!$id || !is_numeric($quantity)) {
            $this->redirect('products.php', 'Invalid parameters', 'error');
        }
        
        $data = ['stock_quantity' => $quantity];
        $this->db->update('products', $data, ['id' => $id]);
        
        $this->redirect('products.php', 'Stock updated successfully');
    }
} 