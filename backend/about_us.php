<?php
$contributors = [
    [
        'name' => 'Zyril Zaldua',
        'role' => 'Frontend Developer',
        'image' => 'assets/images/zyril.jpeg',
        'phone' => '0917-824-5312',
        'email' => 'zyril.zaldua@ctu.edu.ph',
        'facebook' => 'https://www.facebook.com/zyril.zaldua.3',
    ],
    [
        'name' => 'Joseph Torrefalma',
        'role' => 'Backend Developer',
        'image' => 'assets/images/jtorz.jpeg',
        'phone' => '0918-442-7681',
        'email' => 'joseph.torrefalma@ctu.edu.ph',
        'facebook' => 'https://www.facebook.com/jtorz.summit99',
    ],
    [
        'name' => 'Ralph Navidad',
        'role' => 'Frontend Developer',
        'image' => 'assets/images/ralph.jpeg',
        'phone' => '0919-556-1437',
        'email' => 'ralphjhualanz.navidad@ctu.edu.ph',
        'facebook' => 'https://www.facebook.com/rnavidad16',
    ],
    [
        'name' => 'Marvin Bonghanoy',
        'role' => 'Frontend Developer',
        'image' => 'assets/images/marvin.jpeg',
        'phone' => '0920-771-8524',
        'email' => 'marvin.bonghanoy@ctu.edu.ph',
        'facebook' => 'https://www.facebook.com/ipabs555',
    ],
    [
        'name' => 'Hezekiah Paloma',
        'role' => 'Frontend Developer',
        'image' => 'assets/images/kia.jpeg',
        'phone' => '0916-238-4425',
        'email' => 'hezekiah.paloma@ctu.edu.ph',
        'facebook' => 'https://www.facebook.com/profile.php?id=61561287823025',
    ],
    [
        'name' => 'Jamaica Tura',
        'role' => 'Frontend Developer',
        'image' => 'assets/images/jamaica.jpeg',
        'phone' => '0921-632-1197',
        'email' => 'jamaica.tura@ctu.edu.ph',
        'facebook' => 'https://www.facebook.com/shamshaaam6',
    ],
];

