<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = db_connect();
if (!$conn) die("Database connection failed.");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$follower_id = $_SESSION['user_id'];
$following_id = intval($_POST['following_id'] ?? 0);

if ($following_id <= 0 || $following_id == $follower_id) {
    die("Invalid follow action.");
}

$check = $conn->prepare("SELECT follow_id FROM follows WHERE follower_id = ? AND following_id = ?");
$check->bind_param("ii", $follower_id, $following_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
    $del->bind_param("ii", $follower_id, $following_id);
    $del->execute();
} else {
    $ins = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
    $ins->bind_param("ii", $follower_id, $following_id);
    $ins->execute();

    $message = "@" . ($_SESSION['username'] ?? 'Someone') . " followed you.";
    $link = "profile.php?user_id=" . $follower_id;

    $conn->query("CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        actor_id INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        link VARCHAR(255) DEFAULT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_notifications_user_read (user_id, is_read),
        INDEX idx_notifications_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $notif = $conn->prepare("\n        INSERT INTO notifications (user_id, actor_id, type, message, link)\n        VALUES (?, ?, 'follow', ?, ?)\n    ");

    if ($notif) {
        $notif->bind_param("iiss", $following_id, $follower_id, $message, $link);
        $notif->execute();
    }
}

header("Location: profile.php?user_id=" . $following_id);
exit;
?>