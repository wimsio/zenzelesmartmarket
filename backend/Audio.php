<?php
// ===== ZENZELE — AUDIO UPLOAD API =====
// POST /php/audio.php    → upload audio file (auth, multipart)
// DELETE /php/audio.php  → remove audio (auth)

require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// ---- UPLOAD AUDIO ----
if ($method === 'POST') {
    $user = requireAuth($pdo);

    if (empty($_FILES['audio'])) jsonErr('No audio file uploaded. Send as multipart field "audio"');

    $file     = $_FILES['audio'];
    $maxSize  = 10 * 1024 * 1024; // 10 MB
    $allowed  = ['audio/webm', 'audio/ogg', 'audio/mp4', 'audio/mpeg', 'audio/wav', 'audio/x-m4a'];

    if ($file['error'] !== UPLOAD_ERR_OK) jsonErr('Upload error code: ' . $file['error']);
    if ($file['size'] > $maxSize)         jsonErr('File too large. Max 10 MB.');

    // Validate MIME via finfo
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowed, true)) jsonErr("Invalid file type: $mimeType. Use webm, ogg, mp4, mp3 or wav.");

    $uploadDir = __DIR__ . '/../assets/audio/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Remove old audio for this user
    $existing = $pdo->prepare('SELECT audio_url FROM users WHERE id = ?');
    $existing->execute([$user['id']]);
    $oldUrl = $existing->fetchColumn();
    if ($oldUrl) {
        $oldPath = __DIR__ . '/../' . ltrim(parse_url($oldUrl, PHP_URL_PATH), '/');
        if (file_exists($oldPath)) unlink($oldPath);
    }

    $ext      = match($mimeType) {
        'audio/webm'  => 'webm',
        'audio/ogg'   => 'ogg',
        'audio/mp4', 'audio/x-m4a' => 'm4a',
        'audio/mpeg'  => 'mp3',
        'audio/wav'   => 'wav',
        default       => 'audio'
    };
    $filename = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath))
        jsonErr('Failed to save audio file. Check server write permissions.');

    $audioUrl = APP_URL . '/assets/audio/' . $filename;
    $pdo->prepare('UPDATE users SET audio_url = ? WHERE id = ?')->execute([$audioUrl, $user['id']]);

    jsonOk(['audio_url' => $audioUrl, 'filename' => $filename, 'size' => $file['size']]);
}

// ---- DELETE AUDIO ----
if ($method === 'DELETE') {
    $user = requireAuth($pdo);

    $stmt = $pdo->prepare('SELECT audio_url FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $url = $stmt->fetchColumn();

    if ($url) {
        $path = __DIR__ . '/../' . ltrim(parse_url($url, PHP_URL_PATH), '/');
        if (file_exists($path)) unlink($path);
    }

    $pdo->prepare('UPDATE users SET audio_url = NULL WHERE id = ?')->execute([$user['id']]);
    jsonOk(['deleted' => true]);
}

jsonErr('Use POST to upload or DELETE to remove audio', 405);