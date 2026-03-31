<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../db-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// ── Inputs ────────────────────────────────────────────────
$firstName = trim($body['first_name'] ?? '');
$lastName  = trim($body['last_name'] ?? '');
$email     = strtolower(trim($body['email'] ?? ''));
$password  = $body['password'] ?? '';
$password2 = $body['password2'] ?? '';

// ── Validate ──────────────────────────────────────────────
$errors = [];

if (empty($firstName) || empty($lastName)) {
    $errors[] = 'First and last name are required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}

if ($password !== $password2) {
    $errors[] = 'Passwords do not match.';
}

if ($errors) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Check duplicate ───────────────────────────────────────
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'That email address is already registered.'
    ]);
    exit;
}

// ── Hash + insert ─────────────────────────────────────────
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
$verifyToken = bin2hex(random_bytes(32));

$stmt = $pdo->prepare('
    INSERT INTO users (first_name, last_name, email, password_hash, verify_token, role)
    VALUES (?, ?, ?, ?, ?, ?)
');
$stmt->execute([$firstName, $lastName, $email, $hash, $verifyToken, 'member']);

echo json_encode([
    'success' => true,
    'message' => 'Account created successfully! You can now log in.',
]);