$vmg = [
    [
        'icon' => 'bi-eye-fill',
        'title' => 'Vision',
        'text' => 'To become a trusted and accessible platform where artists and clients can collaborate creatively through a modern and secure digital environment.',
    ],
    [
        'icon' => 'bi-bullseye',
        'title' => 'Mission',
        'text' => 'To provide artists with an organized commission marketplace that simplifies communication, promotes creativity, and ensures safe transactions between artists and customers.',
    ],
    [
        'icon' => 'bi-trophy-fill',
        'title' => 'Goal',
        'text' => 'To develop a user-friendly commission platform that supports digital artists while giving customers a professional and seamless commissioning experience.',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CommisGrid — About Us</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
:root{
    --bg:#f5f7fc;
    --surface:#ffffff;
    --surface2:#f0f4ff;
    --surface3:#e9efff;
    --border:#dbe4ff;
    --primary:#82B2F9;
    --secondary:#C069DD;
    --third:#CA5FE8;
    --fourth:#C806F9;
    --gradient:linear-gradient(135deg,#82B2F9 0%,#C069DD 35%,#CA5FE8 70%,#C806F9 100%);
    --text:#24324a;
    --muted:#6d7b94;
    --muted2:#94a0b8;
    --nav-width:260px;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;}
.sidebar{width:var(--nav-width);background:white;border-right:1px solid #e6ecff;position:fixed;top:0;left:0;bottom:0;padding:1.5rem 1rem;overflow-y:auto;}
.logo-area{display:flex;flex-direction:column;align-items:center;margin-bottom:2.5rem;}
.logo-box{width:72px;height:72px;border-radius:22px;background:var(--gradient);display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;margin-bottom:1rem;box-shadow:0 15px 40px rgba(192,105,221,0.25);}
.logo-text{font-size:2rem;font-weight:800;background:var(--gradient);background-clip:text;-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.nav-menu{display:flex;flex-direction:column;gap:0.6rem;}
.nav-item{display:flex;align-items:center;gap:0.9rem;padding:1rem 1rem;border-radius:18px;text-decoration:none;color:var(--text);font-size:0.95rem;font-weight:600;transition:0.25s ease;}
.nav-item i{font-size:1.15rem;}
.nav-item:hover{background:#eef3ff;}
.nav-item.active{background:var(--gradient);color:white;}
.sidebar-footer{margin-top:3rem;text-align:center;font-size:0.8rem;color:var(--muted);line-height:1.8;}
.main{flex:1;margin-left:var(--nav-width);padding:2.5rem 3rem;}
.page-hero{display:flex;justify-content:space-between;align-items:center;gap:3rem;margin-bottom:4rem;}
.hero-left{max-width:700px;}
.hero-badge{display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1rem;border-radius:100px;background:#eef3ff;color:#4f7cff;font-size:0.85rem;font-weight:600;margin-bottom:1.3rem;}
.hero-title{font-size:3.5rem;font-weight:800;line-height:1.1;margin-bottom:1rem;color:#2680eb;}
.hero-title span{background:var(--gradient);background-clip:text;-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.hero-sub{color:var(--muted);font-size:1rem;line-height:1.9;max-width:650px;}
.hero-right{flex-shrink:0;}
.hero-circle{width:210px;height:210px;border-radius:50%;background:var(--gradient);display:flex;align-items:center;justify-content:center;color:white;font-size:5rem;box-shadow:0 25px 60px rgba(192,105,221,0.28);}
.section{margin-bottom:5rem;}
.section-head{margin-bottom:2rem;}
.section-tag{color:#5a7cff;font-size:0.85rem;font-weight:700;margin-bottom:0.5rem;letter-spacing:1px;}
.section-title{font-size:2.2rem;font-weight:800;margin-bottom:0.75rem;}
.section-desc{color:var(--muted);line-height:1.8;max-width:720px;}
.contributors-grid,.vmg-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.7rem;}
.contributor-card,.vmg-card{background:white;border-radius:30px;padding:2rem;border:1px solid #e4ebff;transition:0.3s ease;box-shadow:0 8px 24px rgba(0,0,0,0.04);}
.contributor-card:hover,.vmg-card:hover{transform:translateY(-8px);box-shadow:0 18px 45px rgba(130,178,249,0.18);}
.contributor-img{width:120px;height:120px;border-radius:50%;object-fit:cover;border:5px solid #eef3ff;margin-bottom:1.3rem;}
.contributor-name{font-size:1.2rem;font-weight:700;margin-bottom:0.4rem;}
.contributor-role{color:var(--secondary);font-weight:600;margin-bottom:1.2rem;}
.contributor-info{display:flex;flex-direction:column;gap:0.9rem;}
.info-item{display:flex;align-items:flex-start;gap:0.8rem;color:var(--muted);font-size:0.92rem;}
.info-item i{color:#6f63ff;font-size:1rem;margin-top:2px;}
.info-item a{color:#5a67ff;text-decoration:none;font-weight:600;}
.info-item a:hover{text-decoration:underline;}
.vmg-icon{width:70px;height:70px;border-radius:20px;background:var(--gradient);display:flex;align-items:center;justify-content:center;color:white;font-size:1.7rem;margin-bottom:1.3rem;}
.vmg-title{font-size:1.2rem;font-weight:700;margin-bottom:1rem;}
.vmg-text{color:var(--muted);line-height:1.9;}
.footer{text-align:center;padding-top:2rem;border-top:1px solid #e6ecff;color:var(--muted);font-size:0.9rem;}
@media(max-width:1100px){.page-hero{flex-direction:column;align-items:flex-start;}}
@media(max-width:900px){body{flex-direction:column;}.sidebar{position:relative;width:100%;height:auto;}.main{margin-left:0;}}
@media(max-width:700px){.main{padding:2rem 1.2rem;}.hero-title{font-size:2.4rem;}.hero-circle{width:160px;height:160px;font-size:4rem;}.contributors-grid,.vmg-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<aside class="sidebar">
    <div class="logo-area">
        <div class="logo-box"><i class="bi bi-grid-1x2-fill"></i></div>
        <div class="logo-text">CommisGrid</div>
    </div>
    <nav class="nav-menu">
        <a href="index.php" class="nav-item"><i class="bi bi-compass-fill"></i>Explore</a>
        <a href="about_us.php" class="nav-item active"><i class="bi bi-info-circle-fill"></i>About Us</a>
        <a href="backend/profile.php" class="nav-item"><i class="bi bi-person-circle"></i>My Profile</a>
        <a href="backend/wallet.php" class="nav-item"><i class="bi bi-wallet2"></i>Wallet</a>
        <a href="backend/messages.php" class="nav-item"><i class="bi bi-chat-dots-fill"></i>Messages</a>
        <a href="backend/logout.php" class="nav-item"><i class="bi bi-box-arrow-right"></i>Logout</a>
    </nav>
    <div class="sidebar-footer">© 2025 CommisGrid <br>Creative Marketplace Platform</div>
</aside>
<main class="main">
    <section class="page-hero">
        <div class="hero-left">
            <div class="hero-badge"><i class="bi bi-stars"></i>Meet the CommisGrid Contributors</div>
            <h1 class="hero-title">Building a better platform for <span>artists and clients</span></h1>
            <p class="hero-sub">CommisGrid is a commission-based marketplace platform designed to help artists showcase their talents while allowing clients to discover and commission artworks through a modern, secure, and organized environment.</p>
        </div>
        <div class="hero-right"><div class="hero-circle"><i class="bi bi-palette-fill"></i></div></div>
    </section>
    <section class="section">
        <div class="section-head">
            <div class="section-tag">CONTRIBUTORS</div>
            <h2 class="section-title">Meet the Developers</h2>
            <p class="section-desc">The team behind the development, design, implementation, and documentation of the CommisGrid website.</p>
        </div>
        <div class="contributors-grid">
            <?php foreach ($contributors as $contributor): ?>
                <div class="contributor-card">
                    <img src="<?php echo htmlspecialchars($contributor['image']); ?>" class="contributor-img" alt="<?php echo htmlspecialchars($contributor['name']); ?>">
                    <div class="contributor-name"><?php echo htmlspecialchars($contributor['name']); ?></div>
                    <div class="contributor-role"><?php echo htmlspecialchars($contributor['role']); ?></div>
                    <div class="contributor-info">
                        <div class="info-item"><i class="bi bi-telephone-fill"></i><span><?php echo htmlspecialchars($contributor['phone']); ?></span></div>
                        <div class="info-item"><i class="bi bi-envelope-fill"></i><span><?php echo htmlspecialchars($contributor['email']); ?></span></div>
                        <div class="info-item"><i class="bi bi-facebook"></i><a href="<?php echo htmlspecialchars($contributor['facebook']); ?>" target="_blank" rel="noreferrer">Facebook Profile</a></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="section">
        <div class="section-head">
            <div class="section-tag">COMMISGRID</div>
            <h2 class="section-title">Vision, Mission & Goal</h2>
            <p class="section-desc">The guiding principles and purpose behind the development of the CommisGrid platform.</p>
        </div>
        <div class="vmg-grid">
            <?php foreach ($vmg as $item): ?>
                <div class="vmg-card">
                    <div class="vmg-icon"><i class="bi <?php echo htmlspecialchars($item['icon']); ?>"></i></div>
                    <div class="vmg-title"><?php echo htmlspecialchars($item['title']); ?></div>
                    <div class="vmg-text"><?php echo htmlspecialchars($item['text']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <footer class="footer">© 2025 CommisGrid. All Rights Reserved.</footer>
</main>
</body>
</html>