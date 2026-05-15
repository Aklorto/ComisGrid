<?php
session_start();

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = db_connect();

if (!$conn) {
    die("Database connection failed.");
}

function get_single_value($conn, $sql, $default = 0) {
    $result = $conn->query($sql);
    if (!$result) {
        return $default;
    }

    $row = $result->fetch_assoc();
    return array_values($row)[0] ?? $default;
}

$totalUsers = get_single_value($conn, "SELECT COUNT(*) FROM users");
$totalProducts = get_single_value($conn, "SELECT COUNT(*) FROM products");
$totalOrders = get_single_value($conn, "SELECT COUNT(*) FROM orders");
$totalPlatformIncome = get_single_value($conn, "SELECT COALESCE(SUM(amount), 0) FROM company_profits");

$thisMonthIncome = get_single_value($conn, "
    SELECT COALESCE(SUM(amount), 0)
    FROM company_profits
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
    AND YEAR(created_at) = YEAR(CURRENT_DATE())
");

$totalGrossSales = get_single_value($conn, "SELECT COALESCE(SUM(total_price), 0) FROM orders");
$totalSellerEarnings = get_single_value($conn, "SELECT COALESCE(SUM(seller_earning), 0) FROM orders");

$users = $conn->query("
    SELECT user_id, username, email, balance, total_earned, total_spent, total_sales, created_at
    FROM users
    ORDER BY created_at DESC
");

$orders = $conn->query("
    SELECT 
        o.*,
        buyer.username AS buyer_name,
        seller.username AS seller_name,
        p.title AS product_title
    FROM orders o
    JOIN users buyer ON o.buyer_id = buyer.user_id
    JOIN users seller ON o.seller_id = seller.user_id
    JOIN products p ON o.product_id = p.product_id
    ORDER BY o.created_at DESC
");

$profits = $conn->query("
    SELECT 
        cp.*,
        o.total_price,
        o.seller_earning,
        buyer.username AS buyer_name,
        seller.username AS seller_name,
        p.title AS product_title
    FROM company_profits cp
    JOIN orders o ON cp.order_id = o.order_id
    JOIN users buyer ON o.buyer_id = buyer.user_id
    JOIN users seller ON o.seller_id = seller.user_id
    JOIN products p ON o.product_id = p.product_id
    ORDER BY cp.created_at DESC
");

$recentOrders = $conn->query("
    SELECT 
        o.*,
        buyer.username AS buyer_name,
        seller.username AS seller_name,
        p.title AS product_title
    FROM orders o
    JOIN users buyer ON o.buyer_id = buyer.user_id
    JOIN users seller ON o.seller_id = seller.user_id
    JOIN products p ON o.product_id = p.product_id
    ORDER BY o.created_at DESC
    LIMIT 6
");

$chartRows = $conn->query("
    SELECT DATE(created_at) AS profit_date, COALESCE(SUM(amount), 0) AS daily_profit
    FROM company_profits
    GROUP BY DATE(created_at)
    ORDER BY profit_date DESC
    LIMIT 10
");

$chartData = [];

if ($chartRows) {
    while ($row = $chartRows->fetch_assoc()) {
        $chartData[] = $row;
    }
    $chartData = array_reverse($chartData);
}

$maxChartValue = 1;
foreach ($chartData as $row) {
    if ((float)$row["daily_profit"] > $maxChartValue) {
        $maxChartValue = (float)$row["daily_profit"];
    }
}

$currentPage = $_GET["page"] ?? "overview";
$allowedPages = ["overview", "balance", "accounts", "transactions", "earnings"];
if (!in_array($currentPage, $allowedPages)) {
    $currentPage = "overview";
}

$pageTitles = [
    "overview" => "Dashboard",
    "balance" => "Platform Balance",
    "accounts" => "All Accounts",
    "transactions" => "Transactions",
    "earnings" => "Platform Earnings"
];

$pageSubs = [
    "overview" => "Welcome back, Admin — here's what's happening.",
    "balance" => "Accumulated 5% platform fee from completed sales.",
    "accounts" => "View registered users and wallet balances.",
    "transactions" => "Monitor buyer and seller transactions.",
    "earnings" => "Detailed platform income records."
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ComisGrid Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-shell">

    <aside class="admin-sidebar">
        <div class="admin-logo">
            <img src="../assets/images/comisgridlogo1.png" alt="ComisGrid Logo">
            <div>
                <h2>ComisGrid</h2>
                <p>Admin Console</p>
            </div>
        </div>

        <div class="admin-badge">
            <i class="bi bi-shield-check"></i>
            Developer Side
        </div>

        <nav class="admin-nav">
            <a href="?page=overview" class="<?php echo $currentPage === 'overview' ? 'active' : ''; ?>">
                <i class="bi bi-grid"></i> Dashboard
            </a>

            <a href="?page=balance" class="<?php echo $currentPage === 'balance' ? 'active' : ''; ?>">
                <i class="bi bi-wallet2"></i> Platform Balance
            </a>

            <a href="?page=accounts" class="<?php echo $currentPage === 'accounts' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> All Accounts
            </a>

            <a href="?page=transactions" class="<?php echo $currentPage === 'transactions' ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i> Transactions
            </a>

            <a href="?page=earnings" class="<?php echo $currentPage === 'earnings' ? 'active' : ''; ?>">
                <i class="bi bi-graph-up-arrow"></i> Platform Earnings
            </a>

            <!-- Public pages -->
            <a href="../about_us.php" target="_blank" class="nav-external">
                <i class="bi bi-info-circle"></i> About Us
            </a>

            <a href="../help_center.php" target="_blank" class="nav-external">
                <i class="bi bi-question-circle"></i> Help Center
            </a>
        </nav>

        <div class="admin-footer">
            <div class="admin-user">
                <div class="admin-avatar">AG</div>
                <div>
                    <strong>admingrid</strong>
                    <span>Super Admin</span>
                </div>
            </div>

            <a href="admin_logout.php" class="admin-logout">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </div>
    </aside>

    <main class="admin-main">

        <header class="admin-topbar">
            <div>
                <h1><?php echo $pageTitles[$currentPage]; ?></h1>
                <p><?php echo $pageSubs[$currentPage]; ?></p>
            </div>

            <div class="admin-balance-chip">
                <span></span>
                Platform Income: ₱<?php echo number_format((float)$totalPlatformIncome, 2); ?>
            </div>
        </header>

        <section class="admin-content">

            <?php if ($currentPage === "overview"): ?>

                <div class="stat-grid">
                    <div class="stat-card green">
                        <i class="bi bi-cash-coin"></i>
                        <p>Total Platform Income</p>
                        <h2>₱<?php echo number_format((float)$totalPlatformIncome, 2); ?></h2>
                        <span>5% of all completed sales</span>
                    </div>

                    <div class="stat-card blue">
                        <i class="bi bi-people"></i>
                        <p>Registered Users</p>
                        <h2><?php echo number_format((int)$totalUsers); ?></h2>
                        <span>all accounts</span>
                    </div>

                    <div class="stat-card purple">
                        <i class="bi bi-bag-check"></i>
                        <p>Total Orders</p>
                        <h2><?php echo number_format((int)$totalOrders); ?></h2>
                        <span>paid records</span>
                    </div>

                    <div class="stat-card pink">
                        <i class="bi bi-images"></i>
                        <p>Products Posted</p>
                        <h2><?php echo number_format((int)$totalProducts); ?></h2>
                        <span>marketplace items</span>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-head">
                        <div>
                            <h3>Platform Earnings Overview</h3>
                            <p>Daily 5% platform fee collected</p>
                        </div>
                    </div>

                    <div class="bar-chart">
                        <?php if (empty($chartData)): ?>
                            <div class="empty-admin">No earnings data yet.</div>
                        <?php else: ?>
                            <?php foreach ($chartData as $row): ?>
                                <?php $height = max(8, ((float)$row["daily_profit"] / $maxChartValue) * 180); ?>
                                <div class="bar-col">
                                    <div class="bar-value">₱<?php echo number_format((float)$row["daily_profit"], 2); ?></div>
                                    <div class="bar" style="height: <?php echo $height; ?>px;"></div>
                                    <div class="bar-label"><?php echo date("M d", strtotime($row["profit_date"])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-card">
                    <div class="card-head">
                        <div>
                            <h3>Recent Transactions</h3>
                            <p>Latest completed purchases</p>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Buyer</th>
                                    <th>Seller</th>
                                    <th>Product</th>
                                    <th>Sale Price</th>
                                    <th>Platform 5%</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentOrders && $recentOrders->num_rows > 0): ?>
                                    <?php while($o = $recentOrders->fetch_assoc()): ?>
                                        <tr>
                                            <td>@<?php echo htmlspecialchars($o["buyer_name"]); ?></td>
                                            <td>@<?php echo htmlspecialchars($o["seller_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($o["product_title"]); ?></td>
                                            <td>₱<?php echo number_format((float)$o["total_price"], 2); ?></td>
                                            <td class="income">₱<?php echo number_format((float)$o["platform_fee"], 2); ?></td>
                                            <td><?php echo date("M j, Y", strtotime($o["created_at"])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="empty-admin">No transactions yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($currentPage === "balance"): ?>

                <div class="balance-hero">
                    <p>Admin Platform Balance</p>
                    <h2>₱<?php echo number_format((float)$totalPlatformIncome, 2); ?></h2>
                    <span>Accumulated from 5% platform fee on every transaction</span>
                </div>

                <div class="stat-grid three">
                    <div class="stat-card green">
                        <p>This Month</p>
                        <h2>₱<?php echo number_format((float)$thisMonthIncome, 2); ?></h2>
                        <span>current month income</span>
                    </div>

                    <div class="stat-card blue">
                        <p>Gross Sales</p>
                        <h2>₱<?php echo number_format((float)$totalGrossSales, 2); ?></h2>
                        <span>before platform fee</span>
                    </div>

                    <div class="stat-card purple">
                        <p>Seller Earnings</p>
                        <h2>₱<?php echo number_format((float)$totalSellerEarnings, 2); ?></h2>
                        <span>95% released to sellers</span>
                    </div>
                </div>

            <?php elseif ($currentPage === "accounts"): ?>

                <div class="table-card">
                    <div class="card-head">
                        <div>
                            <h3>All Registered Accounts</h3>
                            <p>User list with wallet and sales information</p>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Balance</th>
                                    <th>Earned</th>
                                    <th>Spent</th>
                                    <th>Sales</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users && $users->num_rows > 0): ?>
                                    <?php while($u = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $u["user_id"]; ?></td>
                                            <td>@<?php echo htmlspecialchars($u["username"]); ?></td>
                                            <td><?php echo htmlspecialchars($u["email"]); ?></td>
                                            <td>₱<?php echo number_format((float)$u["balance"], 2); ?></td>
                                            <td class="income">₱<?php echo number_format((float)$u["total_earned"], 2); ?></td>
                                            <td class="danger">₱<?php echo number_format((float)$u["total_spent"], 2); ?></td>
                                            <td><?php echo number_format((int)$u["total_sales"]); ?></td>
                                            <td><?php echo date("M j, Y", strtotime($u["created_at"])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="empty-admin">No users found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($currentPage === "transactions"): ?>

                <div class="table-card">
                    <div class="card-head">
                        <div>
                            <h3>All Transaction Records</h3>
                            <p>Buyer, seller, product, and payment breakdown</p>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Buyer</th>
                                    <th>Seller</th>
                                    <th>Product</th>
                                    <th>Total</th>
                                    <th>Seller 95%</th>
                                    <th>Platform 5%</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($orders && $orders->num_rows > 0): ?>
                                    <?php while($o = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $o["order_id"]; ?></td>
                                            <td>@<?php echo htmlspecialchars($o["buyer_name"]); ?></td>
                                            <td>@<?php echo htmlspecialchars($o["seller_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($o["product_title"]); ?></td>
                                            <td>₱<?php echo number_format((float)$o["total_price"], 2); ?></td>
                                            <td class="income">₱<?php echo number_format((float)$o["seller_earning"], 2); ?></td>
                                            <td class="warning">₱<?php echo number_format((float)$o["platform_fee"], 2); ?></td>
                                            <td><span class="status-badge"><?php echo htmlspecialchars($o["status"]); ?></span></td>
                                            <td><?php echo date("M j, Y", strtotime($o["created_at"])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="9" class="empty-admin">No orders yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($currentPage === "earnings"): ?>

                <div class="table-card">
                    <div class="card-head">
                        <div>
                            <h3>Platform Earnings Breakdown</h3>
                            <p>Each 5% income record connected to an order</p>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Profit ID</th>
                                    <th>Seller</th>
                                    <th>Buyer</th>
                                    <th>Product</th>
                                    <th>Gross Sale</th>
                                    <th>Platform Earned</th>
                                    <th>Seller Received</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($profits && $profits->num_rows > 0): ?>
                                    <?php while($p = $profits->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $p["profit_id"]; ?></td>
                                            <td>@<?php echo htmlspecialchars($p["seller_name"]); ?></td>
                                            <td>@<?php echo htmlspecialchars($p["buyer_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($p["product_title"]); ?></td>
                                            <td>₱<?php echo number_format((float)$p["total_price"], 2); ?></td>
                                            <td class="income">₱<?php echo number_format((float)$p["amount"], 2); ?></td>
                                            <td>₱<?php echo number_format((float)$p["seller_earning"], 2); ?></td>
                                            <td><?php echo date("M j, Y", strtotime($p["created_at"])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="empty-admin">No platform income records yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php endif; ?>

        </section>
    </main>
</div>

</body>
</html>
