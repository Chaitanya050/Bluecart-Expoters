<?php
session_start();
require_once '../config/database.php';
require_once 'config/auth.php';

// Check admin authentication
requireAdminLogin();

$page_title = "My Profile";

// Get admin user details
$admin = getAdminUser($pdo, $_SESSION['user_id']);

if (!$admin) {
    header("Location: logout.php");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        
        try {
            // Check if email is already taken by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $error = "Email address is already in use.";
            } else {
                // Update profile
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $_SESSION['user_id']]);
                
                // Update session
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
                
                // Log activity
                logAdminActivity($pdo, $_SESSION['user_id'], 'UPDATE_PROFILE', 'Updated profile information');
                
                $success = "Profile updated successfully.";
                
                // Refresh admin details
                $admin = getAdminUser($pdo, $_SESSION['user_id']);
            }
        } catch (PDOException $e) {
            $error = "Error updating profile: " . $e->getMessage();
            error_log($error);
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (!verifyPassword($current_password, $user['password'])) {
                $error = "Current password is incorrect.";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords do not match.";
            } elseif (strlen($new_password) < 8) {
                $error = "New password must be at least 8 characters long.";
            } else {
                // Update password
                $hashed_password = hashPassword($new_password);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                
                // Log activity
                logAdminActivity($pdo, $_SESSION['user_id'], 'CHANGE_PASSWORD', 'Changed account password');
                
                $success = "Password changed successfully.";
            }
        } catch (PDOException $e) {
            $error = "Error changing password: " . $e->getMessage();
            error_log($error);
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-user-circle me-2"></i>My Profile
                    </h5>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Profile Information</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Last Login</label>
                                    <input type="text" class="form-control" value="<?php echo date('M d, Y H:i:s', strtotime($admin['last_login'])); ?>" readonly>
                                </div>

                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Change Password</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           minlength="8" required>
                                    <div class="form-text">Password must be at least 8 characters long</div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>

                                <button type="submit" name="change_password" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    if (this.value !== document.getElementById('new_password').value) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 