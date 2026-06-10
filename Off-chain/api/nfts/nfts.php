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
    $title       = isset($_POST['title']) ? trim(htmlspecialchars($_POST['title'])) : null;
    $description = isset($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : null;
   


    // 5. Validation stricte des champs obligatoires
    if (!$title || !$description) {
        throw new Exception("Champs obligatoires manquants.");
    }

    // 9. Insertion dans la Base de Données (Exemple avec PDO)
    require_once '../../app/config/db.php'; // Votre instance PDO de connexion
    $stmt = $pdo->prepare("INSERT INTO nfts (title, description) VALUES (?, ?)");
    
    $stmt->execute([
        $title, $description
    ]);

    // 10. Succès
    $response['success'] = true;
    $response['message'] = 'NFT successfully registered !';

} catch (Exception $e) {
    // Gestion des erreurs
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 11. Renvoi de la réponse au format JSON
echo json_encode($response);
exit;
