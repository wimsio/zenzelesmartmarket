<?php
// api/auth/request_nonce.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

require_once '../../app/config/db.php';
require_once '../../app/models/User.php';

// Récupération des données brutes envoyées en JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['wallet_address']) || empty(trim($data['wallet_address']))) {
    echo json_encode([
        'success' => false,
        'message' => 'L\'adresse du portefeuille Cardano est requise.'
    ]);
    exit;
}

$walletAddress = trim($data['wallet_address']);

try {
    $userModel = new User($pdo);
    
    // Génère le jeton (nonce) unique et pré-enregistre le wallet si c'est sa première connexion
    $nonce = $userModel->generateNonceForWallet($walletAddress);
    
    // On stocke également le nonce en session par sécurité pour la vérification ultérieure
    $_SESSION['expected_nonce'] = $nonce;
    $_SESSION['auth_wallet_address'] = $walletAddress;

    echo json_encode([
        'success' => true,
        'nonce' => $nonce
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur lors de la génération du défi.'
    ]);
}