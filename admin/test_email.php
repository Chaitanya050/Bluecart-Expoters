<?php
require_once __DIR__ . '/config/email.php';

// Create email notifier instance
$emailNotifier = new EmailNotifier();

// Test order data
$testOrderData = [
    'order_id' => 'TEST-001',
    'order_date' => date('Y-m-d H:i:s'),
    'customer_name' => 'Test Customer',
    'customer_email' => 'khatikanuj914@gmail.com',
    'customer_phone' => '1234567890',
    'total_amount' => '999.99',
    'shipping_address' => "Test Address\nTest City\nTest State\nTest PIN",
    'items' => [
        [
            'name' => 'Test Product',
            'quantity' => 1,
            'price' => '999.99',
            'subtotal' => '999.99'
        ]
    ]
];

// Send test email
$result = $emailNotifier->sendOrderNotification($testOrderData);

// Output result
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?> 