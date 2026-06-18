<?php
// 1. Configuration des en-têtes pour répondre en JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // À restreindre en production pour la sécurité
header('Access-Control-Allow-Methods: POST');

// 2. Initialisation de la réponse par défaut
$response = [
    'success' => false,
    'message' => 'Une erreur inconnue est survenue.'
];

// 3. Vérification de la méthode de requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée. Seul le POST est accepté.';
    echo json_encode($response);
    exit;
}

try {
    // 4. Récupération et nettoyage des données textuelles ($_POST)
    $amount       = isset($_POST['amount']) ? trim(htmlspecialchars($_POST['amount'])) : null;
    $motif = isset($_POST['motif']) ? trim(htmlspecialchars($_POST['motif'])) : null;
    $dateLimit        = isset($_POST['dateLimit']) ? trim(htmlspecialchars($_POST['dateLimit'])) : null;
    $benefit           = isset($_POST['benefit']) ? trim(htmlspecialchars($_POST['benefit'])) : null;
   

    // 5. Validation stricte des champs obligatoires
    if (!$amount || !$motif || !$dateLimit || !$benefit) {
        throw new Exception("Champs obligatoires manquants.");
    }

    // 9. Insertion dans la Base de Données (Exemple avec PDO)
    require_once '../../app/config/db.php'; // Votre instance PDO de connexion
    $stmt = $pdo->prepare("INSERT INTO donations (amount, motif, dateLimit, benefit) VALUES (?, ?, ?, ?)");
    
    $stmt->execute([
        $amount, $motif, $dateLimit, $benefit
    ]);

    // 10. Succès
    $response['success'] = true;
    $response['message'] = 'Donation successfully registered !';

} catch (Exception $e) {
    // Gestion des erreurs
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 11. Renvoi de la réponse au format JSON
echo json_encode($response);
exit;
