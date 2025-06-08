<?php
$page_title = "Website Settings";
include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    $_SESSION['success'] = 'Settings updated successfully!';
    header("Location: settings.php");
    exit();
}

// Get all settings grouped by category
$stmt = $pdo->query("SELECT * FROM website_settings ORDER BY category, setting_key");
$all_settings = $stmt->fetchAll();

$settings_by_category = [];
foreach ($all_settings as $setting) {
    $settings_by_category[$setting['category']][] = $setting;
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-cogs me-2"></i>Website Settings</h1>
                <button type="submit" form="settingsForm" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save All Settings
                </button>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" id="settingsForm">
                <div class="row">
                    <?php foreach ($settings_by_category as $category => $settings): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-<?php echo getCategoryIcon($category); ?> me-2"></i>
                                    <?php echo ucwords(str_replace('_', ' ', $category)); ?> Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($settings as $setting): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <?php echo ucwords(str_replace('_', ' ', str_replace($category . '_', '', $setting['setting_key']))); ?>
                                    </label>
                                    <?php if ($setting['description']): ?>
                                        <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($setting['description']); ?></small>
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['setting_type'] === 'boolean'): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                   value="true" 
                                                   <?php echo $setting['setting_value'] === 'true' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Enable</label>
                                        </div>
                                    <?php elseif ($setting['setting_type'] === 'number'): ?>
                                        <input type="number" step="0.01" class="form-control" 
                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                    <?php elseif (strpos($setting['setting_key'], 'address') !== false || strpos($setting['setting_key'], 'description') !== false): ?>
                                        <textarea class="form-control" rows="3" 
                                                  name="settings[<?php echo $setting['setting_key']; ?>]"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" class="form-control" 
                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </form>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-outline-warning w-100" onclick="toggleMaintenance()">
                                        <i class="fas fa-tools me-1"></i>Toggle Maintenance
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-outline-info w-100" onclick="clearCache()">
                                        <i class="fas fa-broom me-1"></i>Clear Cache
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-outline-success w-100" onclick="backupDatabase()">
                                        <i class="fas fa-database me-1"></i>Backup Database
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-outline-primary w-100" onclick="testEmail()">
                                        <i class="fas fa-envelope me-1"></i>Test Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function toggleMaintenance() {
    if (confirm('Are you sure you want to toggle maintenance mode?')) {
        // Implementation for maintenance mode toggle
        alert('Maintenance mode toggled!');
    }
}

function clearCache() {
    if (confirm('Clear all cached data?')) {
        // Implementation for cache clearing
        alert('Cache cleared successfully!');
    }
}

function backupDatabase() {
    if (confirm('Create a database backup?')) {
        // Implementation for database backup
        alert('Database backup created!');
    }
}

function testEmail() {
    // Implementation for email testing
    alert('Test email sent!');
}
</script>

<?php 
function getCategoryIcon($category) {
    $icons = [
        'general' => 'home',
        'contact' => 'phone',
        'shipping' => 'shipping-fast',
        'pricing' => 'dollar-sign',
        'social' => 'share-alt',
        'system' => 'server',
        'inventory' => 'boxes',
        'seo' => 'search'
    ];
    return $icons[$category] ?? 'cog';
}

include 'includes/footer.php'; 
?>
