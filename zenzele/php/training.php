<?php
// ===== ZENZELE — TRAINING API =====
// POST /php/training.php?action=request    → submit training request (auth)
// GET  /php/training.php?action=mine       → my requests (auth)
// GET  /php/training.php?action=mentors    → list available mentors
// POST /php/training.php?action=mentor_on  → register as mentor (auth)
// POST /php/training.php?action=mentor_off → remove mentor status (auth)
// POST /php/training.php?action=accept     → mentor accepts request (auth)
// GET  /php/training.php                   → all pending requests (public)

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ---- SUBMIT TRAINING REQUEST ----
if ($action === 'request') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $b    = getBody();

    $area    = sanitize($b['area']    ?? '');
    $details = sanitize($b['details'] ?? '');
    if (!$area) jsonErr('area is required');

    // Update user's training_wanted field too
    $pdo->prepare('UPDATE users SET training_wanted = ? WHERE id = ?')->execute([$area, $user['id']]);

    // Check for existing pending request in same area
    $dup = $pdo->prepare('SELECT id FROM training_requests WHERE user_id = ? AND area = ? AND status = "pending"');
    $dup->execute([$user['id'], $area]);
    if ($dup->fetch()) jsonErr('You already have a pending request for this area', 409);

    $stmt = $pdo->prepare('INSERT INTO training_requests (user_id, area, details) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'], $area, $details]);
    $id = (int)$pdo->lastInsertId();

    jsonOk(['id' => $id, 'area' => $area, 'status' => 'pending', 'message' => 'Training request submitted!'], 201);
}

// ---- MY REQUESTS ----
if ($action === 'mine') {
    requireMethod('GET');
    $user = requireAuth($pdo);
    $stmt = $pdo->prepare('SELECT * FROM training_requests WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user['id']]);
    jsonOk($stmt->fetchAll());
}

// ---- LIST ALL PENDING REQUESTS (public — for mentors to browse) ----
if ($method === 'GET' && !$action) {
    $area   = sanitize($_GET['area'] ?? '');
    $params = ['pending'];
    $where  = 'tr.status = ?';
    if ($area) { $where .= ' AND tr.area = ?'; $params[] = $area; }

    $stmt = $pdo->prepare("
        SELECT tr.id, tr.area, tr.details, tr.status, tr.created_at,
               u.id AS user_id, u.name AS user_name, u.city, u.avatar, u.category
        FROM training_requests tr
        JOIN users u ON u.id = tr.user_id
        WHERE $where
        ORDER BY tr.created_at DESC
        LIMIT 50
    ");
    $stmt->execute($params);
    jsonOk($stmt->fetchAll());
}

// ---- LIST MENTORS ----
if ($action === 'mentors') {
    requireMethod('GET');
    $area = sanitize($_GET['area'] ?? '');

    $stmt = $pdo->prepare('
        SELECT id, name, city, country, category, bio, avatar, training_wanted, skills
        FROM users WHERE has_mentor = 1 ' . ($area ? 'AND category LIKE ?' : '') . '
        ORDER BY followers DESC LIMIT 30
    ');
    $params = $area ? ["%$area%"] : [];
    $stmt->execute($params);
    $mentors = $stmt->fetchAll();
    foreach ($mentors as &$m) $m['skills'] = json_decode($m['skills'] ?? '[]', true);
    jsonOk($mentors);
}

// ---- REGISTER AS MENTOR ----
if ($action === 'mentor_on') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $pdo->prepare('UPDATE users SET has_mentor = 1 WHERE id = ?')->execute([$user['id']]);
    jsonOk(['mentor' => true, 'message' => 'You are now listed as a mentor!']);
}

// ---- REMOVE MENTOR STATUS ----
if ($action === 'mentor_off') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $pdo->prepare('UPDATE users SET has_mentor = 0 WHERE id = ?')->execute([$user['id']]);
    jsonOk(['mentor' => false]);
}

// ---- MENTOR ACCEPTS REQUEST ----
if ($action === 'accept') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $b    = getBody();
    $reqId = (int)($b['request_id'] ?? 0);
    if (!$reqId) jsonErr('request_id required');

    $stmt = $pdo->prepare('SELECT * FROM training_requests WHERE id = ?');
    $stmt->execute([$reqId]);
    $req = $stmt->fetch();
    if (!$req) jsonErr('Request not found', 404);
    if ($req['status'] !== 'pending') jsonErr('Request is no longer pending');

    $pdo->prepare('UPDATE training_requests SET status = "matched" WHERE id = ?')->execute([$reqId]);
    jsonOk(['matched' => true, 'request_id' => $reqId, 'message' => 'You have accepted this training request!']);
}

jsonErr('Unknown action or method', 404);
