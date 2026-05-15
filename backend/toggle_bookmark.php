<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$conn = db_connect();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'DB failed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$check = $conn->prepare("SELECT bookmark_id FROM bookmarks WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    echo json_encode(['success' => true, 'bookmarked' => false]);
    exit;
}

$ins = $conn->prepare("INSERT INTO bookmarks (user_id, product_id) VALUES (?, ?)");
$ins->bind_param("ii", $user_id, $product_id);
$ins->execute();

echo json_encode(['success' => true, 'bookmarked' => true]);
?>