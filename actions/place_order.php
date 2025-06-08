<?php
session_start();
require_once '../admin/config/email.php';

// Database connection details
$host = 'localhost';
$db = 'bluecart';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get cart items from database
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.user_id, c.product_id, c.quantity,
           p.name, p.price as product_price, p.stock_quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if cart is empty
if (empty($cart_items)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['product_price'] * $item['quantity'];
}

// Start transaction
try {
    $pdo->beginTransaction();

    // Insert order into orders table
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())");
    $stmt->execute([$user_id, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Insert order items into order_items table
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, product_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['product_price']]);
    }

    // Reduce stock quantities for ordered items
    foreach ($cart_items as $item) {
        // Check if enough stock is available
        if ($item['stock_quantity'] < $item['quantity']) {
            throw new Exception("Not enough stock available for product: " . $item['name']);
        }

        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
        
        // Record stock movement
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
        $stmt->execute([$item['product_id']]);
        $new_stock = $stmt->fetch()['stock_quantity'];
        
        $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason) VALUES (?, 'out', ?, ?, ?, ?)");
        $stmt->execute([
            $item['product_id'], 
            $item['quantity'], 
            $new_stock + $item['quantity'], 
            $new_stock,
            'Order #' . $order_id
        ]);
    }

    // Get customer details for email notification
    $stmt = $pdo->prepare("SELECT full_name as name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $customer = $stmt->fetch();

    // Prepare order data for email notification
    $orderData = [
        'order_id' => $order_id,
        'customer_name' => $customer['name'],
        'customer_email' => $customer['email'],
        'customer_phone' => $_POST['customer_phone'] ?? '',
        'order_date' => date('Y-m-d H:i:s'),
        'total_amount' => number_format($total_amount, 2),
        'items' => [],
        'shipping_address' => $_POST['shipping_address'] ?? 'Address not provided'
    ];

    // Format items for email
    foreach ($cart_items as $item) {
        $orderData['items'][] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => number_format($item['product_price'], 2),
            'subtotal' => number_format($item['product_price'] * $item['quantity'], 2)
        ];
    }

    // Send email notification to admin
    $emailNotifier = new EmailNotifier();
    $notificationResult = $emailNotifier->sendOrderNotification($orderData);

    // Log the notification attempt
    $notification_status = isset($notificationResult['success']) && $notificationResult['success'] ? 'sent' : 'failed';
    $stmt = $pdo->prepare("INSERT INTO notification_log (order_id, type, status, details) VALUES (?, 'email_admin', ?, ?)");
    $stmt->execute([$order_id, $notification_status, json_encode($notificationResult)]);

    // Clear the user's cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Commit transaction
    $pdo->commit();

    // Store order details in session for the success page
    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'total_amount' => number_format($total_amount, 2),
        'email_notification' => $notification_status
    ];

    // Return success response with redirect URL
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'redirect' => '/newblue/views/order_success.php?order_id=' . $order_id . '&total_amount=' . number_format($total_amount, 2),
        'order_id' => $order_id,
        'email_notification' => $notification_status,
        'notification_details' => $notificationResult['message'] ?? 'Unknown status'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Order placement failed: ' . $e->getMessage()]);
}
?>
