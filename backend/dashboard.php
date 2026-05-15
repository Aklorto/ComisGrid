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

$currentUserId = $_SESSION['user_id'];

$userBalanceQuery = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$userBalanceQuery->bind_param("i", $currentUserId);
$userBalanceQuery->execute();
$currentUser = $userBalanceQuery->get_result()->fetch_assoc();
$currentBalance = $currentUser['balance'] ?? 0;
$purchaseSuccess = isset($_GET['purchase']) && $_GET['purchase'] === 'success';
$purchaseAmount = $_GET['amount'] ?? 0;


$artworksQuery = "
SELECT 
    products.product_id,
    products.seller_id,
    products.title,
    products.category,
    products.description,
    products.price,
    products.cover_image,
    products.status,
    products.created_at,
    users.username
FROM products
JOIN users ON products.seller_id = users.user_id
WHERE products.status = 'Available'
ORDER BY products.created_at DESC
";

$artworksResult = mysqli_query($conn, $artworksQuery);

if (!$artworksResult) {
    die('Dashboard query failed: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComisGrid | Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <img src="../assets/images/comisgridlogo1.png" alt="ComisGrid Logo">
            <h3>ComisGrid</h3>
        </div>

        <nav class="nav-menu">
            <a href="dashboard.php" class="active"><i class="bi bi-compass"></i> Explore</a>
            <a href="profile.php"><i class="bi bi-person-circle"></i> My Profile</a>
            <a href="wallet.php"><i class="bi bi-wallet2"></i> Wallet</a>
            <a href="messages.php"><i class="bi bi-chat-dots"></i> Messages</a>
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOP BAR -->
        <header class="topbar">
            <div>
                <h1>Explore Artworks</h1>
                <p>Buy, commission, and discover creative works from artists.</p>
            </div>

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Search artworks...">
            </div>
        </header>

        <!-- CATEGORY FILTERS -->
        <div class="filter-row">
            <button class="filter-btn active" onclick="filterArt('all')">All</button>
            <button class="filter-btn" onclick="filterArt('digital')">Digital Art</button>
            <button class="filter-btn" onclick="filterArt('anime')">Anime</button>
            <button class="filter-btn" onclick="filterArt('portrait')">Portrait</button>
            <button class="filter-btn" onclick="filterArt('logo')">Logo</button>
        </div>

        <!-- ARTWORK GRID -->
        <section class="art-grid" id="artGrid">
<?php while ($art = mysqli_fetch_assoc($artworksResult)): ?>
    <div class="art-card" data-category="<?php echo htmlspecialchars($art['category']); ?>" data-title="<?php echo htmlspecialchars($art['title']); ?>">
        <img 
    src="<?php echo htmlspecialchars($art['cover_image']); ?>" 
    alt="Artwork"
    onclick="openProductPreview(
        '<?php echo htmlspecialchars(addslashes($art['title'])); ?>',
        '<?php echo htmlspecialchars(addslashes($art['username'])); ?>',
        '<?php echo htmlspecialchars($art['cover_image']); ?>',
        '<?php echo htmlspecialchars(addslashes($art['description'] ?? 'No description provided.')); ?>',
        <?php echo $art['price']; ?>
    )"
>

        <div class="art-info">
            <div>
                <h4><?php echo htmlspecialchars($art['title']); ?></h4>
                <p>by <a href="profile.php?user_id=<?php echo $art['seller_id']; ?>">
        <?php echo htmlspecialchars($art['username']); ?>
    </a>
</p>
            </div>

            <span class="price">₱<?php echo number_format($art['price'], 2); ?></span>
        </div>

        <div class="card-actions">
            <button type="button"><i class="bi bi-heart"></i></button>
            <button type="button"><i class="bi bi-bookmark"></i></button>

           <?php if ($_SESSION['user_id'] == $art['seller_id']): ?>
    <button class="buy-btn" disabled>Own Product</button>
<?php else: ?>
    <button 
        type="button" 
        class="buy-btn"
        onclick="openPaymentModal(
            <?php echo $art['product_id']; ?>,
            '<?php echo htmlspecialchars(addslashes($art['title'])); ?>',
            '<?php echo htmlspecialchars(addslashes($art['username'])); ?>',
            <?php echo $art['price']; ?>
        )"
    >
        Buy
    </button>
<?php endif; ?>
        </div>

        
    </div>
<?php endwhile; ?>

            

        </section>

    </main>
</div>

<!-- FLOATING UPLOAD BUTTON -->
<button class="floating-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
    <i class="bi bi-plus-lg"></i>
</button>

<!-- BUY MODAL -->
<div class="modal fade" id="buyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">Checkout Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Artwork:</strong> <span id="modalArtwork"></span></p>
                <p><strong>Artist:</strong> <span id="modalArtist"></span></p>

                <hr>

                <div class="breakdown">
                    <div>
                        <span>Buyer pays</span>
                            <strong>₱<span id="modalPrice"></span></strong>
                    </div>
                    <div>
                        <span>Platform fee 5%</span>
                        <strong>₱<span id="modalFee"></span></strong>
                    </div>
                    <div>
                        <span>Artist receives</span>
                        <strong>₱<span id="modalArtistEarn"></span></strong>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn cancel-btn" data-bs-dismiss="modal">Cancel</button>
                <button class="btn confirm-btn">Confirm Purchase</button>
            </div>
        </div>
    </div>
