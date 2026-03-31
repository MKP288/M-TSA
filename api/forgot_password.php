<?php
// api/forgot_password.php
// ----------------------------------------------------------
//  Generates a reset token, stores it, and emails a link.
//  Returns: { success: bool, message: string }
//
//  NOTE: You must configure PHPMailer (or mail()) with your
//  hosting SMTP settings for emails to actually send.
// ----------------------------------------------------------

header('Content-Type: application/json');
require_once __DIR__ . '/../db-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$body  = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($body['email'] ?? ''));

// Always return the same message to prevent email enumeration
$generic = 'If that email is registered, a reset link has been sent.';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => true, 'message' => $generic]);
    exit;
}

$stmt = $pdo->prepare('SELECT id, first_name FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?')
        ->execute([$token, $expires, $user['id']]);

    // ── Send email ─────────────────────────────────────────
    $resetLink = "http://cas.mcmaster.ca/~ulkere/M-TSA/api/reset_password.php?token=$token";

    $subject = 'Reset your McMaster Turkish Society password';
    $message = "Hi {$user['first_name']},\n\n"
             . "Click the link below to reset your password. "
             . "This link expires in 1 hour.\n\n"
             . $resetLink . "\n\n"
             . "If you did not request this, ignore this email.\n\n"
             . "— McMaster Turkish Society";

    $headers = "From: noreply@cas.mcmaster.ca\r\nX-Mailer: PHP/" . phpversion();

    mail($email, $subject, $message, $headers);
    // For production, swap mail() with PHPMailer + your SMTP credentials
}

echo json_encode(['success' => true, 'message' => $generic]);
