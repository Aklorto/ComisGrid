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

$user_stmt = $conn->prepare("
    SELECT username, balance, total_earned, total_spent, gcash_number
    FROM users
    WHERE user_id = ?
");

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user = $user_stmt->get_result()->fetch_assoc();

$transactions_stmt = $conn->prepare("
    SELECT *
    FROM transactions
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$transactions_stmt->bind_param("i", $user_id);
$transactions_stmt->execute();

$transactions = $transactions_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComisGrid Wallet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/wallet.css">
</head>
<body>

<div class="wallet-wrapper">

    <div class="wallet-topbar">
        <div>
            <h1>ComisGrid Wallet</h1>
            <p>Manage your earnings and transactions.</p>
        </div>

        <a href="dashboard.php" class="back-btn">
            Back to Dashboard
        </a>
    </div>

    <div class="wallet-grid">

        <div class="wallet-balance-card">
            <div class="wallet-label">Available Balance</div>

            <div class="wallet-balance">
                ₱<?php echo number_format($user['balance'] ?? 0, 2); ?>
            </div>

            <div class="wallet-user">
                @<?php echo htmlspecialchars($user['username']); ?>
            </div>

            <div class="wallet-actions">
                <button class="wallet-btn topup-btn" data-bs-toggle="modal" data-bs-target="#topupModal">
                    Top Up
                </button>

                <button class="wallet-btn withdraw-btn" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    Withdraw
                </button>
            </div>
        </div>

        <div class="wallet-side-info">

            <div class="info-card">
                <div class="info-title">GCash Number</div>
                <div class="info-value">
                    <?php echo htmlspecialchars($user['gcash_number'] ?? 'Not Set'); ?>
                </div>
            </div>

            <div class="info-card">
                <div class="info-title">Total Earned</div>
                <div class="info-value positive">
                    ₱<?php echo number_format($user['total_earned'] ?? 0, 2); ?>
                </div>
            </div>

            <div class="info-card">
                <div class="info-title">Total Spent</div>
                <div class="info-value negative">
                    ₱<?php echo number_format($user['total_spent'] ?? 0, 2); ?>
                </div>
            </div>

        </div>

    </div>

    <div class="transactions-card">

        <div class="transactions-header">
            <h3>Transaction History</h3>
        </div>

        <?php if ($transactions->num_rows > 0): ?>

            <div class="transactions-table">

                <?php while($transaction = $transactions->fetch_assoc()): ?>

                    <div class="transaction-row">

                        <div class="transaction-left">
                            <div class="transaction-type">
                                <?php echo htmlspecialchars($transaction['type']); ?>
                            </div>

                            <div class="transaction-desc">
                                <?php echo htmlspecialchars($transaction['description']); ?>
                            </div>

                            <div class="transaction-date">
                                <?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?>
                            </div>
                        </div>

                        <div class="transaction-right <?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                            ₱<?php echo number_format($transaction['amount'], 2); ?>
                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="empty-transactions">
                No transactions yet.
            </div>

        <?php endif; ?>

    </div>

</div>

<script src="../assets/js/wallet.js"></script>

<!-- TOP UP MODAL -->
<div class="modal fade" id="topupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content wallet-modal">
            <div class="modal-header">
                <h5 class="modal-title">Top Up ComisGrid Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="wallet_action.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action_type" value="topup">

                    <label>Amount to Top Up</label>
                    <input type="number" name="amount" step="0.01" min="1" class="form-control mb-3" required>

                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control mb-3">
                        <option value="GCash">GCash</option>
                    </select>

                    <div class="simulation-box">
                        This is a simulation. Once confirmed, the amount will be added to your ComisGrid Wallet.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn confirm-btn">Confirm Top Up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- WITHDRAW MODAL -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content wallet-modal">
            <div class="modal-header">
                <h5 class="modal-title">Withdraw Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="wallet_action.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action_type" value="withdraw">

                    <label>Amount to Withdraw</label>
                    <input type="number" name="amount" step="0.01" min="1" class="form-control mb-3" required>

                    <label>Withdraw To</label>
                    <input type="text" class="form-control mb-3" value="GCash: <?php echo htmlspecialchars($user['gcash_number'] ?? 'Not Set'); ?>" readonly>

                    <div class="simulation-box">
                        This is a simulation. Once confirmed, the amount will be deducted from your wallet.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn confirm-btn">Confirm Withdraw</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
