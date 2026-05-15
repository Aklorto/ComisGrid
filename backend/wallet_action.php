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
$action_type = $_POST['action_type'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);

if ($amount <= 0) {
    die("Invalid amount.");
}

if ($action_type === 'topup') {
    $conn->begin_transaction();

    try {
        $update = $conn->prepare("UPDATE users SET balance = balance + ? WHERE user_id = ?");
        $update->bind_param("di", $amount, $user_id);
        $update->execute();

        $description = "Wallet top up via GCash simulation";

        $trans = $conn->prepare("
            INSERT INTO transactions (user_id, order_id, type, amount, description)
            VALUES (?, NULL, 'Top Up', ?, ?)
        ");
        $trans->bind_param("ids", $user_id, $amount, $description);
        $trans->execute();

        $conn->commit();

        header("Location: wallet.php?wallet=topup_success&amount=" . $amount);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Top up failed: " . $e->getMessage());
    }
}

if ($action_type === 'withdraw') {
    $balance_stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
    $balance_stmt->bind_param("i", $user_id);
    $balance_stmt->execute();

    $user = $balance_stmt->get_result()->fetch_assoc();

    if (!$user || $user['balance'] < $amount) {
        die("Insufficient balance.");
    }

    $conn->begin_transaction();

    try {
        $update = $conn->prepare("UPDATE users SET balance = balance - ? WHERE user_id = ?");
        $update->bind_param("di", $amount, $user_id);
        $update->execute();

        $negative_amount = -$amount;
        $description = "Wallet withdrawal to GCash simulation";

        $trans = $conn->prepare("
            INSERT INTO transactions (user_id, order_id, type, amount, description)
            VALUES (?, NULL, 'Withdraw', ?, ?)
        ");
        $trans->bind_param("ids", $user_id, $negative_amount, $description);
        $trans->execute();

        $conn->commit();

        header("Location: wallet.php?wallet=withdraw_success&amount=" . $amount);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Withdraw failed: " . $e->getMessage());
    }
}

die("Invalid wallet action.");
?>