<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

$conn = db_connect();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$limit = 12;

$stmt = $conn->prepare("SELECT n.notification_id, n.message, n.link, n.is_read, n.created_at, u.username, u.profile_image FROM notifications n LEFT JOIN users u ON n.actor_id = u.user_id WHERE n.user_id = ? ORDER BY n.created_at DESC LIMIT ?");
$stmt->bind_param('ii', $user_id, $limit);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = [
        'id' => (int)$row['notification_id'],
        'message' => $row['message'],
        'link' => $row['link'],
        'is_read' => (int)$row['is_read'],
        'created_at' => $row['created_at'],
        'actor' => [
            'username' => $row['username'],
            'profile_image' => $row['profile_image'] ?? '../assets/images/default-profile.png'
        ]
    ];
}

echo json_encode(['items' => $items]);

exit;
*** End Patch