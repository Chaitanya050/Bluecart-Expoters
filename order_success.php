<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed Successfully - TechHub Electronics</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2196F3;
            --accent-color: #FFC107;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --success-green: #4CAF50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-icon i {
            color: white;
            font-size: 40px;
        }

        h1 {
            color: var(--success-green);
            margin-bottom: 20px;
            font-size: 28px;
        }

        .order-details {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }

        .order-details p {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }

        .order-details strong {
            color: var(--secondary-color);
        }

        .next-steps {
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed var(--accent-color);
            border-radius: 10px;
        }

        .next-steps h3 {
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        .next-steps ul {
            list-style-type: none;
            text-align: left;
            padding-left: 20px;
        }

        .next-steps li {
            margin: 10px 0;
            position: relative;
        }

        .next-steps li:before {
            content: "✓";
            color: var(--success-green);
            position: absolute;
            left: -20px;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .support-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Order Placed Successfully! 🎉</h1>
        <p>Thank you for shopping with TechHub Electronics. We're preparing your order with care!</p>

        <div class="order-details">
            <p>
                <span>Order ID:</span>
                <strong>#<span id="orderId"></span></strong>
            </p>
            <p>
                <span>Order Date:</span>
                <strong><span id="orderDate"></span></strong>
            </p>
            <p>
                <span>Total Amount:</span>
                <strong>₹<span id="totalAmount"></span></strong>
            </p>
        </div>

        <div class="next-steps">
            <h3>What's Next?</h3>
            <ul>
                <li>You'll receive an order confirmation email shortly</li>
                <li>We'll notify you when your order ships</li>
                <li>Track your order status in your account dashboard</li>
                <li>Expected delivery within 3-5 business days</li>
            </ul>
        </div>

        <div class="buttons">
            <a href="track-order.php" class="btn btn-primary">Track Order</a>
            <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
        </div>

        <div class="support-info">
            <p>Need help? Contact our support team</p>
            <p>📧 support@techhub.com | 📞 1800-123-4567</p>
        </div>
    </div>

    <script>
        // Parse the JSON response from place_order.php
        function displayOrderDetails(orderData) {
            document.getElementById('orderId').textContent = orderData.order_id;
            document.getElementById('orderDate').textContent = new Date().toLocaleString();
            document.getElementById('totalAmount').textContent = orderData.total_amount || '0.00';
        }

        // Get order details from URL parameters or session storage
        const urlParams = new URLSearchParams(window.location.search);
        const orderData = {
            order_id: urlParams.get('order_id'),
            total_amount: urlParams.get('total_amount')
        };

        displayOrderDetails(orderData);
    </script>
</body>
</html> 