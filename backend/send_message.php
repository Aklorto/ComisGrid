<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = db_connect();
if (!$conn) die("Database connection failed.");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$thread_id = intval($_POST['thread_id'] ?? 0);
$message_text = trim($_POST['message_text'] ?? '');

if ($thread_id <= 0 || $message_text === '') {
    die("Invalid message.");
}

/* verify membership */
$check = $conn->prepare("SELECT member_id FROM chat_members WHERE thread_id = ? AND user_id = ?");
$check->bind_param("ii", $thread_id, $user_id);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die("You are not part of this chat.");
}

$stmt = $conn->prepare("INSERT INTO chat_messages (thread_id, sender_id, message_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $thread_id, $user_id, $message_text);
$stmt->execute();

header("Location: messages.php?thread_id=" . $thread_id);
exit;
?>