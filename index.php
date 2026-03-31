<?php
// index.php — Login / Sign-up page
session_start();
// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    $map = ['admin' => '/~ulkere/M-TSA/admin/dashboard.php', 'executive' => '/~ulkere/M-TSA/executive/dashboard.php', 'member' => '/~ulkere/M-TSA/member/dashboard.php'];
    header('Location: ' . ($map[$_SESSION['user_role']] ?? '/~ulkere/M-TSA/member/dashboard.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>McMaster Turkish Society — Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <nav>
    <a class="logo">
        <div class="logo-icon"></div>
        <div class="logo-text">McMaster <span>Turkish Society</span></div>
    </a>
    <ul class="nav-links">
        <li><a href="index.html#about"> About</a></li>
        <li><a href="index.html#events"> Events</a></li>
        <li><a href="index.html#gallery"> Gallery</a></li>
        <li><a href="index.html#contact"> Contact</a></li>
        <li><a href="index.php" class="nav-cta">Join Us</a></li>
    </ul>
    <button class="mobile-menu-btn">
        <span></span>
        <span></span>
        <span></span>
    </button>
  </nav>
  <div class="container">
    <div class="header">
      <div class="logo-mark">
        <svg viewBox="0 0 34 34" xmlns="http://www.w3.org/2000/svg" fill="none">
          <path d="M20 6a10 10 0 1 0 0 22 8 8 0 1 1 0-22z"
                stroke="#C9973D" stroke-width="1.5" fill="#C9973D" fill-opacity="0.3"/>
          <circle cx="24" cy="13" r="2" fill="#C9973D"/>
        </svg>
      </div>
      <h1>McMaster Turkish Society</h1>
      <p>Member Portal</p>
    </div>

    <div class="card">

      <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('login')">Log In</button>
        <button class="tab-btn"        onclick="switchTab('signup')">Sign Up</button>
      </div>

      <!-- ── LOGIN ── -->
      <div id="login-section" class="form-section active">

        <div class="alert" id="login-alert"></div>

        <div class="field">
          <label for="login-email">Email Address</label>
          <input type="email" id="login-email"
                placeholder="yourname@mcmaster.ca" autocomplete="email">
        </div>

        <div class="field">
          <label for="login-password">Password</label>
          <div class="password-wrapper">
            <input type="password" id="login-password"
                  placeholder="Enter your password" autocomplete="current-password">
            <button class="toggle-pw" type="button"
                    onclick="togglePw('login-password', this)">Show</button>
          </div>
          <a href="#" class="forgot-link" onclick="handleForgot(event)">
            Forgot password?
          </a>
        </div>

        <!-- Remember Me -->
        <div class="remember-row">
          <input type="checkbox" id="remember-me">
          <label for="remember-me">Keep me logged in for 30 days</label>
        </div>

        <button id="login-btn" class="btn-primary" onclick="handleLogin()">
          Log In to Account
        </button>

        <div class="divider">or continue with</div>

        <button class="btn-social" type="button">
          <svg width="18" height="18" viewBox="0 0 18 18">
            <path d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z" fill="#4285F4"/>
            <path d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2.01c-.72.48-1.63.86-2.7.86a4.98 4.98 0 0 1-4.69-3.44H1.6v2.08A8 8 0 0 0 8.98 17z" fill="#34A853"/>
            <path d="M4.29 10.47A5.02 5.02 0 0 1 4.03 9c0-.51.09-1.01.26-1.47V5.45H1.6A8 8 0 0 0 1 9c0 1.29.31 2.52.6 3.55l2.69-2.08z" fill="#FBBC05"/>
            <path d="M8.98 3.58c1.21 0 2.3.42 3.16 1.23l2.38-2.38A8 8 0 0 0 1.6 5.45l2.69 2.08a4.98 4.98 0 0 1 4.69-3.95z" fill="#EA4335"/>
          </svg>
          Continue with Google
        </button>

        <div class="card-footer">
          Don't have an account?
          <a href="#" onclick="switchTab('signup'); return false;">Sign up here</a>
        </div>

      </div><!-- /login-section -->

      <!-- ── SIGN UP ── -->
      <div id="signup-section" class="form-section">

        <div class="alert" id="signup-alert"></div>

        <div class="field-row">
          <div class="field">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name"
                  placeholder="Ayşe" autocomplete="given-name">
          </div>
          <div class="field">
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name"
                  placeholder="Yıldız" autocomplete="family-name">
          </div>
        </div>

        <div class="field">
          <label for="signup-email">McMaster Email</label>
          <input type="email" id="signup-email"
                placeholder="yourname@mcmaster.ca" autocomplete="email">
        </div>

        <div class="field">
          <label for="student-id">
            Student ID <span class="optional">(optional)</span>
          </label>
          <input type="text" id="student-id"
                placeholder="400123456" maxlength="9">
        </div>

        <div class="field">
          <label for="signup-password">Password</label>
          <div class="password-wrapper">
            <input type="password" id="signup-password"
                  placeholder="Create a strong password"
                  oninput="checkStrength(this.value)"
                  autocomplete="new-password">
            <button class="toggle-pw" type="button"
                    onclick="togglePw('signup-password', this)">Show</button>
          </div>
          <div class="strength-bar">
            <span id="sb1"></span><span id="sb2"></span>
            <span id="sb3"></span><span id="sb4"></span>
          </div>
          <div class="strength-label" id="strength-label"></div>
        </div>

        <div class="field">
          <label for="confirm-password">Confirm Password</label>
          <div class="password-wrapper">
            <input type="password" id="confirm-password"
                  placeholder="Repeat your password"
                  autocomplete="new-password">
            <button class="toggle-pw" type="button"
                    onclick="togglePw('confirm-password', this)">Show</button>
          </div>
        </div>

        <button id="signup-btn" class="btn-primary" onclick="handleSignup()">
          Create Account
        </button>

        <p class="terms-note">
          By signing up you agree to the
          <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
          of McMaster Turkish Society.
        </p>

        <div class="card-footer" style="margin-top:1rem">
          Already have an account?
          <a href="#" onclick="switchTab('login'); return false;">Log in here</a>
        </div>

      </div><!-- /signup-section -->

    </div><!-- /card -->
  </div><!-- /container -->

  <script src="js/auth.js"></script>
</body>
</html>
