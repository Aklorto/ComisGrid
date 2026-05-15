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

$threads = $conn->prepare("
    SELECT 
        ct.thread_id,
        other_user.user_id AS other_user_id,
        other_user.username AS other_username,
        other_user.profile_image AS other_profile_image,
        MAX(cm.created_at) AS last_message_at
    FROM chat_threads ct
    JOIN chat_members me 
        ON ct.thread_id = me.thread_id
    JOIN chat_members other_member 
        ON ct.thread_id = other_member.thread_id 
        AND other_member.user_id != ?
    JOIN users other_user 
        ON other_member.user_id = other_user.user_id
    LEFT JOIN chat_messages cm 
        ON ct.thread_id = cm.thread_id
    WHERE me.user_id = ?
    GROUP BY ct.thread_id, other_user.user_id, other_user.username, other_user.profile_image
    ORDER BY last_message_at DESC
");

$threads->bind_param("ii", $user_id, $user_id);
$threads->execute();
$threadList = $threads->get_result();

$active_thread_id = intval($_GET['thread_id'] ?? 0);
$messages = null;

if ($active_thread_id > 0) {
    $msg_stmt = $conn->prepare("
        SELECT cm.*, u.username, u.profile_image, u.user_id
        FROM chat_messages cm
        JOIN users u 
            ON cm.sender_id = u.user_id
        JOIN chat_members mem 
            ON cm.thread_id = mem.thread_id
        WHERE cm.thread_id = ? 
        AND mem.user_id = ?
        ORDER BY cm.created_at ASC
    ");

    $msg_stmt->bind_param("ii", $active_thread_id, $user_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ComisGrid Messages</title>
    <link rel="stylesheet" href="../assets/css/messages.css">
</head>
<body>

<div class="messages-layout">

    <aside class="threads-panel">
        <h2>Messages</h2>
        <a class="back-link" href="dashboard.php">Back to Dashboard</a>

        <?php if ($threadList->num_rows === 0): ?>
            <p class="empty">No chats yet.</p>
        <?php endif; ?>

        <?php while($t = $threadList->fetch_assoc()): ?>
            <a class="thread-link <?php echo $active_thread_id == $t['thread_id'] ? 'active' : ''; ?>" 
               href="messages.php?thread_id=<?php echo $t['thread_id']; ?>">

                <img 
                    src="<?php echo htmlspecialchars($t['other_profile_image'] ?? '../assets/images/default-profile.png'); ?>" 
                    class="thread-avatar"
                >

                <div>
                    <strong><?php echo htmlspecialchars($t['other_username']); ?></strong>
                    <span>
                        <?php echo $t['last_message_at'] 
                            ? date('M j, g:i A', strtotime($t['last_message_at'])) 
                            : 'No messages'; 
                        ?>
                    </span>
                </div>
            </a>
        <?php endwhile; ?>
    </aside>

    <main class="chat-panel">

        <?php if ($active_thread_id <= 0): ?>

            <div class="empty-chat">Select a conversation.</div>

        <?php else: ?>

            <div class="chat-messages">

                <?php if ($messages && $messages->num_rows > 0): ?>

                    <?php while($m = $messages->fetch_assoc()): ?>

                        <div class="message <?php echo $m['sender_id'] == $user_id ? 'mine' : ''; ?>">

                            <?php if ($m['sender_id'] != $user_id): ?>
                                <a href="profile.php?user_id=<?php echo $m['user_id']; ?>">
                                    <img 
                                        src="<?php echo htmlspecialchars($m['profile_image'] ?? '../assets/images/default-profile.png'); ?>" 
                                        class="message-avatar"
                                    >
                                </a>
                            <?php endif; ?>

                            <div class="message-content">
                                <a href="profile.php?user_id=<?php echo $m['user_id']; ?>" class="message-name">
                                    @<?php echo htmlspecialchars($m['username']); ?>
                                </a>

                                <p><?php echo htmlspecialchars($m['message_text']); ?></p>

                                <span><?php echo date('g:i A', strtotime($m['created_at'])); ?></span>
                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="empty-chat">No messages yet.</div>

                <?php endif; ?>

            </div>

            <form class="chat-form" action="send_message.php" method="POST">
                <input type="hidden" name="thread_id" value="<?php echo $active_thread_id; ?>">
                <input type="text" name="message_text" placeholder="Write a message..." required>
                <button type="submit">Send</button>
            </form>

        <?php endif; ?>

    </main>

</div>

</body>
</html>