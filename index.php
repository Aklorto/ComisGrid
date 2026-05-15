<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComisGrid | Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="main-wrapper">

    <!-- LEFT IMAGE CAROUSEL -->
    <div class="left-panel">
        <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">

            <div class="carousel-inner h-100">

                <div class="carousel-item active h-100">
                    <img src="assets/images/fifthslide.png" class="carousel-img" alt="ComisGrid Slide 1">
                </div>

                <div class="carousel-item h-100">
                    <img src="assets/images/fourthslide.png" class="carousel-img" alt="ComisGrid Slide 2">
                </div>

                <div class="carousel-item h-100">
                    <img src="assets/images/thirdslide.png" class="carousel-img" alt="ComisGrid Slide 3">
                </div>

                <div class="carousel-item h-100">
                    <img src="assets/images/sixthslide.png" class="carousel-img" alt="ComisGrid Slide 3">
                </div>

            </div>



            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>
    </div>

    <!-- RIGHT AUTH PANEL -->
    <div class="right-panel">

        <div class="auth-box">

            <div class="brand-mini">
                <img src="assets/images/comisgridlogo1.png" class="logo-img" alt="ComisGrid Logo">

            </div>

            <!-- SLIDING FORMS CONTAINER -->
            <div class="form-slider" id="formSlider">

                <!-- LOGIN FORM -->
                <div class="form-page">
                    <h1>Welcome Back!</h1>
                    <p class="subtitle">Login to explore artworks, commissions, and chats.</p>

                    <form id="loginForm" action="backend/login.php" method="POST">
                        <div class="mb-3">
                            <label>Email or Username</label>
                            <input type="text" class="form-control" name="login_input" placeholder="Enter your email or username">
                        </div>

                        <div class="mb-2">
                            <label>Password</label>
                            <div class="password-box">
                                <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password">
                                <button type="button" onclick="togglePassword('loginPassword')">Show</button>
                            </div>
                        </div>

                        <div class="forgot-link">
                            <button type="button" onclick="showForgot()">Forgot password?</button>
                        </div>

                       <button type="submit" class="main-btn">LOGIN</button>
                    </form>

                    <p class="switch-text">
                        Don't have an account?
                        <button onclick="showRegister()">Register here</button>
                    </p>
                </div>

                <!-- REGISTER FORM -->
                <div class="form-page">
                    <h1>Create Account</h1>
                    <p class="subtitle">Join ComisGrid and start buying or selling art.</p>

                    <form id="registerForm" action="backend/register.php" method="POST" enctype="multipart/form-data">

    <div class="register-step active" id="registerStep1">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" class="form-control" name="fullname" placeholder="Enter your full name">
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" name="username" placeholder="Choose a username">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="Enter your email">
        </div>

        <div class="mb-3">
    <label>GCash Number for Wallet/Withdrawal</label>
    <input type="text" class="form-control" name="gcash_number" maxlength="11" placeholder="09XXXXXXXXX">
</div>

        <button type="button" class="main-btn" onclick="nextRegisterStep()">NEXT</button>
    </div>

    <div class="register-step" id="registerStep2" style="display: none;">
        <div class="mb-3">
            <label>Profile Picture</label>
            <input type="file" class="form-control" name="profile_image" accept="image/*">
        </div>

        <div class="mb-3">
            <label>Facebook Link Optional</label>
            <input type="url" class="form-control" name="facebook_link" placeholder="Facebook profile link">
        </div>

        <div class="mb-3">
            <label>Instagram Link Optional</label>
            <input type="url" class="form-control" name="instagram_link" placeholder="Instagram profile link">
        </div>

        <div class="mb-3">
            <label>X/Twitter Link Optional</label>
            <input type="url" class="form-control" name="x_link" placeholder="X/Twitter profile link">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <div class="password-box">
                <input type="password" class="form-control" id="registerPassword" name="password" placeholder="Create password">
                <button type="button" onclick="togglePassword('registerPassword')">Show</button>
            </div>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <div class="password-box">
                <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" placeholder="Confirm password">
                <button type="button" onclick="togglePassword('registerConfirmPassword')">Show</button>
            </div>
        </div>

        <div class="register-actions">
            <button type="button" class="secondary-btn" onclick="prevRegisterStep()">BACK</button>
            <button type="submit" class="main-btn">REGISTER</button>
        </div>
    </div>

</form>

                    <p class="switch-text">
                        Already have an account?
                        <button onclick="showLogin()">Login here</button>
                    </p>
                </div>

                <!-- FORGOT PASSWORD FORM -->
                <div class="form-page">
                    <h1>Forgot Password</h1>
                    <p class="subtitle">Enter your email and we’ll simulate sending a reset link.</p>

                        <form id="forgotForm" action="#" method="POST">
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter your registered email">
                        </div>

                        <button type="submit" class="main-btn">SEND RESET LINK</button>
                    </form>

                    <p class="switch-text">
                        Remembered your password?
                        <button onclick="showLogin()">Back to login</button>
                    </p>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/script.js"></script>
</body>
</html>