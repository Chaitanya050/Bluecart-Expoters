<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if (!isset($_POST['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID required']);
    exit();
}

$cart_id = $_POST['cart_id'];
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error removing item from cart']);
}
?>
