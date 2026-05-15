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

$user_id = $_SESSION['user_id'];

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
$prod_stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE seller_id = ?
    ORDER BY created_at DESC
");

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

$purchased_stmt->bind_param('i', $user_id);
$purchased_stmt->execute();
$purchased = $purchased_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* CALCULATIONS */
$total_reviews = count($reviews);
$avg_rating = $total_reviews > 0 ? array_sum(array_column($reviews, 'rating')) / $total_reviews : 0;

$pending_payout = $user['balance'] ?? 0;
$platform_fee_paid = ($user['total_earned'] ?? 0) * 0.05;
$this_month_earned = $user['this_month_earned'] ?? 0;

$preview_mode = $_GET['preview'] ?? 'owner';
?>
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



<div class="profile-wrapper">

    <div class="profile-card">
        <div class="profile-top">

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
                <div class="stat-icon"><i class="bi bi-star"></i></div>
                <div class="stat-label">Avg. rating</div>
                <div class="stat-value"><?php echo number_format($avg_rating, 1); ?></div>
                <div class="stat-sub">from <?php echo $total_reviews; ?> reviews</div>
            </div>

            <div class="stat-box">
                <div class="stat-icon"><i class="bi bi-eye"></i></div>
                <div class="stat-label">Profile views</div>
                <div class="stat-value"><?php echo number_format($user['profile_views'] ?? 0); ?></div>
                <div class="stat-sub">this month</div>
            </div>
        </div>
    </div>

    <?php if ($preview_mode === 'owner'): ?>
    <div class="earnings-card">
        <div class="earnings-header">
            <div class="earnings-header-left">
                <i class="bi bi-lock-fill"></i>
                Your earnings — only visible to you
            </div>
            <span class="private-badge">Private</span>
        </div>

        <div class="earnings-grid">
            <div class="earn-box">
                <div class="earn-box-label"><i class="bi bi-wallet2"></i> Total earnings</div>
                <div class="earn-box-value">₱<?php echo number_format($user['total_earned'] ?? 0, 2); ?></div>
                <div class="earn-box-sub">after 5% platform fee</div>
            </div>

            <div class="earn-box">
                <div class="earn-box-label"><i class="bi bi-arrow-up-circle"></i> Available balance</div>
                <div class="earn-box-value">₱<?php echo number_format($pending_payout, 2); ?></div>
                <button class="payout-btn" onclick="alert('Payout request simulation only.')">
                    Request payout <i class="bi bi-arrow-up-right"></i>
                </button>
            </div>

            <div class="earn-box">
                <div class="earn-box-label"><i class="bi bi-calendar-month"></i> This month</div>
                <div class="earn-box-value">₱<?php echo number_format($this_month_earned, 2); ?></div>
            </div>

            <div class="earn-box">
                <div class="earn-box-label"><i class="bi bi-percent"></i> Platform fee paid</div>
                <div class="earn-box-value">₱<?php echo number_format($platform_fee_paid, 2); ?></div>
                <div class="earn-box-sub">5% of gross income</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="tabs-row">
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
                            <?php if ($preview_mode === 'owner'): ?>

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

    <div id="tab-reviews" class="tab-panel">
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

<script src="../assets/js/profile.js"></script>
</body>
</html>