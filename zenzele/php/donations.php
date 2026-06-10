<?php
// ===== ZENZELE — DONATIONS API =====
// POST /php/donations.php?action=donate    → send donation (auth)
// GET  /php/donations.php?user_id=X        → donations received by user
// GET  /php/donations.php?action=sent      → donations I sent (auth)
// GET  /php/donations.php?action=summary&user_id=X → totals

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ---- SEND DONATION ----
if ($action === 'donate') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $b    = getBody();

    $toUserId  = (int)($b['to_user_id'] ?? 0);
    $amountAda = (float)($b['amount_ada'] ?? 0);
    $message   = sanitize($b['message']   ?? '');
    $fromName  = sanitize($b['from_name'] ?? $user['name']);

    if (!$toUserId)      jsonErr('to_user_id is required');
    if ($amountAda <= 0) jsonErr('amount_ada must be greater than 0');
    if ($toUserId === $user['id']) jsonErr('You cannot donate to yourself');

    // Check recipient exists
    $check = $pdo->prepare('SELECT id, name, wallet FROM users WHERE id = ?');
    $check->execute([$toUserId]);
    $recipient = $check->fetch();
    if (!$recipient) jsonErr('Recipient not found', 404);

    // Simulate Cardano transaction
    // In production: build & submit tx via Lucid + CowryWallet
    $txRef = 'tx_' . strtoupper(bin2hex(random_bytes(12)));

    $stmt = $pdo->prepare('
        INSERT INTO donations (to_user_id, from_name, amount_ada, message, tx_ref, network)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$toUserId, $fromName, $amountAda, $message, $txRef, CARDANO_NETWORK]);
    $donationId = (int)$pdo->lastInsertId();

    // Increment donation count on recipient
    $pdo->prepare('UPDATE users SET donations = donations + 1 WHERE id = ?')->execute([$toUserId]);

    jsonOk([
        'id'             => $donationId,
        'tx_ref'         => $txRef,
        'amount_ada'     => $amountAda,
        'to'             => $recipient['name'],
        'to_wallet'      => $recipient['wallet'],
        'network'        => CARDANO_NETWORK,
        'message'        => $message,
        'timestamp'      => date('c'),
        'note'           => 'Simulated on testnet. Real Cardano tx via Lucid integration required for mainnet.'
    ], 201);
}

// ---- DONATIONS RECEIVED BY USER ----
if ($method === 'GET' && isset($_GET['user_id']) && !$action) {
    $userId = (int)$_GET['user_id'];
    $page   = max(1, (int)($_GET['page']  ?? 1));
    $limit  = min(50, (int)($_GET['limit'] ?? 10));
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare('
        SELECT id, from_name, amount_ada, message, tx_ref, network, donated_at
        FROM donations WHERE to_user_id = ?
        ORDER BY donated_at DESC LIMIT ? OFFSET ?
    ');
    $stmt->execute([$userId, $limit, $offset]);
    $donations = $stmt->fetchAll();

    $total = $pdo->prepare('SELECT COUNT(*), COALESCE(SUM(amount_ada),0) FROM donations WHERE to_user_id = ?');
    $total->execute([$userId]);
    [$count, $totalAda] = $total->fetch(PDO::FETCH_NUM);

    jsonOk([
        'donations'  => $donations,
        'count'      => (int)$count,
        'total_ada'  => (float)$totalAda,
        'page'       => $page,
        'limit'      => $limit
    ]);
}

// ---- DONATIONS I SENT ----
if ($action === 'sent') {
    requireMethod('GET');
    $user = requireAuth($pdo);
    $stmt = $pdo->prepare('
        SELECT d.*, u.name AS recipient_name
        FROM donations d
        JOIN users u ON u.id = d.to_user_id
        WHERE d.from_name = ?
        ORDER BY d.donated_at DESC LIMIT 20
    ');
    $stmt->execute([$user['name']]);
    jsonOk($stmt->fetchAll());
}

// ---- SUMMARY ----
if ($action === 'summary') {
    requireMethod('GET');
    $userId = (int)($_GET['user_id'] ?? 0);
    if (!$userId) jsonErr('user_id required');

    $stmt = $pdo->prepare('
        SELECT
            COUNT(*) AS total_donations,
            COALESCE(SUM(amount_ada), 0) AS total_ada,
            COALESCE(MAX(amount_ada), 0) AS largest_donation,
            COALESCE(AVG(amount_ada), 0) AS average_donation
        FROM donations WHERE to_user_id = ?
    ');
    $stmt->execute([$userId]);
    jsonOk($stmt->fetch());
}

jsonErr('Unknown action or method', 404);
