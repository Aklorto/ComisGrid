<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = db_connect();
if (!$conn) die("Database connection failed.");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$buyer_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id'] ?? 0);
$product_id = intval($_POST['product_id'] ?? 0);
$seller_id = intval($_POST['seller_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($order_id <= 0 || $product_id <= 0 || $seller_id <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
    die("Invalid review details.");
}

/* Check buyer really bought it */
$check = $conn->prepare("
    SELECT order_id 
    FROM orders 
    WHERE order_id = ? 
    AND buyer_id = ? 
    AND seller_id = ? 
    AND product_id = ?
    LIMIT 1
");
$check->bind_param("iiii", $order_id, $buyer_id, $seller_id, $product_id);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die("You can only review products you purchased.");
}

/* Prevent duplicate review per order */
$dup = $conn->prepare("SELECT review_id FROM reviews WHERE order_id = ? AND buyer_id = ? LIMIT 1");
$dup->bind_param("ii", $order_id, $buyer_id);
$dup->execute();

if ($dup->get_result()->num_rows > 0) {
    die("You already reviewed this order.");
}

$stmt = $conn->prepare("
    INSERT INTO reviews (buyer_id, seller_id, product_id, order_id, rating, comment)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("iiiiis", $buyer_id, $seller_id, $product_id, $order_id, $rating, $comment);

if ($stmt->execute()) {
    header("Location: profile.php?review=success");
    exit;
}

die("Failed to submit review.");
?>