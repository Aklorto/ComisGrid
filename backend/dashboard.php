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
            <img src="assets/images/comisgridlogo1.png" alt="ComisGrid Logo">
            <h3>ComisGrid</h3>
        </div>

        <nav class="nav-menu">
            <a href="dashboard.php" class="active"><i class="bi bi-compass"></i> Explore</a>
            <a href="#"><i class="bi bi-person-circle"></i> My Profile</a>
            <a href="#"><i class="bi bi-plus-square"></i> Upload Artwork</a>
            <a href="#"><i class="bi bi-wallet2"></i> Wallet</a>
            <a href="#"><i class="bi bi-chat-dots"></i> Messages</a>
            <a href="#"><i class="bi bi-bag-check"></i> Orders</a>
            <a href="index.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
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

            <div class="art-card" data-category="anime" data-title="Anime Character Commission">
                <img src="../assets/images/1.jpeg" alt="Artwork">
                <div class="art-info">
                    <div>
                        <h4>Anime Character Commission</h4>
                        <p>by Mika Arts</p>
                    </div>
                    <span class="price">25 pts</span>
                </div>
                <div class="card-actions">
                    <button><i class="bi bi-heart"></i></button>
                    <button><i class="bi bi-bookmark"></i></button>
                    <button class="buy-btn" onclick="openBuyModal('Anime Character Commission', 25, 'Mika Arts')">Buy</button>
                </div>
            </div>

            <div class="art-card tall" data-category="digital" data-title="Fantasy Digital Painting">
                <img src="../assets/images/2.jpeg" alt="Artwork">
                <div class="art-info">
                    <div>
                        <h4>Fantasy Digital Painting</h4>
                        <p>by Carlo Designs</p>
                    </div>
                    <span class="price">40 pts</span>
                </div>
                <div class="card-actions">
                    <button><i class="bi bi-heart"></i></button>
                    <button><i class="bi bi-bookmark"></i></button>
                    <button class="buy-btn" onclick="openBuyModal('Fantasy Digital Painting', 40, 'Carlo Designs')">Buy</button>
                </div>
            </div>

            <div class="art-card" data-category="portrait" data-title="Realistic Portrait">
                <img src="../assets/images/3.jpeg" alt="Artwork">
                <div class="art-info">
                    <div>
                        <h4>Realistic Portrait</h4>
                        <p>by Jana Studio</p>
                    </div>
                    <span class="price">35 pts</span>
                </div>
                <div class="card-actions">
                    <button><i class="bi bi-heart"></i></button>
                    <button><i class="bi bi-bookmark"></i></button>
                    <button class="buy-btn" onclick="openBuyModal('Realistic Portrait', 35, 'Jana Studio')">Buy</button>
                </div>
            </div>

            <div class="art-card wide" data-category="logo" data-title="Logo Design Package">
                <img src="../assets/images/4.jpeg" alt="Artwork">
                <div class="art-info">
                    <div>
                        <h4>Logo Design Package</h4>
                        <p>by GridWorks</p>
                    </div>
                    <span class="price">30 pts</span>
                </div>
                <div class="card-actions">
                    <button><i class="bi bi-heart"></i></button>
                    <button><i class="bi bi-bookmark"></i></button>
                    <button class="buy-btn" onclick="openBuyModal('Logo Design Package', 30, 'GridWorks')">Buy</button>
                </div>
            </div>

            <div class="art-card tall" data-category="anime" data-title="Chibi Avatar">
                <img src="../assets/images/5.jpeg" alt="Artwork">
                <div class="art-info">
                    <div>
                        <h4>Chibi Avatar</h4>
                        <p>by Kira Draws</p>
                    </div>
                    <span class="price">15 pts</span>
                </div>
                <div class="card-actions">
                    <button><i class="bi bi-heart"></i></button>
                    <button><i class="bi bi-bookmark"></i></button>
                    <button class="buy-btn" onclick="openBuyModal('Chibi Avatar', 15, 'Kira Draws')">Buy</button>
                </div>
            </div>

            <div class="art-card" data-category="digital" data-title="Abstract Artwork">
                <img src="../assets/images/6.jpeg" alt="Artwork">
                <div class="art-info">
                    <div>
                        <h4>Abstract Artwork</h4>
                        <p>by PixelDream</p>
                    </div>
                    <span class="price">20 pts</span>
                </div>
                <div class="card-actions">
                    <button><i class="bi bi-heart"></i></button>
                    <button><i class="bi bi-bookmark"></i></button>
                    <button class="buy-btn" onclick="openBuyModal('Abstract Artwork', 20, 'PixelDream')">Buy</button>
                </div>
            </div>

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
                        <strong><span id="modalPrice"></span> pts</strong>
                    </div>
                    <div>
                        <span>Platform fee 5%</span>
                        <strong><span id="modalFee"></span> pts</strong>
                    </div>
                    <div>
                        <span>Artist receives</span>
                        <strong><span id="modalArtistEarn"></span> pts</strong>
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
                <form>
                    <label>Artwork Title</label>
                    <input type="text" class="form-control mb-3" placeholder="Enter artwork title">

                    <label>Category</label>
                    <select class="form-control mb-3">
                        <option>Digital Art</option>
                        <option>Anime</option>
                        <option>Portrait</option>
                        <option>Logo</option>
                    </select>

                    <label>Price in Points</label>
                    <input type="number" class="form-control mb-3" placeholder="Example: 25">

                    <label>Artwork Image</label>
                    <input type="file" class="form-control mb-3">

                    <label>Description</label>
                    <textarea class="form-control" rows="3" placeholder="Describe your artwork or commission offer"></textarea>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn cancel-btn" data-bs-dismiss="modal">Cancel</button>
                <button class="btn confirm-btn">Post Artwork</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>