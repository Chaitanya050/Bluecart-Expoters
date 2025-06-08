<?php
/**
 * CSRF Protection Functions
 */

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token input field
 * @return string HTML input field
 */
function generateCSRFInput() {
    $token = generateCSRFToken();
    return "<input type='hidden' name='_csrf' value='$token'>";
}

/**
 * Verify request with CSRF protection
 * @param string $method HTTP method
 * @return bool
 */
function verifyCSRFRequest($method = 'POST') {
    if ($method === 'POST') {
        return isset($_POST['_csrf']) && verifyCSRFToken($_POST['_csrf']);
    } elseif ($method === 'GET') {
        return isset($_GET['_csrf']) && verifyCSRFToken($_GET['_csrf']);
    }
    return false;
}

/**
 * Add CSRF protection to all forms
 */
function addCSRFProtection() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Add token to meta tag for AJAX requests
    echo "<meta name='csrf-token' content='" . $_SESSION['csrf_token'] . "'>";
    
    // Add token to all forms
    add_action('form_start', function($form) {
        $form->add(generateCSRFInput());
    });
}

/**
 * Add CSRF protection middleware
 */
function csrfMiddleware() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCSRFRequest('POST')) {
            http_response_code(403);
            die('CSRF token verification failed');
        }
    }
}

// Initialize CSRF protection
addCSRFProtection();
