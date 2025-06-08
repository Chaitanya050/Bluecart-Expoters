<?php
session_start();
require_once '../config/database.php';
require_once 'config/auth.php';

// Check admin authentication
requireAdminLogin();

$page_title = "Activity Log";

// Get filter parameters
$user_filter = $_GET['user_id'] ?? '';
$action_filter = $_GET['action'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "
    SELECT l.*, u.full_name, u.email
    FROM admin_activity_log l
    LEFT JOIN users u ON l.user_id = u.id
";

$params = [];
$where = [];

if ($user_filter) {
    $where[] = "l.user_id = ?";
    $params[] = $user_filter;
}

if ($action_filter) {
    $where[] = "l.action = ?";
    $params[] = $action_filter;
}

if ($date_from) {
    $where[] = "DATE(l.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where[] = "DATE(l.created_at) <= ?";
    $params[] = $date_to;
}

if ($search) {
    $where[] = "(l.details LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY l.created_at DESC";

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$activities = $stmt->fetchAll();

// Get admin users for filter
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'admin'");
$admin_users = $stmt->fetchAll();

// Get unique actions for filter
$stmt = $pdo->query("SELECT DISTINCT action FROM admin_activity_log ORDER BY action");
$actions = $stmt->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Activity Log
                        </h5>
                        <button type="button" class="btn btn-danger" data-confirm="Are you sure you want to clear the activity log?" onclick="clearLog()">
                            <i class="fas fa-trash me-2"></i>Clear Log
                        </button>
                    </div>

                    <!-- Filters -->
                    <form class="row g-3 mb-4">
                        <div class="col-md-2">
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                <?php foreach ($admin_users as $admin): ?>
                                    <option value="<?php echo $admin['id']; ?>" <?php echo $user_filter == $admin['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($admin['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                <?php foreach ($actions as $action): ?>
                                    <option value="<?php echo $action; ?>" <?php echo $action_filter === $action ? 'selected' : ''; ?>>
                                        <?php echo $action; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" placeholder="To Date">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </form>

                    <!-- Activity Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y H:i:s', strtotime($activity['created_at'])); ?></td>
                                        <td>
                                            <?php if ($activity['user_id']): ?>
                                                <div><?php echo htmlspecialchars($activity['full_name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($activity['email']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getActionColor($activity['action']); ?>">
                                                <?php echo $activity['action']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($activity['details']); ?></td>
                                        <td>
                                            <span class="text-muted"><?php echo $activity['ip_address']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($activities)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle me-2"></i>No activities found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearLog() {
    if (confirm('Are you sure you want to clear the activity log? This action cannot be undone.')) {
        window.location.href = 'clear-log.php';
    }
}

function getActionColor(action) {
    switch (action.toLowerCase()) {
        case 'login':
            return 'success';
        case 'logout':
            return 'secondary';
        case 'login_failed':
            return 'danger';
        case 'update_order':
            return 'primary';
        case 'delete':
            return 'danger';
        default:
            return 'info';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?> 