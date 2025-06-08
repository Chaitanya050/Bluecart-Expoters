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

// Check if file was uploaded
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$update_type = $_POST['update_type'] ?? '';

if (!in_array($update_type, ['add', 'set'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid update type']);
    exit();
}

// Process CSV file
try {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');
    
    if (!$handle) {
        throw new Exception('Could not open file');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Skip header row
    $header = fgetcsv($handle);
    
    $updated = 0;
    $errors = [];
    
    while (($data = fgetcsv($handle)) !== false) {
        // Expect: product_id, quantity, reason
        if (count($data) < 3) {
            $errors[] = 'Row has insufficient columns: ' . implode(',', $data);
            continue;
        }
        
        $product_id = trim($data[0]);
        $quantity = trim($data[1]);
        $reason = trim($data[2]);
        
        if (!is_numeric($product_id) || !is_numeric($quantity)) {
            $errors[] = "Invalid data format for product ID: {$product_id}";
            continue;
        }
        
        // Get current stock
        $stmt = $pdo->prepare("SELECT stock_quantity, low_stock_threshold FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $errors[] = "Product not found: ID {$product_id}";
            continue;
        }
        
        $current_stock = $product['stock_quantity'];
        $new_stock = $current_stock;
        
        // Calculate new stock based on update type
        if ($update_type === 'add') {
            $new_stock = $current_stock + $quantity;
            $movement_type = 'in';
        } else { // set
            $new_stock = $quantity;
            $movement_type = 'adjustment';
        }
        
        // Update product stock
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = ?, last_stock_update = NOW() WHERE id = ?");
        $stmt->execute([$new_stock, $product_id]);
        
        // Record stock movement
        $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $movement_type, abs($new_stock - $current_stock), $current_stock, $new_stock, $reason, $_SESSION['user_id']]);
        
        // Check if we need to create/resolve low stock alerts
        $threshold = $product['low_stock_threshold'];
        
        if ($new_stock <= $threshold && $new_stock > 0) {
            // Create low stock alert if it doesn't exist
            $stmt = $pdo->prepare("INSERT IGNORE INTO low_stock_alerts (product_id, current_stock, threshold_level) VALUES (?, ?, ?)");
            $stmt->execute([$product_id, $new_stock, $threshold]);
        } else {
            // Resolve existing alerts
            $stmt = $pdo->prepare("UPDATE low_stock_alerts SET resolved_at = NOW() WHERE product_id = ? AND resolved_at IS NULL");
            $stmt->execute([$product_id]);
        }
        
        $updated++;
    }
    
    fclose($handle);
    
    if ($updated > 0) {
        $pdo->commit();
        echo json_encode([
            'success' => true, 
            'message' => "{$updated} products updated successfully",
            'errors' => $errors
        ]);
    } else {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'No products were updated', 'errors' => $errors]);
    }
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>