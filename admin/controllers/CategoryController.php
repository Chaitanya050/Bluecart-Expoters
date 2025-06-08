<?php
require_once __DIR__ . '/../includes/Controller.php';

class CategoryController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->data['page_title'] = 'Category Management';
    }
    
    public function index() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $categories = $this->db->query($sql)->fetchAll();
        
        // Get product counts for each category
        foreach ($categories as &$category) {
            $sql = "SELECT COUNT(*) FROM products WHERE category_id = ?";
            $count = $this->db->query($sql, [$category['id']])->fetchColumn();
            $category['product_count'] = $count;
        }
        
        $this->data['categories'] = $categories;
        $this->view('categories/index');
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rules = [
                'name' => 'required|max:255',
                'description' => 'max:1000'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            if (empty($errors)) {
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                try {
                    $this->db->insert('categories', $data);
                    $this->redirect('categories.php', 'Category added successfully');
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { // Duplicate entry
                        $errors['name'] = 'A category with this name already exists.';
                    } else {
                        throw $e;
                    }
                }
            }
            
            $this->data['errors'] = $errors;
            $this->data['old'] = $_POST;
        }
        
        $this->view('categories/create');
    }
    
    public function edit($id) {
        $category = $this->db->query("SELECT * FROM categories WHERE id = ?", [$id])->fetch();
        
        if (!$category) {
            $this->redirect('categories.php', 'Category not found', 'error');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rules = [
                'name' => 'required|max:255',
                'description' => 'max:1000'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            if (empty($errors)) {
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                try {
                    $this->db->update('categories', $data, ['id' => $id]);
                    $this->redirect('categories.php', 'Category updated successfully');
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { // Duplicate entry
                        $errors['name'] = 'A category with this name already exists.';
                    } else {
                        throw $e;
                    }
                }
            }
            
            $this->data['errors'] = $errors;
            $this->data['category'] = array_merge($category, $_POST);
        } else {
            $this->data['category'] = $category;
        }
        
        $this->view('categories/edit');
    }
    
    public function delete($id) {
        // Check if category has products
        $sql = "SELECT COUNT(*) FROM products WHERE category_id = ?";
        $count = $this->db->query($sql, [$id])->fetchColumn();
        
        if ($count > 0) {
            $this->redirect('categories.php', 'Cannot delete category: It has associated products', 'error');
        }
        
        $this->db->delete('categories', ['id' => $id]);
        $this->redirect('categories.php', 'Category deleted successfully');
    }
    
    public function search() {
        $term = $_GET['term'] ?? '';
        $sql = "SELECT * FROM categories WHERE name LIKE ? ORDER BY name LIMIT 10";
        $categories = $this->db->query($sql, ["%{$term}%"])->fetchAll();
        $this->json($categories);
    }
} 