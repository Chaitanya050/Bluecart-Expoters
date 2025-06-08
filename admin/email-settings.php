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
        $stmt = $pdo->prepare("UPDATE email_settings SET setting_value = ? WHERE setting_name = ?");
        $stmt->execute([$value, $name]);
    }
    
    $success_message = "Email settings updated successfully!";
}

// Get current settings
$stmt = $pdo->query("SELECT * FROM email_settings");
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
    <title>Email Settings - Admin Panel</title>
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
                    <h1 class="h2"><i class="fas fa-envelope text-primary"></i> Email Settings</h1>
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
                                <h5 class="card-title mb-0">SMTP Configuration</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" name="settings[smtp_host]" 
                                               value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com'); ?>" 
                                               placeholder="smtp.gmail.com">
                                        <div class="form-text">SMTP server hostname</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">SMTP Port</label>
                                        <input type="number" class="form-control" name="settings[smtp_port]" 
                                               value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>">
                                        <div class="form-text">Usually 587 for TLS or 465 for SSL</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">SMTP Username</label>
                                        <input type="email" class="form-control" name="settings[smtp_username]" 
                                               value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>" 
                                               placeholder="your-email@gmail.com">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control" name="settings[smtp_password]" 
                                               value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>"
                                               placeholder="App Password for Gmail">
                                        <div class="form-text">Use App Password for Gmail (not your regular password)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">From Email</label>
                                        <input type="email" class="form-control" name="settings[from_email]" 
                                               value="<?php echo htmlspecialchars($settings['from_email'] ?? 'noreply@techhub.com'); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">From Name</label>
                                        <input type="text" class="form-control" name="settings[from_name]" 
                                               value="<?php echo htmlspecialchars($settings['from_name'] ?? 'TechHub Electronics'); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Admin Email</label>
                                        <input type="email" class="form-control" name="settings[admin_email]" 
                                               value="<?php echo htmlspecialchars($settings['admin_email'] ?? 'khatikanuj914@gmail.com'); ?>">
                                        <div class="form-text">Email address to receive order notifications</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="settings[notifications_enabled]" 
                                                   value="1" <?php echo ($settings['notifications_enabled'] ?? '') === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Enable Email Notifications</label>
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

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="settings[send_low_stock_alerts]" 
                                                   value="1" <?php echo ($settings['send_low_stock_alerts'] ?? '') === '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Send Low Stock Alerts</label>
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
                                <h6>For Gmail SMTP:</h6>
                                <ol class="small">
                                    <li>Enable 2-Factor Authentication</li>
                                    <li>Generate App Password</li>
                                    <li>Use App Password instead of regular password</li>
                                    <li>Host: smtp.gmail.com, Port: 587</li>
                                </ol>

                                <h6 class="mt-3">For Other Providers:</h6>
                                <ul class="small">
                                    <li><strong>SendGrid:</strong> smtp.sendgrid.net:587</li>
                                    <li><strong>Mailgun:</strong> smtp.mailgun.org:587</li>
                                    <li><strong>Amazon SES:</strong> email-smtp.region.amazonaws.com:587</li>
                                </ul>

                                <div class="alert alert-info mt-3">
                                    <small><i class="fas fa-info-circle"></i> Test your configuration after saving settings.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Test Email</h5>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-primary btn-sm" onclick="testEmail()">
                                    <i class="fas fa-paper-plane"></i> Send Test Email
                                </button>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Email Templates</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Order Notification
                                        <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate('order')">Preview</button>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Status Update
                                        <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate('status')">Preview</button>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Low Stock Alert
                                        <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate('stock')">Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function testEmail() {
            fetch('actions/test-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Test email sent successfully!');
                } else {
                    alert('Failed to send test email: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        }

        function previewTemplate(type) {
            window.open('actions/preview-email-template.php?type=' + type, '_blank', 'width=800,height=600');
        }
    </script>
</body>
</html>
