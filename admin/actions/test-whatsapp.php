<?php
session_start();
require_once '../config/auth.php';
require_once '../config/whatsapp.php';

// Check admin authentication
requireAdminLogin();

header('Content-Type: application/json');

try {
    $testOrderData = [
        'order_id' => 'TEST123',
        'customer_name' => 'Test Customer',
        'customer_email' => 'test@example.com',
        'customer_phone' => '9876543210',
        'order_date' => date('Y-m-d H:i:s'),
        'total_amount' => '999.00',
        'items' => [
            [
                'name' => 'Test Product',
                'quantity' => 1,
                'price' => '999.00',
                'subtotal' => '999.00'
            ]
        ],
        'shipping_address' => 'Test Address, Test City, Test State - 123456'
    ];

    $whatsapp = new WhatsAppNotifier();
    $result = $whatsapp->sendOrderNotification($testOrderData);

    echo json_encode([
        'success' => $result['success'],
        'message' => $result['success'] ? 'Test message sent successfully!' : 'Failed to send test message',
        'details' => $result
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
