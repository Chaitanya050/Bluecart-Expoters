<?php
session_start();
require_once '../config/auth.php';
require_once '../config/email.php';

// Check admin authentication
requireAdminLogin();

$type = $_GET['type'] ?? 'order';

$emailNotifier = new EmailNotifier();

// Sample data for preview
$sampleOrderData = [
    'order_id' => '12345',
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_phone' => '+919876543210',
    'order_date' => date('Y-m-d H:i:s'),
    'total_amount' => '2,499.00',
    'items' => [
        [
            'name' => 'iPhone 15 Pro Max',
            'quantity' => 1,
            'price' => '1,999.00',
            'subtotal' => '1,999.00'
        ],
        [
            'name' => 'Premium Phone Case',
            'quantity' => 1,
            'price' => '500.00',
            'subtotal' => '500.00'
        ]
    ],
    'shipping_address' => '123 Main Street, Electronics City, Bangalore, Karnataka - 560100'
];

$sampleProducts = [
    [
        'name' => 'iPhone 15 Pro',
        'stock_quantity' => 2,
        'min_stock_level' => 5
    ],
    [
        'name' => 'Samsung Galaxy S24',
        'stock_quantity' => 1,
        'min_stock_level' => 3
    ]
];

switch($type) {
    case 'order':
        $reflection = new ReflectionClass($emailNotifier);
        $method = $reflection->getMethod('formatOrderEmailHTML');
        $method->setAccessible(true);
        $html = $method->invoke($emailNotifier, $sampleOrderData);
        break;
        
    case 'status':
        $reflection = new ReflectionClass($emailNotifier);
        $method = $reflection->getMethod('formatStatusUpdateHTML');
        $method->setAccessible(true);
        $html = $method->invoke($emailNotifier, $sampleOrderData, 'shipped');
        break;
        
    case 'stock':
        $reflection = new ReflectionClass($emailNotifier);
        $method = $reflection->getMethod('formatLowStockHTML');
        $method->setAccessible(true);
        $html = $method->invoke($emailNotifier, $sampleProducts);
        break;
        
    default:
        $html = '<h1>Template not found</h1>';
}

echo $html;
?>
