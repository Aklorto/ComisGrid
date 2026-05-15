<?php
session_start();

require_once __DIR__ . '/../config/db.php';

$conn = db_connect();

if (!$conn) {
    die("Database connection failed.");
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$seller_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if ($title === '' || $category === '' || $price <= 0) {
    die("Title, category, and price are required.");
}

if (!isset($_FILES['artwork_image']) || $_FILES['artwork_image']['error'] !== 0) {
    die("Artwork image is required.");
}

$folder = "../uploads/users/user_" . $seller_id . "/products/";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$fileTmp = $_FILES['artwork_image']['tmp_name'];
$fileName = $_FILES['artwork_image']['name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($fileExt, $allowed)) {
    die("Invalid image type.");
}

$newFileName = "product_" . time() . "_" . rand(1000,9999) . "." . $fileExt;
$uploadPath = $folder . $newFileName;

if (move_uploaded_file($fileTmp, $uploadPath)) {
    $dbPath = "../uploads/users/user_" . $seller_id . "/products/" . $newFileName;

    $stmt = $conn->prepare("
        INSERT INTO products
        (seller_id, title, category, description, price, cover_image)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param('isssds', $seller_id, $title, $category, $description, $price, $dbPath);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    }

    die("Failed to save product.");
}

die("Failed to upload image.");
?>