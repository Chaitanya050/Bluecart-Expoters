<?php
session_start();
require_once 'config/auth.php';

// Check admin authentication
requireAdminLogin();

// Database connection
$host = 'localhost';
$db = 'ecommerce';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = $_POST['settings'] ?? [];
    
    foreach ($settings as $name => $value) {
        $stmt = $pdo->prepare("UPDATE whatsapp_settings SET setting_value = ? WHERE setting_name = ?");
        $stmt->execute([$value, $name]);
    }
    
    $success_message = "WhatsApp settings updated successfully!";
}

// Get current settings
$stmt = $pdo->query("SELECT * FROM whatsapp_settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fab fa-whatsapp text-success"></i> WhatsApp Settings</h1>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">WhatsApp Configuration</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Admin Phone Number</label>
                                        <input type="text" class="form-control" name="settings[admin_phone]" 
                                               value="<?php echo htmlspecialchars($settings['admin_phone'] ?? ''); ?>" 
                                               placeholder="+919876543210">
                                        <div class="form-text">Include country code (e.g., +91 for India)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">API Provider</label>
                                        <select class="form-select" name="settings[api_provider]">
                                            <option value="twilio" <?php echo ($settings['api_provider'] ?? '') === 'twilio' ? 'selected' : ''; ?>>Twilio</option>
                                            <option value="whatsapp_business" <?php echo ($settings['api_provider'] ?? '') === 'whatsapp_business' ? 'selected' : ''; ?>>WhatsApp Business API</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Twilio Account SID</label>
                                        <input type="text" class="form-control" name="settings[twilio_account_sid]" 
                                               value="<?php echo htmlspecialchars($settings['twilio_account_sid'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Twilio Auth Token</label>
                                        <input type="password" class="form-control" name="settings[twilio_auth_token]" 
                                               value="<?php echo htmlspecialchars($settings['twilio_auth_token'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">WhatsApp Business Token</label>
                                        <input type="password" class="form-control" name="settings[whatsapp_business_token]" 
                                               value="<?php echo htmlspecialchars($settings['whatsapp_business_token'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="settings[notifications_enabled]" 
                                                   value="1" <?php echo ($settings['notifications_enabled'] ?? '') === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Enable WhatsApp Notifications</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="settings[send_order_notifications]" 
                                                   value="1" <?php echo ($settings['send_order_notifications'] ?? '') === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Send New Order Notifications to Admin</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="settings[send_status_updates]" 
                                                   value="1" <?php echo ($settings['send_status_updates'] ?? '') === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Send Order Status Updates to Customers</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Setup Instructions</h5>
                            </div>
                            <div class="card-body">
                                <h6>For Twilio:</h6>
                                <ol class="small">
                                    <li>Sign up at <a href="https://twilio.com" target="_blank">twilio.com</a></li>
                                    <li>Get your Account SID and Auth Token</li>
                                    <li>Enable WhatsApp sandbox for testing</li>
                                    <li>Apply for WhatsApp Business API for production</li>
                                </ol>

                                <h6 class="mt-3">For WhatsApp Business API:</h6>
                                <ol class="small">
                                    <li>Apply for WhatsApp Business API</li>
                                    <li>Get your access token from Facebook</li>
                                    <li>Configure webhook endpoints</li>
                                    <li>Verify your business</li>
                                </ol>

                                <div class="alert alert-info mt-3">
                                    <small><i class="fas fa-info-circle"></i> Test your configuration after saving settings.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Test WhatsApp</h5>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-success btn-sm" onclick="testWhatsApp()">
                                    <i class="fab fa-whatsapp"></i> Send Test Message
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function testWhatsApp() {
            fetch('actions/test-whatsapp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Test message sent successfully!');
                } else {
                    alert('Failed to send test message: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        }
    </script>
</body>
</html>
