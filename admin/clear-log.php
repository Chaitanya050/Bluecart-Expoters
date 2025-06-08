<?php
session_start();
require_once '../config/database.php';
require_once 'config/auth.php';

// Check admin authentication
requireAdminLogin();

try {
    // Start transaction
    $pdo->beginTransaction();

    // Log the clear action before clearing
    logAdminActivity($pdo, $_SESSION['user_id'], 'CLEAR_LOG', 'Cleared activity log');

    // Clear the log table
    $stmt = $pdo->prepare("DELETE FROM admin_activity_log WHERE created_at < CURRENT_TIMESTAMP");
    $stmt->execute();

    $pdo->commit();
    $_SESSION['success'] = "Activity log cleared successfully.";
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error clearing activity log: " . $e->getMessage();
    error_log("Error clearing activity log: " . $e->getMessage());
}

// Redirect back to activity log page
header("Location: activity-log.php");
exit(); 