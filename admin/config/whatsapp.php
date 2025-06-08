<?php
// WhatsApp configuration for order notifications

class WhatsAppNotifier {
    private $apiUrl;
    private $apiKey;
    private $adminPhone;
    
    public function __construct() {
        // Using Twilio WhatsApp API (you can also use other services like WhatsApp Business API)
        $this->apiUrl = 'https://api.twilio.com/2010-04-01/Accounts/YOUR_ACCOUNT_SID/Messages.json';
        $this->apiKey = 'YOUR_AUTH_TOKEN'; // Twilio Auth Token
        $this->adminPhone = 'whatsapp:+919876543210'; // Admin's WhatsApp number (replace with actual)
    }
    
    public function sendOrderNotification($orderData) {
        $message = $this->formatOrderMessage($orderData);
        return $this->sendMessage($this->adminPhone, $message);
    }
    
    private function formatOrderMessage($orderData) {
        $message = "🛒 *NEW ORDER RECEIVED* 🛒\n\n";
        $message .= "📋 *Order ID:* #{$orderData['order_id']}\n";
        $message .= "👤 *Customer:* {$orderData['customer_name']}\n";
        $message .= "📧 *Email:* {$orderData['customer_email']}\n";
        $message .= "📱 *Phone:* {$orderData['customer_phone']}\n";
        $message .= "📅 *Date:* {$orderData['order_date']}\n";
        $message .= "💰 *Total Amount:* ₹{$orderData['total_amount']}\n\n";
        
        $message .= "📦 *ITEMS ORDERED:*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        
        foreach ($orderData['items'] as $item) {
            $message .= "• {$item['name']}\n";
            $message .= "  Qty: {$item['quantity']} × ₹{$item['price']} = ₹{$item['subtotal']}\n\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🏠 *DELIVERY ADDRESS:*\n";
        $message .= "{$orderData['shipping_address']}\n\n";
        
        $message .= "⚡ *Action Required:*\n";
        $message .= "Please confirm this order in the admin panel.\n";
        $message .= "🔗 Admin Panel: " . $_SERVER['HTTP_HOST'] . "/admin/orders.php\n\n";
        
        $message .= "Thank you! 🙏";
        
        return $message;
    }
    
    private function sendMessage($to, $message) {
        $data = [
            'From' => 'whatsapp:+14155238886', // Twilio WhatsApp number
            'To' => $to,
            'Body' => $message
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "YOUR_ACCOUNT_SID:" . $this->apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode == 201,
            'response' => json_decode($response, true),
            'http_code' => $httpCode
        ];
    }
    
    // Alternative method using WhatsApp Business API
    public function sendViaWhatsAppBusinessAPI($orderData) {
        $message = $this->formatOrderMessage($orderData);
        
        // WhatsApp Business API endpoint
        $url = 'https://graph.facebook.com/v17.0/YOUR_PHONE_NUMBER_ID/messages';
        
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => '919876543210', // Admin phone number without +
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];
        
        $headers = [
            'Authorization: Bearer YOUR_ACCESS_TOKEN',
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode == 200,
            'response' => json_decode($response, true),
            'http_code' => $httpCode
        ];
    }
    
    // Send order status updates
    public function sendOrderStatusUpdate($orderData, $newStatus) {
        $message = "📋 *ORDER STATUS UPDATE*\n\n";
        $message .= "Order ID: #{$orderData['order_id']}\n";
        $message .= "Customer: {$orderData['customer_name']}\n";
        $message .= "Status: *" . strtoupper($newStatus) . "*\n\n";
        
        switch($newStatus) {
            case 'confirmed':
                $message .= "✅ Your order has been confirmed and is being prepared.";
                break;
            case 'shipped':
                $message .= "🚚 Your order has been shipped and is on the way!";
                break;
            case 'delivered':
                $message .= "🎉 Your order has been delivered successfully!";
                break;
            case 'cancelled':
                $message .= "❌ Your order has been cancelled.";
                break;
        }
        
        return $this->sendMessage('whatsapp:+91' . $orderData['customer_phone'], $message);
    }
}

// Alternative using a simpler WhatsApp API service
class SimpleWhatsAppAPI {
    private $apiUrl = 'https://api.whatsapp.com/send';
    
    public function generateWhatsAppLink($phone, $message) {
        $encodedMessage = urlencode($message);
        return "{$this->apiUrl}?phone={$phone}&text={$encodedMessage}";
    }
    
    public function sendInstantMessage($phone, $message) {
        // This opens WhatsApp with pre-filled message
        $link = $this->generateWhatsAppLink($phone, $message);
        return $link;
    }
}
?>
