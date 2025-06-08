<?php
require_once __DIR__ . '/admin/config/email.php';

// Sample order data
$orderData = [
    'order_id' => '12345',
    'customer_name' => 'Test Customer',
    'customer_email' => 'khatikanuj914@gmail.com',
    'total_amount' => '1299.99',
    'order_date' => date('Y-m-d H:i:s'),
    'items' => [
        [
            'name' => 'Test Product 1',
            'quantity' => 2,
            'price' => '499.99'
        ],
        [
            'name' => 'Test Product 2',
            'quantity' => 1,
            'price' => '300.00'
        ]
    ]
];

// Create email notifier instance
$emailNotifier = new EmailNotifier();

// Send order notification
$result = $emailNotifier->sendOrderNotification($orderData);

// Output result
echo "Email Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n"; 