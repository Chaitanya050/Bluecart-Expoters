<?php
class Auth {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance();
    }
    
    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
        $stmt = $this->db->query($sql, [$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['user_id'] = $admin['id']; 
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['role'] = 'admin';
            $_SESSION['last_activity'] = time();
            
            // Log the login activity
            $this->logActivity('login', 'Admin logged in');
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        // Log the logout activity
        if (isset($_SESSION['admin_id'])) {
            $this->logActivity('logout', 'Admin logged out');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            $this->logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: login.php');
            exit();
        }
    }
    
    private function logActivity($action, $description) {
        $data = [
            'admin_id' => $_SESSION['admin_id'],
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('admin_activity_log', $data);
    }
    
    public function changePassword($currentPassword, $newPassword) {
        $sql = "SELECT password FROM admins WHERE id = ?";
        $stmt = $this->db->query($sql, [$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if (!$admin || !password_verify($currentPassword, $admin['password'])) {
            return false;
        }
        
        $data = ['password' => password_hash($newPassword, PASSWORD_DEFAULT)];
        $where = ['id' => $_SESSION['admin_id']];
        
        if ($this->db->update('admins', $data, $where)) {
            $this->logActivity('password_change', 'Admin changed password');
            return true;
        }
        
        return false;
    }
    
    public function getAdminDetails() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = ? AND role = 'admin'";
        $stmt = $this->db->query($sql, [$_SESSION['admin_id']]);
        return $stmt->fetch();
    }
}