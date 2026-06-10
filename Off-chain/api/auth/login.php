<?php
// api/auth/login.php
header('Content-Type: application/json');
session_start();

require_once '../../app/config/db.php'; // Votre instance PDO de connexion
require_once '../../app/models/User.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Identifiants manquants.']);
    exit;
}

$userModel = new User($pdo);
$user = $userModel->verifyCredentials($data['email'], $data['password']);


if ($user) {
    // Établissement de la session serveur
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['account_type'] = $user['account_type'];

    echo json_encode([
        'success' => true, 
        'user' => [
            'username' => $user['username'],
            'account_type' => $user['account_type'],
            'lang' => $user['preferred_language']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'E-mail ou mot de passe incorrect.']);
}
