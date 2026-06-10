<?php
// api/auth/register.php
header('Content-Type: application/json');

require_once '../../app/config/db.php';
require_once '../../app/models/User.php';

// Récupérer le corps de la requête JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Données d\'inscription non reçues ou mal formées.']);
    exit;
}

$username    = isset($data['username']) ? trim($data['username']) : '';
$email       = isset($data['email']) ? trim($data['email']) : '';
$password    = isset($data['password']) ? $data['password'] : '';
$country     = isset($data['country']) ? trim($data['country']) : '';
$lang        = isset($data['lang']) ? trim($data['lang']) : 'en';
$accountType = isset($data['account_type']) ? trim($data['account_type']) : 'entrepreneur';

$userModel = new User($pdo);

// 1. Validation stricte côté serveur via le modèle User
$validationCheck = $userModel->validateRegistrationData($username, $email, $password, $country, $accountType);

if ($validationCheck !== true) {
    echo json_encode(['success' => false, 'message' => $validationCheck]);
    exit;
}

try {
    // 2. Vérification de l'unicité de l'e-mail ou du nom d'utilisateur
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmtCheck->execute([$username, $email]);
    if ($stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Le nom d\'utilisateur ou l\'adresse e-mail est déjà utilisé.']);
        exit;
    }

    // 3. Enregistrement en base de données avec hachage (exécuté à l'intérieur de registerStandard)
    $success = $userModel->registerStandard($username, $email, $password, $country, $lang, $accountType);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Utilisateur créé avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur technique lors de l\'écriture en base de données.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}