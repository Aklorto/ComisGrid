<?php
// ComisGrid landing page: login, register, and forgot password UI only.
// Connect this later to your PHP/MySQL backend.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ComisGrid | Login</title>

    <!-- Bootstrap Icons only, for clean input icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <div class="image-panel">
                <div class="brand-box">
                    <div class="logo-mark">CG</div>
                    <div>
                        <h1>ComisGrid</h1>
                        <p>Discover artists. Commission ideas. Create together.</p>
                    </div>
                </div>

                <div class="image-placeholder">
                    <i class="bi bi-image"></i>
                    <p>Place your artwork / artist image here later</p>
                </div>
            </div>

            <div class="form-panel">
                <div class="form-wrapper" id="formWrapper">
                    <!-- LOGIN FORM -->
                    <form class="form-box login-form active" id="loginForm" action="backend/login.php" method="POST">
                        <span class="mini-label">Welcome back</span>
                        <h2>Login to ComisGrid</h2>
                        <p class="subtitle">Continue buying, selling, and chatting with artists.</p>

                        <label>Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <label>Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>

                        <div class="form-row">
                            <label class="remember">
                                <input type="checkbox" name="remember">
                                Remember me
                            </label>
                            <button type="button" class="link-btn" id="showForgot">Forgot password?</button>
                        </div>

                        <button type="submit" class="primary-btn">Login</button>

                        <p class="switch-text">
                            No account yet?
                            <button type="button" class="link-btn strong" id="showRegister">Create account</button>
                        </p>
                    </form>

                    <!-- REGISTER FORM -->
                    <form class="form-box register-form" id="registerForm" action="backend/register.php" method="POST">
                        <span class="mini-label">Join the grid</span>
                        <h2>Create Account</h2>
                        <p class="subtitle">One account lets you buy, sell, post, and message.</p>

                        <label>Full Name</label>
                        <div class="input-group">
                            <i class="bi bi-person"></i>
                            <input type="text" name="full_name" placeholder="Enter your full name" required>
                        </div>

                        <label>Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <label>Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="password" placeholder="Create a password" required>
                        </div>

                        <label>Confirm Password</label>
                        <div class="input-group">
                            <i class="bi bi-shield-lock"></i>
                            <input type="password" name="confirm_password" placeholder="Confirm your password" required>
                        </div>

                        <button type="submit" class="primary-btn">Register</button>

                        <p class="switch-text">
                            Already have an account?
                            <button type="button" class="link-btn strong" id="showLogin">Login</button>
                        </p>
                    </form>

                    <!-- FORGOT PASSWORD FORM -->
                    <form class="form-box forgot-form" id="forgotForm" action="backend/forgot_password.php" method="POST">
                        <span class="mini-label">Account recovery</span>
                        <h2>Forgot Password</h2>
                        <p class="subtitle">Enter your email. For simulation, this can show a reset confirmation later.</p>

                        <label>Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope-exclamation"></i>
                            <input type="email" name="email" placeholder="Enter your registered email" required>
                        </div>

                        <button type="submit" class="primary-btn">Send Reset Link</button>

                        <p class="switch-text">
                            Remembered your password?
                            <button type="button" class="link-btn strong" id="backToLogin">Back to login</button>
                        </p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="assets/js/auth.js"></script>
</body>
</html>
