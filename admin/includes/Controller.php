<?php
abstract class Controller {
    protected $db;
    protected $auth;
    protected $data = [];
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/Auth.php';
        
        $this->db = Database::getInstance();
        $this->auth = new Auth();
        $this->auth->requireLogin();
        
        // Set common view data
        $this->data['admin'] = $this->auth->getAdminDetails();
        $this->data['page_title'] = '';
        $this->data['success'] = $_SESSION['success'] ?? null;
        $this->data['error'] = $_SESSION['error'] ?? null;
        
        // Clear flash messages
        unset($_SESSION['success'], $_SESSION['error']);
    }
    
    protected function view($template, $data = []) {
        // Merge controller data with view data
        $data = array_merge($this->data, $data);
        extract($data);
        
        require_once __DIR__ . '/../includes/header.php';
        require_once __DIR__ . "/../views/{$template}.php";
        require_once __DIR__ . '/../includes/footer.php';
    }
    
    protected function redirect($url, $message = '', $type = 'success') {
        if ($message) {
            $_SESSION[$type] = $message;
        }
        header("Location: {$url}");
        exit();
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    protected function uploadFile($file, $destination) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_IMAGES)) {
            throw new RuntimeException('Invalid file format.');
        }
        
        $filename = sprintf(
            '%s-%s.%s',
            uniqid(),
            bin2hex(random_bytes(8)),
            $extension
        );
        
        if (!move_uploaded_file($file['tmp_name'], $destination . $filename)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
        
        return $filename;
    }
    
    protected function validateInput($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = ucfirst($field) . ' is required.';
                continue;
            }
            
            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = 'Invalid email format.';
            }
            
            if (strpos($rule, 'min:') !== false) {
                preg_match('/min:(\d+)/', $rule, $matches);
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = ucfirst($field) . " must be at least {$min} characters.";
                }
            }
            
            if (strpos($rule, 'max:') !== false) {
                preg_match('/max:(\d+)/', $rule, $matches);
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = ucfirst($field) . " must not exceed {$max} characters.";
                }
            }
        }
        
        return $errors;
    }
    
    protected function formatDate($date, $format = null) {
        if (!$format) {
            $format = DATETIME_FORMAT;
        }
        return date($format, strtotime($date));
    }
    
    protected function formatCurrency($amount) {
        return CURRENCY_SYMBOL . number_format($amount, DECIMAL_PLACES);
    }
    
    protected function getPagination($total, $page = 1, $limit = null) {
        if (!$limit) {
            $limit = ITEMS_PER_PAGE;
        }
        
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset,
            'total' => $total,
            'total_pages' => $totalPages
        ];
    }
} 