<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = db_connect();
if (!$conn) die("Database connection failed.");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$current_user = $_SESSION['user_id'];
$other_user = intval($_GET['user_id'] ?? 0);

if ($other_user <= 0 || $other_user == $current_user) {
    die("Invalid chat user.");
}

/* find existing direct thread */
$stmt = $conn->prepare("
    SELECT t1.thread_id
    FROM chat_members t1
    JOIN chat_members t2 ON t1.thread_id = t2.thread_id
    JOIN chat_threads ct ON ct.thread_id = t1.thread_id
    WHERE t1.user_id = ? AND t2.user_id = ? AND ct.thread_type = 'direct'
    LIMIT 1
");
$stmt->bind_param("ii", $current_user, $other_user);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    header("Location: messages.php?thread_id=" . $row['thread_id']);
    exit;
}

$conn->begin_transaction();

try {
    $thread = $conn->prepare("INSERT INTO chat_threads (thread_type, thread_name) VALUES ('direct', 'Direct Chat')");
    $thread->execute();
    $thread_id = $thread->insert_id;

    $member = $conn->prepare("INSERT INTO chat_members (thread_id, user_id) VALUES (?, ?)");
    $member->bind_param("ii", $thread_id, $current_user);
    $member->execute();

    $member->bind_param("ii", $thread_id, $other_user);
    $member->execute();

    $conn->commit();

    header("Location: messages.php?thread_id=" . $thread_id);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Failed to start chat.");
}
?>