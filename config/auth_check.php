<?php
// config/auth_check.php
// ----------------------------------------------------------
//  Include at the TOP of any page that requires login.
//  Handles remember-me auto-login before checking session.
//
//  Usage:
//    require_once __DIR__ . '/config/auth_check.php';
//
//  To restrict to a specific role:
//    require_role('admin');   or   require_role('executive');
// ----------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db-config.php';

// ── Auto-login via remember_me cookie ────────────────────
if (empty($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $hash = hash('sha256', $_COOKIE['remember_token']);

    $stmt = $pdo->prepare('
        SELECT u.id, u.first_name, u.email, u.role
        FROM   remember_tokens rt
        JOIN   users u ON u.id = rt.user_id
        WHERE  rt.token_hash = ?
          AND  rt.expires_at > NOW()
        LIMIT  1
    ');
    $stmt->execute([$hash]);
    $user = $stmt->fetch();

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['first_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
    }
}

// ── Redirect to login if not authenticated ────────────────
function require_login(string $loginPage = '/~ulkere/M-TSA/index.php'): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . $loginPage);
        exit;
    }
}

// ── Redirect if role is insufficient ─────────────────────
function require_role(string $role, string $fallback = '/~ulkere/M-TSA/member/dashboard.php'): void {
    require_login();

    $hierarchy = ['member' => 1, 'executive' => 2, 'admin' => 3];
    $userLevel = $hierarchy[$_SESSION['user_role']] ?? 0;
    $required  = $hierarchy[$role] ?? 99;

    if ($userLevel < $required) {
        header('Location: ' . $fallback);
        exit;
    }
}
