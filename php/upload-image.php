<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit; }
if (empty($_FILES['image'])) { http_response_code(400); echo json_encode(['error'=>'No image file']); exit; }
$file = $_FILES['image'];
$maxSize = 5*1024*1024;
$allowed = ['image/jpeg','image/png','image/webp','image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if ($file['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['error'=>'Upload error']); exit; }
if ($file['size'] > $maxSize) { http_response_code(400); echo json_encode(['error'=>'Max 5MB']); exit; }
if (!in_array($mime, $allowed)) { http_response_code(400); echo json_encode(['error'=>'Invalid type']); exit; }
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
$filename = uniqid('img_', true) . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) { http_response_code(500); echo json_encode(['error'=>'Save failed']); exit; }
$base = (isset($_SERVER['HTTPS'])?'https':'http') . '://' . $_SERVER['HTTP_HOST'];
echo json_encode(['success'=>true,'url'=>$base.'/zenzele-smart-market/uploads/'.$filename,'filename'=>$filename]);
