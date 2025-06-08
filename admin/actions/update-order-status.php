<?php
session_start();
require_once '../config/auth.php';
require_once '../config/whatsapp.php';

// Database connection
$host = 'localhost';
$db = 'bluecart'; // FIXED: Correct database name
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check admin authentication
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    $send_notification = $_POST['send_notification'] ?? false;

    if (empty($order_id) || empty($new_status)) {
        echo json_encode(['error' => 'Order ID and status are required']);
        exit;
    }

    try {
        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);

        // Get order and customer details for notification
        if ($send_notification) {
            $stmt = $pdo->prepare("
                SELECT o.*, u.name as customer_name, u.email, u.phone 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = ?
            ");
            $stmt->execute([$order_id]);
            $orderData = $stmt->fetch();

            if ($orderData && !empty($orderData['phone'])) {
                $whatsapp = new WhatsAppNotifier();
                $notificationResult = $whatsapp->sendOrderStatusUpdate($orderData, $new_status);

                // Log notification
                $notification_status = $notificationResult['success'] ? 'sent' : 'failed';
                $stmt = $pdo->prepare("INSERT INTO notification_log (order_id, type, status, details) VALUES (?, 'whatsapp_customer', ?, ?)");
                $stmt->execute([$order_id, $notification_status, json_encode($notificationResult)]);
            }
        }

        // Log admin activity
        logAdminActivity($pdo, $_SESSION['user_id'], 'order_status_update', "Updated order #{$order_id} to {$new_status}");

        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully',
            'notification_sent' => $send_notification
        ]);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to update order status: ' . $e->getMessage()]);
    }
}
?>