</div>

<!-- UPLOAD MODAL -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title">Upload Artwork</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="upload_artwork.php" method="POST" enctype="multipart/form-data">
    <label>Artwork Title</label>
    <input type="text" name="title" class="form-control mb-3" placeholder="Enter artwork title">

    <label>Category</label>
    <select name="category" class="form-control mb-3">
        <option value="Digital Art">Digital Art</option>
        <option value="Anime">Anime</option>
        <option value="Portrait">Portrait</option>
        <option value="Logo">Logo</option>
    </select>

    <label>Price in Pesos</label>
    <input type="number" name="price" step="0.01" class="form-control mb-3" placeholder="Example: 500">

    <label>Artwork Image</label>
    <input type="file" name="artwork_image" class="form-control mb-3" accept="image/*">

    <label>Description</label>
    <textarea name="description" class="form-control" rows="3" placeholder="Describe your artwork or commission offer"></textarea>

    <button type="submit" class="btn confirm-btn mt-3">Post Artwork</button>
</form>
            </div>

        </div>
    </div>
</div>
<?php if ($purchaseSuccess): ?>

<div class="success-overlay" id="successOverlay">
    <div class="success-box">

        <div class="success-check">
            <i class="bi bi-check-lg"></i>
        </div>

        <h2>Payment Successful</h2>

        <p>
            Your payment of 
            <strong>₱<?php echo number_format((float)$purchaseAmount, 2); ?></strong>
            was successfully processed.
        </p>

        <button onclick="closeSuccessModal()" class="success-btn">
            Continue
        </button>

    </div>
</div>

<?php endif; ?>
<script>
    const currentUserBalance = <?php echo json_encode((float)$currentBalance); ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard.js"></script>

<!-- PRODUCT PREVIEW MODAL -->
<div class="modal fade" id="productPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content preview-modal">
            <div class="preview-layout">
                <div class="preview-image-wrap">
                    <img id="previewImage" src="" alt="Artwork Preview">
                </div>

                <div class="preview-info">
                    <button type="button" class="btn-close preview-close" data-bs-dismiss="modal"></button>

                    <h2 id="previewTitle"></h2>
                    <p class="preview-artist">by <span id="previewArtist"></span></p>

                    <div class="preview-price">₱<span id="previewPrice"></span></div>

                    <hr>

                    <h6>Description</h6>
                    <p id="previewDescription"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAYMENT MODAL -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content payment-modal">

            <form action="buy_product.php" method="POST">
                <input type="hidden" name="product_id" id="paymentProductId">

                <div class="payment-root">

                    <div class="payment-left">
                        <div class="pay-title">Pay with</div>

                       <div class="pay-section">Card</div>

<div class="pay-method disabled"
onclick="alert('Debit/Credit Card is currently in UI simulation mode. Please use ComisGrid Wallet.')">
    <span>Debit / Credit card</span>
    <small>Coming soon</small>
</div>

<div class="pay-section">Online Banking</div>

<div class="pay-method disabled"
onclick="alert('Online Banking is currently in UI simulation mode. Please use ComisGrid Wallet.')">
    <span>Online banking</span>
    <small>Coming soon</small>
</div>

<div class="pay-section">E-Wallet</div>

<div class="pay-method disabled"
onclick="alert('GCash/Maya/PayPal is currently in UI simulation mode. Please use ComisGrid Wallet.')">
    <span>GCash / Maya / PayPal</span>
    <small>Coming soon</small>
</div>



                        <div class="pay-section">ComisGrid</div>
                        <div class="pay-method active">
                            <span>ComisGrid Wallet</span>
                            <small>Available</small>
                        </div>

                        <div class="payment-summary">
                            <div><span>Artwork Price</span><strong>₱<span id="paySubtotal"></span></strong></div>
                            <div><span>Platform Fee 5%</span><strong>₱<span id="payFee"></span></strong></div>
                            <div><span>Artist Receives</span><strong>₱<span id="payArtistEarn"></span></strong></div>
                            <div class="total"><span>Total</span><strong>₱<span id="payTotal"></span></strong></div>
                        </div>
                    </div>

                    <div class="payment-right">
                        <button type="button" class="btn-close payment-close" data-bs-dismiss="modal"></button>

                        <h4>ComisGrid Wallet</h4>
                        <p class="payment-subtitle">Your linked wallet</p>

                        <div class="wallet-card">
                            <div class="wallet-top">
                                <div class="wallet-logo">CG</div>
                                <div>
                                    <strong>ComisGrid Wallet</strong>
                                    <p>Instant internal payment</p>
                                </div>
                            </div>

                            <div class="wallet-balance">
                                <span>Available balance</span>
                                <strong>₱<span id="walletBalance">0.00</span></strong>
                            </div>

                            <p class="after-payment">
                                After payment, your balance will be 
                                <strong>₱<span id="afterBalance">0.00</span></strong>.
                            </p>
                        </div>

<button type="submit" class="wallet-pay-btn" id="walletPayBtn">
    Pay ₱<span id="payButtonAmount"></span> with CG Wallet
</button>

<div class="payment-error" id="paymentError"></div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script src="../assets/js/social_actions.js"></script>
</body>
</html>