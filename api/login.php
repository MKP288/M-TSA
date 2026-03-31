<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

session_start();
require_once __DIR__ . '/../db-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Parse body ────────────────────────────────────────────
$body       = json_decode(file_get_contents('php://input'), true);
$email      = strtolower(trim($body['email'] ?? ''));
$password   = $body['password'] ?? '';
$rememberMe = !empty($body['remember_me']);

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

// ── Rate limiting: 5 attempts per email per 15 minutes ───
$stmt = $pdo->prepare('
    SELECT COUNT(*) AS cnt
    FROM login_attempts
    WHERE email = ?
      AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
');
$stmt->execute([$email]);

if ((int)$stmt->fetch()['cnt'] >= 5) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Too many login attempts. Please wait 15 minutes and try again.'
    ]);
    exit;
}

// ── Log the attempt ───────────────────────────────────────
$pdo->prepare('INSERT INTO login_attempts (email) VALUES (?)')
    ->execute([$email]);

// ── Fetch user ────────────────────────────────────────────
$stmt = $pdo->prepare('
    SELECT id, first_name, last_name, email, password_hash, role, is_verified
    FROM users
    WHERE email = ?
    LIMIT 1
');
$stmt->execute([$email]);
$user = $stmt->fetch();

// ── Verify password ───────────────────────────────────────
if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
    exit;
}

// ── Rehash if needed ──────────────────────────────────────
if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
        ->execute([$newHash, $user['id']]);
}

// ── Start session ─────────────────────────────────────────
session_regenerate_id(true);
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['first_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role'];

// ── Remember Me cookie ────────────────────────────────────
if ($rememberMe) {
    $rawToken  = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $rawToken);
    $expires   = date('Y-m-d H:i:s', strtotime('+30 days'));

    $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?')
        ->execute([$user['id']]);

    $pdo->prepare('
        INSERT INTO remember_tokens (user_id, token_hash, expires_at)
        VALUES (?, ?, ?)
    ')->execute([$user['id'], $tokenHash, $expires]);

    setcookie(
        'remember_token',
        $rawToken,
        [
            'expires'  => strtotime('+30 days'),
            'path'     => '/~ulkere/M-TSA/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

// ── Role-based redirect ───────────────────────────────────
$basePath = '/~ulkere/M-TSA';

$redirectMap = [
    'admin'     => $basePath . '/admin/dashboard.php',
    'executive' => $basePath . '/executive/dashboard.php',
    'member'    => $basePath . '/member/dashboard.php',
];

$redirect = $redirectMap[$user['role']] ?? $basePath . '/member/dashboard.php';

echo json_encode([
    'success'  => true,
    'message'  => 'Welcome back, ' . htmlspecialchars($user['first_name']) . '!',
    'role'     => $user['role'],
    'redirect' => $redirect,
]);