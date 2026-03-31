<?php
// api/reset_password.php
// ----------------------------------------------------------
//  GET  → show the reset form (token passed in URL)
//  POST → validate token + update password
// ----------------------------------------------------------

session_start();
require_once __DIR__ . '/../db-config.php';

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');

// ── POST: update password ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $body      = json_decode(file_get_contents('php://input'), true);
    $token     = trim($body['token']    ?? '');
    $password  = $body['password']  ?? '';
    $password2 = $body['password2'] ?? '';

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        exit;
    }
    if ($password !== $password2) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    $stmt = $pdo->prepare('
        SELECT id FROM users
        WHERE reset_token = ?
          AND reset_expires > NOW()
        LIMIT 1
    ');
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'This reset link is invalid or has expired.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare('UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?')
        ->execute([$hash, $user['id']]);

    echo json_encode(['success' => true, 'message' => 'Password updated! You can now log in.']);
    exit;
}

// ── GET: validate token before showing form ───────────────
$valid = false;
if ($token) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1');
    $stmt->execute([$token]);
    $valid = (bool)$stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — MTS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo-mark">
      <svg viewBox="0 0 34 34" xmlns="http://www.w3.org/2000/svg" fill="none">
        <path d="M20 6a10 10 0 1 0 0 22 8 8 0 1 1 0-22z" stroke="#C9973D" stroke-width="1.5" fill="#C9973D" fill-opacity="0.3"/>
        <circle cx="24" cy="13" r="2" fill="#C9973D"/>
      </svg>
    </div>
    <h1>McMaster Turkish Society</h1>
    <p>Reset Your Password</p>
  </div>

  <div class="card">
    <?php if (!$valid): ?>
      <div class="alert error" style="display:block">
        This reset link is invalid or has expired. Please request a new one.
      </div>
      <div class="card-footer" style="margin-top:1rem">
        <a href="/~ulkere/M-TSA/index.php">Back to Login</a>
      </div>
    <?php else: ?>
      <div class="alert" id="reset-alert"></div>

      <div class="field">
        <label for="new-password">New Password</label>
        <div class="password-wrapper">
          <input type="password" id="new-password" placeholder="At least 8 characters"
                 oninput="checkStrength(this.value)">
          <button class="toggle-pw" type="button" onclick="togglePw('new-password',this)">Show</button>
        </div>
        <div class="strength-bar">
          <span id="sb1"></span><span id="sb2"></span>
          <span id="sb3"></span><span id="sb4"></span>
        </div>
        <div class="strength-label" id="strength-label"></div>
      </div>

      <div class="field">
        <label for="confirm-new-password">Confirm New Password</label>
        <div class="password-wrapper">
          <input type="password" id="confirm-new-password" placeholder="Repeat new password">
          <button class="toggle-pw" type="button" onclick="togglePw('confirm-new-password',this)">Show</button>
        </div>
      </div>

      <button id="reset-btn" class="btn-primary" onclick="handleReset()">Update Password</button>
    <?php endif; ?>
  </div>
</div>

<script src="../js/auth.js"></script>
<script>
async function handleReset() {
  const btn = document.getElementById('reset-btn');
  const pw  = document.getElementById('new-password').value;
  const pw2 = document.getElementById('confirm-new-password').value;
  clearAlert('reset-alert');

  if (pw.length < 8) { showAlert('reset-alert','Password must be at least 8 characters.','error'); return; }
  if (pw !== pw2)    { showAlert('reset-alert','Passwords do not match.','error'); return; }

  setLoading(btn, true);
  try {
    const data = await postJSON('reset_password.php', {
      token: '<?= htmlspecialchars($token) ?>',
      password: pw, password2: pw2
    });
    if (data.success) {
      showAlert('reset-alert', data.message, 'success');
      setTimeout(() => window.location.href = '/~ulkere/M-TSA/index.php', 2000);
    } else {
      showAlert('reset-alert', data.message, 'error');
    }
  } catch(e) {
    showAlert('reset-alert','Connection error. Please try again.','error');
  } finally {
    setLoading(btn, false);
  }
}
</script>
</body>
</html>
