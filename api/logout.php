<?php
// api/logout.php
// ----------------------------------------------------------
//  Destroys session, clears remember-me cookie + DB token,
//  then redirects to login page.
// ----------------------------------------------------------

session_start();
require_once __DIR__ . '/../db-config.php';

// ── Remove remember_me token from DB ─────────────────────
if (isset($_COOKIE['remember_token'])) {
    $hash = hash('sha256', $_COOKIE['remember_token']);
    $pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = ?')
        ->execute([$hash]);

    // Expire the cookie
    setcookie('remember_token', '', [
        'expires'  => time() - 3600,
        'path'     => '/~ulkere/M-TSA/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// ── Destroy session ───────────────────────────────────────
session_unset();
session_destroy();

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

header('Location: /~ulkere/M-TSA/index.php');
exit;
