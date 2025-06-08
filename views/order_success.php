<?php
session_start();
if (!isset($_GET['order_id']) && !isset($_SESSION['last_order'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed Successfully - BlueCrate Exports</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
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
            animation: checkmark 0.8s ease-in-out;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }

        h1 {
            color: var(--success-green);
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .order-details {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .order-details p {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }

        .order-details p:last-child {
            border-bottom: none;
        }

        .order-details strong {
            color: var(--secondary-color);
            font-size: 1.1em;
        }

        .next-steps {
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed var(--accent-color);
            border-radius: 10px;
            background: rgba(255, 193, 7, 0.05);
        }

        .next-steps h3 {
            color: var(--secondary-color);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .next-steps ul {
            list-style-type: none;
            text-align: left;
            padding-left: 20px;
        }

        .next-steps li {
            margin: 12px 0;
            position: relative;
            padding-left: 10px;
            transition: transform 0.2s ease;
        }

        .next-steps li:hover {
            transform: translateX(5px);
        }

        .next-steps li:before {
            content: "✓";
            color: var(--success-green);
            position: absolute;
            left: -20px;
            font-weight: bold;
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
            border: none;
            min-width: 160px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white !important;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white !important;
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

        .support-info p {
            margin: 5px 0;
        }

        .support-info a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .support-info a:hover {
            color: var(--primary-color);
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

            h1 {
                font-size: 24px;
            }
        }

        /* Loading animation */
        .loading {
            display: none;
            margin: 20px auto;
        }

        .loading:after {
            content: " ";
            display: block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            border-color: var(--primary-color) transparent;
            animation: loading 1.2s linear infinite;
        }

        @keyframes loading {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Order Placed Successfully! 🎉</h1>
        <p class="lead">Thank you for shopping with BlueCrate Exports. We're preparing your order with care!</p>

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
            <a href="../track-order.php" class="btn btn-primary">
                <i class="fas fa-truck"></i> Track Order
            </a>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-shopping-cart"></i> Continue Shopping
            </a>
        </div>

        <div class="support-info">
            <p>Need help? Contact our support team</p>
            <p>
                <a href="mailto:support@bluecrate.com">
                    <i class="fas fa-envelope"></i> support@bluecrate.com
                </a> | 
                <a href="tel:18001234567">
                    <i class="fas fa-phone"></i> 1800-123-4567
                </a>
            </p>
        </div>
        
        <div class="loading" id="loading"></div>
    </div>

    <!-- Add Bootstrap JS and its dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Add click handlers for buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const loading = document.getElementById('loading');
                    loading.style.display = 'block';
                    setTimeout(() => loading.style.display = 'none', 2000);
                });
            });
        });
    </script>
</body>
</html> 