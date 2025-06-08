<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$product_id = $_POST['product_id'] ?? '';
$update_type = $_POST['update_type'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$reason = $_POST['reason'] ?? '';

if (!$product_id || !$update_type || !is_numeric($quantity)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Get current stock
    $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    $current_stock = $product['stock_quantity'];
    $new_stock = $current_stock;
    
    // Calculate new stock based on update type
    switch ($update_type) {
        case 'add':
            $new_stock = $current_stock + $quantity;
            $movement_type = 'in';
            break;
        case 'remove':
            $new_stock = max(0, $current_stock - $quantity);
            $movement_type = 'out';
            break;
        case 'set':
            $new_stock = $quantity;
            $movement_type = 'adjustment';
            break;
        default:
            throw new Exception('Invalid update type');
    }
    
    // Update product stock
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = ?, last_stock_update = NOW() WHERE id = ?");
    $stmt->execute([$new_stock, $product_id]);
    
    // Record stock movement
    $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product_id, $movement_type, abs($new_stock - $current_stock), $current_stock, $new_stock, $reason, $_SESSION['user_id']]);
    
    // Check if we need to create/resolve low stock alerts
    $stmt = $pdo->prepare("SELECT low_stock_threshold FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $threshold = $stmt->fetch()['low_stock_threshold'];
    
    if ($new_stock <= $threshold && $new_stock > 0) {
        // Create low stock alert if it doesn't exist
        $stmt = $pdo->prepare("INSERT IGNORE INTO low_stock_alerts (product_id, current_stock, threshold_level) VALUES (?, ?, ?)");
        $stmt->execute([$product_id, $new_stock, $threshold]);
    } else {
        // Resolve existing alerts
        $stmt = $pdo->prepare("UPDATE low_stock_alerts SET resolved_at = NOW() WHERE product_id = ? AND resolved_at IS NULL");
        $stmt->execute([$product_id]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
