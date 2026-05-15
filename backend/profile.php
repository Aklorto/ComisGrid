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

$logged_in_user_id = $_SESSION['user_id'];

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
} else {
    $user_id = $logged_in_user_id;
}

$is_owner = ($logged_in_user_id == $user_id);

/* GET USER */
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
if (!$stmt) {
    die("User query failed: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User not found.");
}

/* GET PRODUCTS */
$prod_stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
if (!$prod_stmt) {
    die("Products query failed: " . $conn->error);
}
$prod_stmt->bind_param('i', $user_id);
$prod_stmt->execute();
$products = $prod_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* GET REVIEWS */
$rev_stmt = $conn->prepare("
    SELECT r.*, u.username, u.profile_image, p.title AS product_title
    FROM reviews r
    JOIN users u ON r.buyer_id = u.user_id
    JOIN products p ON r.product_id = p.product_id
    WHERE r.seller_id = ?
    ORDER BY r.created_at DESC
");
if (!$rev_stmt) {
    die("Reviews query failed: " . $conn->error);
}
$rev_stmt->bind_param('i', $user_id);
$rev_stmt->execute();
$reviews = $rev_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* REVIEWABLE ORDERS */
$can_review = false;
$reviewable_orders = [];

if (!$is_owner) {
    $reviewable_stmt = $conn->prepare("
        SELECT o.order_id, o.product_id, p.title
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        WHERE o.buyer_id = ?
        AND o.seller_id = ?
        AND NOT EXISTS (
            SELECT 1 FROM reviews r
            WHERE r.order_id = o.order_id
            AND r.buyer_id = o.buyer_id
        )
    ");

    if ($reviewable_stmt) {
        $reviewable_stmt->bind_param("ii", $logged_in_user_id, $user_id);
        $reviewable_stmt->execute();
        $reviewable_orders = $reviewable_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $can_review = count($reviewable_orders) > 0;
    }
}

/* RATING COUNTS */
$rating_counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

foreach ($reviews as $review) {
    $rate = (int)$review['rating'];
    if (isset($rating_counts[$rate])) {
        $rating_counts[$rate]++;
    }
}

/* GET SOLD PRODUCTS */
$sold_stmt = $conn->prepare("
    SELECT o.*, p.title, p.cover_image, u.username AS buyer_name
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    JOIN users u ON o.buyer_id = u.user_id
    WHERE o.seller_id = ?
    ORDER BY o.created_at DESC
");
if (!$sold_stmt) {
    die("Sold items query failed: " . $conn->error);
}
$sold_stmt->bind_param('i', $user_id);
$sold_stmt->execute();
$sold = $sold_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* GET PURCHASED PRODUCTS */
$purchased_stmt = $conn->prepare("
    SELECT o.*, p.title, p.cover_image, u.username AS seller_name
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    JOIN users u ON o.seller_id = u.user_id
    WHERE o.buyer_id = ?
    ORDER BY o.created_at DESC
");
if (!$purchased_stmt) {
    die("Purchased query failed: " . $conn->error);
}
$purchased_stmt->bind_param('i', $user_id);
$purchased_stmt->execute();
$purchased = $purchased_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* FOLLOW COUNTS */
$followers_count = 0;
$following_count = 0;

$follower_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM follows WHERE following_id = ?");
if ($follower_stmt) {
    $follower_stmt->bind_param("i", $user_id);
    $follower_stmt->execute();
    $followers_count = $follower_stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

$following_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM follows WHERE follower_id = ?");
if ($following_stmt) {
    $following_stmt->bind_param("i", $user_id);
    $following_stmt->execute();
    $following_count = $following_stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

/* CALCULATIONS */
$total_reviews = count($reviews);
$avg_rating = $total_reviews > 0 ? array_sum(array_column($reviews, 'rating')) / $total_reviews : 0;

/* NOTIFICATION COUNT */
$notif_count = 0;
$notif_stmt = $conn->prepare("SELECT COUNT(*) AS unread FROM notifications WHERE user_id = ? AND is_read = 0");
if ($notif_stmt) {
    $notif_stmt->bind_param("i", $logged_in_user_id);
    $notif_stmt->execute();
    $notif_count = $notif_stmt->get_result()->fetch_assoc()['unread'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ComisGrid | <?php echo htmlspecialchars($user['username']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/profile.css">
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
            <a href="profile.php" class="active"><i class="bi bi-person-circle"></i> My Profile</a>
            <a href="wallet.php"><i class="bi bi-wallet2"></i> Wallet</a>
            <a href="about_us.php"><i class="bi bi-info-circle"></i> About Us</a>
            <a href="help_center.php"><i class="bi bi-question-circle"></i> Help Center</a>
            <a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a>
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </aside>

    <main class="profile-main">
        <div class="profile-wrapper">

            <!-- PROFILE CARD -->
            <div class="profile-card">

                <div class="profile-top">

                    <div class="profile-left">

                        <img src="<?php echo htmlspecialchars($user['profile_image'] ?? '../assets/images/default-profile.png'); ?>" class="profile-image">

                        <div class="profile-info">

                            <div class="profile-name-row">
                                <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>

                                <?php if (($user['is_verified'] ?? 0) == 1): ?>
                                    <span class="verified-badge">
                                        <i class="bi bi-patch-check-fill"></i> Verified seller
                                    </span>
                                <?php endif; ?>
                            </div>

                            <button id="notifToggle" class="notif-bell" type="button" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                <?php if (!empty($notif_count) && $notif_count > 0): ?>
                                    <span class="notif-badge"><?php echo $notif_count; ?></span>
                                <?php endif; ?>
                            </button>

                            <div id="notifDropdown" class="notif-dropdown" hidden></div>

                            <?php if (!empty($user['tags'])): ?>
                                <div class="profile-tags"><?php echo htmlspecialchars($user['tags']); ?></div>
                            <?php endif; ?>

                            <div class="bio">
                                <?php echo nl2br(htmlspecialchars($user['bio'] ?? 'No bio yet.')); ?>
                            </div>

                            <div class="profile-meta">
                                <span class="rating">★ <?php echo number_format($avg_rating, 1); ?></span>
                                <span>(<?php echo $total_reviews; ?> reviews)</span>
                                <span>· <?php echo (int)($user['total_sales'] ?? 0); ?> sales</span>
                                <span>· Member since <?php echo date('M Y', strtotime($user['created_at'] ?? date('Y-m-d'))); ?></span>
                            </div>

                            <div class="social-links">
                                <?php if (!empty($user['facebook_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['facebook_link']); ?>" target="_blank">
                                        <i class="bi bi-facebook"></i> Facebook
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($user['instagram_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['instagram_link']); ?>" target="_blank">
                                        <i class="bi bi-instagram"></i> Instagram
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($user['x_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['x_link']); ?>" target="_blank">
                                        <i class="bi bi-twitter-x"></i> X / Twitter
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if (!$is_owner): ?>
                                <div class="profile-actions">
                                    <form action="toggle_follow.php" method="POST">
                                        <input type="hidden" name="following_id" value="<?php echo $user_id; ?>">
                                        <button type="submit" class="follow-btn">
                                            <i class="bi bi-person-plus"></i> Follow / Unfollow
                                        </button>
                                    </form>

                                    <a href="start_chat.php?user_id=<?php echo $user_id; ?>" class="message-btn">
                                        <i class="bi bi-chat-dots"></i> Message
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
                        <div class="stat-label">Total sales</div>
                        <div class="stat-value"><?php echo (int)($user['total_sales'] ?? 0); ?></div>
                        <div class="stat-sub">all time</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon"><i class="bi bi-chat-left-text"></i></div>
                        <div class="stat-label">Profile reviews</div>
                        <div class="stat-value"><?php echo number_format($total_reviews); ?></div>
                        <div class="stat-sub">total reviews</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-label">Followers</div>
                        <div class="stat-value"><?php echo number_format($followers_count); ?></div>
                        <div class="stat-sub">total followers</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon"><i class="bi bi-person-plus"></i></div>
                        <div class="stat-label">Following</div>
                        <div class="stat-value"><?php echo number_format($following_count); ?></div>
                        <div class="stat-sub">accounts followed</div>
                    </div>
                </div>
            </div>

            <!-- RATING SUMMARY -->
            <div class="rating-summary-card">

                <div class="rating-summary-top">
                    <div class="avg-rating-left">
                        <div class="avg-number"><?php echo number_format($avg_rating, 1); ?></div>
                        <div class="avg-sub"><?php echo $total_reviews; ?> reviews</div>
                    </div>

                    <div class="rating-summary-box stars-box">
                        <div class="summary-stars">
                            <?php
                                $rounded = round($avg_rating);
                                for ($s = 1; $s <= 5; $s++) {
                                    echo $s <= $rounded ? '★' : '☆';
                                }
                            ?>
                        </div>

                        <div class="summary-label">
                            <?php echo round($avg_rating); ?> star average
                        </div>
                    </div>
                </div>

                <div class="rating-breakdown-list">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <?php
                            $count = $rating_counts[$i];
                            $width = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
                        ?>

                        <div class="rating-breakdown-row">
                            <div class="star-label"><?php echo $i; ?> ★</div>

                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width: <?php echo $width; ?>%;"></div>
                            </div>

                            <div class="rating-count"><?php echo $count; ?></div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- TABS ROW -->
            <div class="profile-tabs-row">
                <button class="tab-btn active" onclick="switchTab('products', this)">
                    <i class="bi bi-grid"></i> Products
                    <span class="count"><?php echo count($products); ?></span>
                </button>

                <button class="tab-btn" onclick="switchTab('purchased', this)">
                    <i class="bi bi-cart-check"></i> Purchased
                    <span class="count"><?php echo count($purchased); ?></span>
                </button>

                <button class="tab-btn" onclick="switchTab('reviews', this)">
                    <i class="bi bi-chat-left-text"></i> Reviews
                    <span class="count"><?php echo $total_reviews; ?></span>
                </button>

                <button class="tab-btn" onclick="switchTab('sold', this)">
                    <i class="bi bi-bag"></i> Sold products
                    <span class="count"><?php echo count($sold); ?></span>
                </button>
            </div>

            <!-- TABS CONTENT -->
            <div class="profile-tabs-content">

                <div id="tab-products" class="tab-panel active">
                    <?php if (empty($products)): ?>
                        <p class="empty-text">No products yet.</p>
                    <?php else: ?>
                        <div class="products-grid">
                            <?php foreach ($products as $p): ?>
                                <div class="product-card">
                                    <img src="<?php echo htmlspecialchars($p['cover_image']); ?>">
                                    <div class="product-info">
                                        <div class="product-title"><?php echo htmlspecialchars($p['title']); ?></div>
                                        <div class="product-price">₱<?php echo number_format($p['price'], 2); ?></div>

                                        <?php if ($is_owner): ?>
                                            <form action="delete_product.php" method="POST" onsubmit="return confirm('Delete this artwork?');">
                                                <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                                <button type="submit" class="delete-btn">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="tab-purchased" class="tab-panel">
                    <?php if (empty($purchased)): ?>
                        <p class="empty-text">No purchased products yet.</p>
                    <?php else: ?>
                        <?php foreach ($purchased as $item): ?>
                            <div class="sold-item">
                                <img src="<?php echo htmlspecialchars($item['cover_image']); ?>">

                                <div class="sold-info">
                                    <div class="sold-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div class="sold-buyer">
                                        Sold by @<?php echo htmlspecialchars($item['seller_name']); ?>
                                        · <?php echo date('M j, Y', strtotime($item['created_at'])); ?>
                                    </div>
                                </div>

                                <div class="sold-price">₱<?php echo number_format($item['total_price'], 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="tab-reviews" class="tab-panel">
                    <?php if ($can_review): ?>
                        <div class="review-form-card">
                            <h4>Leave a review</h4>

                            <form action="submit_review.php" method="POST">
                                <label>Purchased product</label>
                                <select name="order_product" class="form-control mb-3" onchange="setReviewHiddenValues(this)" required>
                                    <option value="">Select product</option>

                                    <?php foreach ($reviewable_orders as $order): ?>
                                        <option value="<?php echo $order['order_id']; ?>|<?php echo $order['product_id']; ?>">
                                            <?php echo htmlspecialchars($order['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="hidden" name="order_id" id="reviewOrderId">
                                <input type="hidden" name="product_id" id="reviewProductId">
                                <input type="hidden" name="seller_id" value="<?php echo $user_id; ?>">

                                <label>Rating</label>
                                <select name="rating" class="form-control mb-3" required>
                                    <option value="5">★★★★★ 5 stars</option>
                                    <option value="4">★★★★☆ 4 stars</option>
                                    <option value="3">★★★☆☆ 3 stars</option>
                                    <option value="2">★★☆☆☆ 2 stars</option>
                                    <option value="1">★☆☆☆☆ 1 star</option>
                                </select>

                                <label>Comment</label>
                                <textarea name="comment" class="form-control mb-3" rows="3" required></textarea>

                                <button type="submit" class="submit-review-btn">Post Review</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($reviews)): ?>
                        <p class="empty-text">No reviews yet.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $r): ?>
                            <div class="review-card">
                                <div class="review-top">
                                    <div class="reviewer-info">
                                        <img class="reviewer-avatar" src="<?php echo htmlspecialchars($r['profile_image'] ?? '../assets/images/default-profile.png'); ?>">

                                        <div>
                                            <div class="reviewer-name">@<?php echo htmlspecialchars($r['username']); ?></div>
                                            <div class="reviewer-meta">
                                                Bought: <?php echo htmlspecialchars($r['product_title']); ?>
                                                · <?php echo date('M j, Y', strtotime($r['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-stars">
                                        <?php echo str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']); ?>
                                    </div>
                                </div>

                                <div class="review-body">
                                    <?php echo nl2br(htmlspecialchars($r['comment'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="tab-sold" class="tab-panel">
                    <?php if (empty($sold)): ?>
                        <p class="empty-text">No sold products yet.</p>
                    <?php else: ?>
                        <?php foreach ($sold as $s): ?>
                            <div class="sold-item">
                                <img src="<?php echo htmlspecialchars($s['cover_image']); ?>">

                                <div class="sold-info">
                                    <div class="sold-title"><?php echo htmlspecialchars($s['title']); ?></div>
                                    <div class="sold-buyer">
                                        Bought by @<?php echo htmlspecialchars($s['buyer_name']); ?>
                                        · <?php echo date('M j, Y', strtotime($s['created_at'])); ?>
                                    </div>
                                </div>

                                <div class="sold-price">₱<?php echo number_format($s['total_price'], 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </main>
</div>

<script src="../assets/js/profile.js"></script>
<script src="../assets/js/notifications.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
