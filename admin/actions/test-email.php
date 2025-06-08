<?php
session_start();
require_once '../config/auth.php';
require_once '../config/email.php';

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
                'name' => 'Test Product - iPhone 15 Pro',
                'quantity' => 1,
                'price' => '999.00',
                'subtotal' => '999.00'
            ],
            [
                'name' => 'Test Accessory - Phone Case',
                'quantity' => 2,
                'price' => '25.00',
                'subtotal' => '50.00'
            ]
        ],
        'shipping_address' => 'Test Address, Test City, Test State - 123456'
    ];

    $emailNotifier = new EmailNotifier();
    $result = $emailNotifier->sendOrderNotification($testOrderData);

    echo json_encode([
        'success' => $result['success'],
        'message' => $result['success'] ? 'Test email sent successfully!' : 'Failed to send test email',
        'details' => $result
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
