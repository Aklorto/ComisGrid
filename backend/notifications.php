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

$stmt = $conn->prepare("
    SELECT n.*, u.username, u.profile_image
    FROM notifications n
    LEFT JOIN users u ON n.actor_id = u.user_id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();

$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/notifications.css">
</head>
<body>

<div class="dashboard-layout">

<aside class="sidebar">
    <div class="brand">
        <img src="../assets/images/comisgridlogo1.png" alt="ComisGrid Logo">
        <h3>ComisGrid</h3>
    </div>

    <nav class="nav-menu">
        <a href="dashboard.php"><i class="bi bi-compass"></i> Explore</a>
        <a href="profile.php"><i class="bi bi-person-circle"></i> My Profile</a>
        <a href="wallet.php"><i class="bi bi-wallet2"></i> Wallet</a>
        <a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a>
        <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </nav>
</aside>

<main class="page-main">
    <div class="notifications-wrapper">
        <h1>Notifications</h1>

        <?php if ($notifications->num_rows === 0): ?>
            <div class="empty-box">No notifications yet.</div>
        <?php endif; ?>

        <?php while($n = $notifications->fetch_assoc()): ?>
            <a href="<?php echo htmlspecialchars($n['link'] ?? '#'); ?>" class="notification-card <?php echo $n['is_read'] ? '' : 'unread'; ?>">
                <img src="<?php echo htmlspecialchars($n['profile_image'] ?? '../assets/images/default-profile.png'); ?>" class="notif-avatar">

                <div>
                    <p><?php echo htmlspecialchars($n['message']); ?></p>
                    <span><?php echo date('M j, Y g:i A', strtotime($n['created_at'])); ?></span>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</main>

</div>

</body>
</html>