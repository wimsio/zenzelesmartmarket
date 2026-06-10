<?php
require_once __DIR__ . '/config.php';
// $pdo is created in config.php — do NOT call getDB()
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && empty($action)) {
    $user = requireAuth($pdo);
    if (empty($_FILES['audio'])) { jsonErr('No audio file uploaded.', 400); }
    $file = $_FILES['audio'];
    if ($file['error'] !== UPLOAD_ERR_OK) { jsonErr('Upload error: ' . $file['error'], 400); }
    if ($file['size'] > 32 * 1024 * 1024) { jsonErr('File too large. Max 32MB.', 400); }
    $uploadDir = __DIR__ . '/../uploads/audio/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $ext = 'webm';
    if (strpos($mime, 'ogg')  !== false) $ext = 'ogg';
    if (strpos($mime, 'mpeg') !== false) $ext = 'mp3';
    if (strpos($mime, 'wav')  !== false) $ext = 'wav';
    if (strpos($mime, 'mp4')  !== false) $ext = 'mp4';
    $filename = 'audio_' . $user['id'] . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        jsonErr('Failed to save audio file.', 500);
    }
    $base      = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $audio_url = $base . '/zenzele-smart-market/uploads/audio/' . $filename;
    $stmt = $pdo->prepare('UPDATE users SET audio_url = ? WHERE id = ?');
    $stmt->execute([$audio_url, (int)$user['id']]);
    jsonOk(['audio_url' => $audio_url, 'message' => 'Audio pitch saved successfully']);
}

if ($method === 'DELETE') {
    $user = requireAuth($pdo);
    $stmt = $pdo->prepare('UPDATE users SET audio_url = NULL WHERE id = ?');
    $stmt->execute([(int)$user['id']]);
    jsonOk(['message' => 'Audio deleted']);
}

jsonErr('Method not allowed', 405);