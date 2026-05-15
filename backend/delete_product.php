<?php
session_start();

require_once __DIR__ . '/../config/db.php';

$conn = db_connect();

if (!$conn) {
    die("Database connection failed.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    die("Invalid product.");
}

/* CHECK OWNERSHIP */
$stmt = $conn->prepare("
    SELECT cover_image 
    FROM products 
    WHERE product_id = ? 
    AND seller_id = ?
");

$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Unauthorized or product not found.");
}

/* DELETE IMAGE FILE */
$imagePath = $product['cover_image'];

if (!empty($imagePath)) {

    $realPath = __DIR__ . '/' . $imagePath;

    $realPath = realpath($realPath);

    if ($realPath && file_exists($realPath)) {
        unlink($realPath);
    }
}

/* DELETE PRODUCT */
$delete_stmt = $conn->prepare("
    DELETE FROM products 
    WHERE product_id = ? 
    AND seller_id = ?
");

$delete_stmt->bind_param("ii", $product_id, $user_id);

if ($delete_stmt->execute()) {

    header("Location: profile.php");
    exit;

} else {

    die("Failed to delete product.");
}
?>