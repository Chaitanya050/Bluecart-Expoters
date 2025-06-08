<?php
// Email configuration for order notifications

require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
require_once __DIR__ . '/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailNotifier {
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $fromEmail;
    private $fromName;
    private $adminEmail;
    
    public function __construct() {
        // Load configuration from external file
        $config = require __DIR__ . '/email_config.php';
        
        $this->smtpHost = $config['smtp_host'];
        $this->smtpPort = $config['smtp_port'];
        $this->smtpUsername = $config['smtp_username'];
        $this->smtpPassword = $config['smtp_password'];
        $this->fromEmail = $config['from_email'];
        $this->fromName = $config['from_name'];
        $this->adminEmail = $config['admin_email'];
    }
    
    public function sendOrderNotification($orderData) {
        $subject = "🛒 New Order #{$orderData['order_id']} - Action Required";
        $htmlBody = $this->formatOrderEmailHTML($orderData);
        $textBody = $this->formatOrderEmailText($orderData);
        
        return $this->sendEmail($this->adminEmail, $subject, $htmlBody, $textBody);
    }
    
    private function formatOrderEmailHTML($orderData) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>New Order Notification</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; }
                .order-details { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
                .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                .items-table th { background: #f1f3f4; }
                .total-row { font-weight: bold; background: #e3f2fd; }
                .action-btn { display: inline-block; background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; }
                .alert { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🛒 NEW ORDER RECEIVED</h1>
                    <p>Order #' . $orderData['order_id'] . '</p>
                </div>
                
                <div class="content">
                    <div class="alert">
                        ⚡ <strong>Action Required:</strong> A new order has been placed and requires your attention.
                    </div>
                    
                    <div class="order-details">
                        <h3>📋 Order Information</h3>
                        <table style="width: 100%;">
                            <tr><td><strong>Order ID:</strong></td><td>#' . $orderData['order_id'] . '</td></tr>
                            <tr><td><strong>Order Date:</strong></td><td>' . $orderData['order_date'] . '</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span style="background: #ffc107; padding: 3px 8px; border-radius: 3px;">Pending</span></td></tr>
                            <tr><td><strong>Total Amount:</strong></td><td><strong>₹' . $orderData['total_amount'] . '</strong></td></tr>
                        </table>
                    </div>
                    
                    <div class="order-details">
                        <h3>👤 Customer Information</h3>
                        <table style="width: 100%;">
                            <tr><td><strong>Name:</strong></td><td>' . $orderData['customer_name'] . '</td></tr>
                            <tr><td><strong>Email:</strong></td><td><a href="mailto:' . $orderData['customer_email'] . '">' . $orderData['customer_email'] . '</a></td></tr>
                            <tr><td><strong>Phone:</strong></td><td><a href="tel:' . $orderData['customer_phone'] . '">' . $orderData['customer_phone'] . '</a></td></tr>
                        </table>
                    </div>
                    
                    <div class="order-details">
                        <h3>📦 Ordered Items</h3>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        foreach ($orderData['items'] as $item) {
            $html .= '
                                <tr>
                                    <td>' . $item['name'] . '</td>
                                    <td>' . $item['quantity'] . '</td>
                                    <td>₹' . $item['price'] . '</td>
                                    <td>₹' . $item['subtotal'] . '</td>
                                </tr>';
        }
        
        $html .= '
                                <tr class="total-row">
                                    <td colspan="3"><strong>Total Amount:</strong></td>
                                    <td><strong>₹' . $orderData['total_amount'] . '</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="order-details">
                        <h3>🏠 Delivery Address</h3>
                        <p style="background: #f8f9fa; padding: 10px; border-radius: 5px;">' . nl2br($orderData['shipping_address']) . '</p>
                    </div>
                    
                    <div style="text-align: center; margin: 20px 0;">
                        <a href="' . $_SERVER['HTTP_HOST'] . '/admin/orders.php" class="action-btn">View Order Details</a>
                        <a href="' . $_SERVER['HTTP_HOST'] . '/admin/order-details.php?id=' . $orderData['order_id'] . '" class="action-btn" style="background: #17a2b8;">Process Order</a>
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong>BlueCrate Exports - Admin Panel</strong></p>
                    <p>This is an automated notification. Please do not reply to this email.</p>
                    <p>© ' . date('Y') . ' BlueCrate Exports. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function formatOrderEmailText($orderData) {
        $text = "NEW ORDER RECEIVED\n";
        $text .= "==================\n\n";
        $text .= "Order ID: #{$orderData['order_id']}\n";
        $text .= "Customer: {$orderData['customer_name']}\n";
        $text .= "Email: {$orderData['customer_email']}\n";
        $text .= "Phone: {$orderData['customer_phone']}\n";
        $text .= "Date: {$orderData['order_date']}\n";
        $text .= "Total Amount: ₹{$orderData['total_amount']}\n\n";
        
        $text .= "ITEMS ORDERED:\n";
        $text .= "-------------\n";
        
        foreach ($orderData['items'] as $item) {
            $text .= "• {$item['name']}\n";
            $text .= "  Qty: {$item['quantity']} × ₹{$item['price']} = ₹{$item['subtotal']}\n\n";
        }
        
        $text .= "DELIVERY ADDRESS:\n";
        $text .= "----------------\n";
        $text .= "{$orderData['shipping_address']}\n\n";
        
        $text .= "ACTION REQUIRED:\n";
        $text .= "Please confirm this order in the admin panel.\n";
        $text .= "Admin Panel: " . $_SERVER['HTTP_HOST'] . "/admin/orders.php\n\n";
        
        $text .= "Thank you!\n";
        $text .= "BlueCrate Exports Team";
        
        return $text;
    }
    
    private function sendEmail($to, $subject, $htmlBody, $textBody = '') {
        try {
            // Create new PHPMailer instance with exceptions enabled
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->Port = $this->smtpPort;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            
            // Authentication
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            
            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;
            
            // Send the email
            $success = $mail->send();
            
            return [
                'success' => $success,
                'message' => $success ? 'Email sent successfully' : 'Failed to send email'
            ];
            
        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Email error: ' . $e->getMessage()
            ];
        }
    }
    
    // Send order status updates to customers
    public function sendOrderStatusUpdate($orderData, $newStatus) {
        $subject = "Order #{$orderData['order_id']} Status Update - " . ucfirst($newStatus);
        $htmlBody = $this->formatStatusUpdateHTML($orderData, $newStatus);
        
        return $this->sendEmail($orderData['customer_email'], $subject, $htmlBody);
    }
    
    private function formatStatusUpdateHTML($orderData, $newStatus) {
        $statusColors = [
            'pending' => '#ffc107',
            'confirmed' => '#17a2b8',
            'processing' => '#fd7e14',
            'shipped' => '#6f42c1',
            'delivered' => '#28a745',
            'cancelled' => '#dc3545'
        ];
        
        $statusMessages = [
            'confirmed' => 'Your order has been confirmed and is being prepared for shipment.',
            'processing' => 'Your order is currently being processed and will be shipped soon.',
            'shipped' => 'Great news! Your order has been shipped and is on its way to you.',
            'delivered' => 'Your order has been delivered successfully. Thank you for shopping with us!',
            'cancelled' => 'Your order has been cancelled. If you have any questions, please contact us.'
        ];
        
        $color = $statusColors[$newStatus] ?? '#6c757d';
        $message = $statusMessages[$newStatus] ?? 'Your order status has been updated.';
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: ' . $color . '; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; }
                .status-update { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; text-align: center; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📋 Order Status Update</h1>
                    <p>Order #' . $orderData['order_id'] . '</p>
                </div>
                
                <div class="content">
                    <div class="status-update">
                        <h2>Hello ' . $orderData['customer_name'] . ',</h2>
                        <p>' . $message . '</p>
                        <p><strong>Current Status: <span style="background: ' . $color . '; color: white; padding: 5px 10px; border-radius: 3px;">' . strtoupper($newStatus) . '</span></strong></p>
                    </div>
                    
                    <div style="background: white; padding: 15px; margin: 10px 0; border-radius: 5px;">
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> #' . $orderData['order_id'] . '</p>
                        <p><strong>Total Amount:</strong> ₹' . $orderData['total_amount'] . '</p>
                        <p><strong>Order Date:</strong> ' . $orderData['order_date'] . '</p>
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong>BlueCrate Exports</strong></p>
                    <p>Thank you for shopping with us!</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    // Send low stock alerts to admin
    public function sendLowStockAlert($products) {
        $subject = "⚠️ Low Stock Alert - " . count($products) . " Products Need Attention";
        $htmlBody = $this->formatLowStockHTML($products);
        
        return $this->sendEmail($this->adminEmail, $subject, $htmlBody);
    }
    
    private function formatLowStockHTML($products) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; }
                .products-table { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; }
                .products-table th, .products-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                .products-table th { background: #f1f3f4; }
                .low-stock { color: #dc3545; font-weight: bold; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>⚠️ LOW STOCK ALERT</h1>
                    <p>' . count($products) . ' products need immediate attention</p>
                </div>
                
                <div class="content">
                    <p><strong>The following products are running low on stock:</strong></p>
                    
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Min. Required</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($products as $product) {
            $html .= '
                            <tr>
                                <td>' . $product['name'] . '</td>
                                <td class="low-stock">' . $product['stock_quantity'] . '</td>
                                <td>' . $product['min_stock_level'] . '</td>
                                <td><span style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px;">Low Stock</span></td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                    
                    <p><strong>Action Required:</strong> Please restock these items to avoid stockouts.</p>
                    <div style="text-align: center; margin: 20px 0;">
                        <a href="' . $_SERVER['HTTP_HOST'] . '/admin/inventory.php" style="display: inline-block; background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px;">Manage Inventory</a>
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong>BlueCrate Exports - Inventory Management</strong></p>
                    <p>This is an automated alert from your inventory system.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}

// Simple email function using PHP's mail() function (alternative)
class SimpleEmailNotifier {
    private $fromEmail;
    private $fromName;
    private $adminEmail;
    
    public function __construct() {
        $this->fromEmail = 'noreply@bluecrate.com';
        $this->fromName = 'BlueCrate Exports';
        $this->adminEmail = 'khatikanuj914@gmail.com';
    }
    
    public function sendOrderNotification($orderData) {
        $subject = "New Order #{$orderData['order_id']} - Action Required";
        $message = $this->formatSimpleOrderEmail($orderData);
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        
        $success = mail($this->adminEmail, $subject, $message, $headers);
        
        return [
            'success' => $success,
            'message' => $success ? 'Email sent successfully' : 'Failed to send email'
        ];
    }
    
    private function formatSimpleOrderEmail($orderData) {
        $html = "<h2>New Order Received - #{$orderData['order_id']}</h2>";
        $html .= "<p><strong>Customer:</strong> {$orderData['customer_name']}</p>";
        $html .= "<p><strong>Email:</strong> {$orderData['customer_email']}</p>";
        $html .= "<p><strong>Phone:</strong> {$orderData['customer_phone']}</p>";
        $html .= "<p><strong>Total:</strong> ₹{$orderData['total_amount']}</p>";
        $html .= "<h3>Items:</h3><ul>";
        
        foreach ($orderData['items'] as $item) {
            $html .= "<li>{$item['name']} - Qty: {$item['quantity']} - ₹{$item['subtotal']}</li>";
        }
        
        $html .= "</ul>";
        $html .= "<p><strong>Address:</strong> {$orderData['shipping_address']}</p>";
        $html .= "<p><a href='" . $_SERVER['HTTP_HOST'] . "/admin/orders.php'>View in Admin Panel</a></p>";
        
        return $html;
    }
}
?>
