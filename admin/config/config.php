<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'bluecart');
define('DB_USER', 'root');
define('DB_PASS', '');

// Admin Configuration
define('ADMIN_TITLE', 'BlueCrate Admin');
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('ADMIN_EMAIL', 'admin@bluecrate.com');

// File Upload Configuration
define('UPLOAD_PATH', '../uploads/');
define('ALLOWED_IMAGES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5242880); // 5MB

// Pagination Configuration
define('ITEMS_PER_PAGE', 10);

// Currency Configuration
define('CURRENCY_SYMBOL', '₹');
define('DECIMAL_PLACES', 2);

// Shipping Configuration
define('DEFAULT_SHIPPING_COST', 100);
define('FREE_SHIPPING_THRESHOLD', 1000);

// Date & Time Configuration
define('TIMEZONE', 'Asia/Kolkata');
date_default_timezone_set(TIMEZONE);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize session variables if not set
if (isset($_SESSION['admin_id'])) {
    $_SESSION['user_id'] = $_SESSION['user_id'] ?? $_SESSION['admin_id'];
    $_SESSION['admin_name'] = $_SESSION['admin_name'] ?? 'Admin';
    $_SESSION['user_name'] = $_SESSION['user_name'] ?? $_SESSION['admin_name'];
    $_SESSION['role'] = $_SESSION['role'] ?? 'admin';
}

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper Functions
function formatCurrency($amount) {
    $amount = $amount ?? 0; // Handle null values
    return CURRENCY_SYMBOL . number_format($amount, DECIMAL_PLACES);
}

function formatDate($date) {
    return date('d M Y, h:i A', strtotime($date));
}



function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit();
    }
}
?>