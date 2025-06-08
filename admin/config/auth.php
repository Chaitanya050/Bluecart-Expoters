<?php
/**
 * Admin Authentication Functions
 */

/**
 * Check if user is logged in as admin
 * @return bool
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Alias for isAdminLoggedIn for backward compatibility
 */
function isAdmin() {
    return isAdminLoggedIn();
}

/**
 * Require admin login
 * Redirects to login page if not logged in as admin
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Log admin activity
 * @param PDO $pdo Database connection
 * @param int $user_id Admin user ID
 * @param string $action Action performed
 * @param string $details Additional details
 * @return bool
 */
function logAdminActivity($pdo, $user_id, $action, $details = '') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_activity_log (user_id, action, details, ip_address)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $user_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR']
        ]);
    } catch (PDOException $e) {
        error_log("Error logging admin activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Get admin user details
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @return array|false User details or false if not found
 */
function getAdminUser($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, full_name, email, role, last_login
            FROM users
            WHERE id = ? AND role = 'admin'
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getting admin user: " . $e->getMessage());
        return false;
    }
}

/**
 * Update admin last login
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @return bool
 */
function updateAdminLastLogin($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET last_login = CURRENT_TIMESTAMP
            WHERE id = ? AND role = 'admin'
        ");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log("Error updating admin last login: " . $e->getMessage());
        return false;
    }
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate admin password hash for anuj2311
// echo password_hash('anuj2311', PASSWORD_DEFAULT);
?>
