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

$buyer_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    die("Invalid product.");
}

$product_stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product = $product_stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$seller_id = $product['seller_id'];
$price = floatval($product['price']);

if ($buyer_id == $seller_id) {
    die("You cannot buy your own product.");
}

$buyer_stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$buyer_stmt->bind_param("i", $buyer_id);
$buyer_stmt->execute();
$buyer = $buyer_stmt->get_result()->fetch_assoc();

if (!$buyer) {
    die("Buyer not found.");
}

if ($buyer['balance'] < $price) {
    die("Insufficient balance. Please top up first.");
}

$platform_fee = $price * 0.05;
$seller_earning = $price - $platform_fee;

$conn->begin_transaction();

try {
    $order_stmt = $conn->prepare("
        INSERT INTO orders 
        (buyer_id, seller_id, product_id, total_price, platform_fee, seller_earning, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Paid')
    ");
    $order_stmt->bind_param("iiiddd", $buyer_id, $seller_id, $product_id, $price, $platform_fee, $seller_earning);
    $order_stmt->execute();

    $order_id = $order_stmt->insert_id;

    $deduct_stmt = $conn->prepare("UPDATE users SET balance = balance - ?, total_spent = total_spent + ? WHERE user_id = ?");
    $deduct_stmt->bind_param("ddi", $price, $price, $buyer_id);
    $deduct_stmt->execute();

    $earn_stmt = $conn->prepare("
        UPDATE users 
        SET balance = balance + ?, total_earned = total_earned + ?, this_month_earned = this_month_earned + ?, total_sales = total_sales + 1
        WHERE user_id = ?
    ");
    $earn_stmt->bind_param("dddi", $seller_earning, $seller_earning, $seller_earning, $seller_id);
    $earn_stmt->execute();

    $profit_stmt = $conn->prepare("INSERT INTO company_profits (order_id, amount) VALUES (?, ?)");
    $profit_stmt->bind_param("id", $order_id, $platform_fee);
    $profit_stmt->execute();

    /* MARK PRODUCT AS SOLD */
$mark_sold = $conn->prepare("
    UPDATE products 
    SET status = 'Sold' 
    WHERE product_id = ?
");

$mark_sold->bind_param("i", $product_id);
$mark_sold->execute();

    $buyer_desc = "Purchased product: " . $product['title'];
    $seller_desc = "Sold product: " . $product['title'];
    $company_desc = "Platform fee from product: " . $product['title'];

    $trans_stmt = $conn->prepare("
        INSERT INTO transactions (user_id, order_id, type, amount, description)
        VALUES (?, ?, ?, ?, ?)
    ");

    $type = "Purchase";
    $negative_price = -$price;
    $trans_stmt->bind_param("iisds", $buyer_id, $order_id, $type, $negative_price, $buyer_desc);
    $trans_stmt->execute();

    $type = "Sale Earning";
    $trans_stmt->bind_param("iisds", $seller_id, $order_id, $type, $seller_earning, $seller_desc);
    $trans_stmt->execute();

    $type = "Platform Fee";
    $admin_user_id = 0;
    $trans_stmt->bind_param("iisds", $admin_user_id, $order_id, $type, $platform_fee, $company_desc);
    $trans_stmt->execute();

    $mark_sold = $conn->prepare("UPDATE products SET status = 'Sold' WHERE product_id = ?");
$mark_sold->bind_param("i", $product_id);
$mark_sold->execute();
    $conn->commit();

header("Location: dashboard.php?purchase=success&amount=" . $price);
exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Purchase failed: " . $e->getMessage());
}
?>