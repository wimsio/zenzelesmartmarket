<?php
// ===== ZENZELE — AUTH API =====
// POST /php/auth.php?action=register
// POST /php/auth.php?action=login
// POST /php/auth.php?action=me  (requires Bearer token)

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/jwt.php';

$action = $_GET['action'] ?? '';

// ---- REGISTER ----
if ($action === 'register') {
    requireMethod('POST');
    $b = getBody();

    $name     = sanitize($b['name']     ?? '');
    $email    = strtolower(trim($b['email'] ?? ''));
    $password = $b['password'] ?? '';
    $country  = sanitize($b['country']  ?? '');
    $city     = sanitize($b['city']     ?? '');
    $category = sanitize($b['category'] ?? '');
    $bio      = sanitize($b['bio']      ?? '');

    // Validation
    if (!$name || !$email || !$password || !$country || !$category || !$bio)
        jsonErr('Missing required fields: name, email, password, country, category, bio');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        jsonErr('Invalid email address');
    if (strlen($password) < 8)
        jsonErr('Password must be at least 8 characters');

    // Check duplicate
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) jsonErr('Email already registered', 409);

    $skills         = isset($b['skills']) ? json_encode(array_map('trim', explode(',', $b['skills']))) : '[]';
    $wallet         = sanitize($b['wallet']          ?? '');
    $trainingWanted = sanitize($b['training_wanted'] ?? '');
    $openForWork    = !empty($b['open_for_work']) ? 1 : 0;
    $hash           = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('
        INSERT INTO users (name, email, password, country, city, category, bio, skills, wallet, training_wanted, open_for_work)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$name, $email, $hash, $country, $city, $category, $bio, $skills, $wallet, $trainingWanted, $openForWork]);
    $userId = (int)$pdo->lastInsertId();

    $token = JWT::encode([
        'sub' => $userId,
        'name' => $name,
        'iat' => time(),
        'exp' => time() + TOKEN_EXPIRY
    ], JWT_SECRET);

    jsonOk(['token' => $token, 'user_id' => $userId, 'name' => $name], 201);
}

// ---- LOGIN ----
if ($action === 'login') {
    requireMethod('POST');
    $b     = getBody();
    $email = strtolower(trim($b['email']    ?? ''));
    $pass  = $b['password'] ?? '';

    if (!$email || !$pass) jsonErr('Email and password required');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($pass, $user['password']))
        jsonErr('Invalid email or password', 401);

    // Update last seen (optional)
    $pdo->prepare('UPDATE users SET views = views + 0 WHERE id = ?')->execute([$user['id']]);

    $token = JWT::encode([
        'sub'  => $user['id'],
        'name' => $user['name'],
        'iat'  => time(),
        'exp'  => time() + TOKEN_EXPIRY
    ], JWT_SECRET);

    unset($user['password']);
    $user['skills'] = json_decode($user['skills'] ?? '[]', true);
    jsonOk(['token' => $token, 'user' => $user]);
}

// ---- ME (get current user from token) ----
if ($action === 'me') {
    requireMethod('GET');
    $user = requireAuth($pdo);
    unset($user['password']);
    $user['skills'] = json_decode($user['skills'] ?? '[]', true);

    // Attach NFTs
    $stmt = $pdo->prepare('SELECT * FROM nfts WHERE user_id = ? ORDER BY minted_at DESC');
    $stmt->execute([$user['id']]);
    $user['nfts'] = $stmt->fetchAll();

    jsonOk($user);
}

jsonErr('Unknown action. Use ?action=register|login|me', 404);
