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
    $username       = isset($_POST['username']) ? trim(htmlspecialchars($_POST['username'])) : null;
    $entrepriseName = isset($_POST['entrepriseName']) ? trim(htmlspecialchars($_POST['entrepriseName'])) : null;
    $country        = isset($_POST['country']) ? trim(htmlspecialchars($_POST['country'])) : null;
    $city           = isset($_POST['city']) ? trim(htmlspecialchars($_POST['city'])) : null;
    $langue         = isset($_POST['langue']) ? trim(htmlspecialchars($_POST['langue'])) : null;
    $biographie     = isset($_POST['biographie']) ? trim(htmlspecialchars($_POST['biographie'])) : null;
    $activity       = isset($_POST['activity']) ? trim(htmlspecialchars($_POST['activity'])) : null;
    $entrepriseDesc = isset($_POST['entrepriseDesc']) ? trim(htmlspecialchars($_POST['entrepriseDesc'])) : null;
    $competence     = isset($_POST['competence']) ? trim(htmlspecialchars($_POST['competence'])) : null;
    $walletAddress  = isset($_POST['walletAddress']) ? trim(htmlspecialchars($_POST['walletAddress'])) : null;
    $adresslinkedin = isset($_POST['adresslinkedin']) ? trim(htmlspecialchars($_POST['adresslinkedin'])) : null;

    // 5. Validation stricte des champs obligatoires
    if (!$username || !$entrepriseName || !$country || !$city || !$walletAddress) {
        throw new Exception("Champs obligatoires manquants.");
    }

    // 6. Configuration des dossiers de stockage pour les fichiers
    $uploadDir = __DIR__ . '/../../uploads/';
    $photoDir  = $uploadDir . 'photos/';
    $audioDir  = $uploadDir . 'audios/';

    // Création automatique des dossiers s'ils n'existent pas
    if (!is_dir($photoDir)) mkdir($photoDir, 0755, true);
    if (!is_dir($audioDir)) mkdir($audioDir, 0755, true);

    $photoPathDb = null;
    $audioPathDb = null;

    // 7. Traitement du fichier Image (Photo)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoFile = $_FILES['photo'];
        $photoExt  = strtolower(pathinfo($photoFile['name'], PATHINFO_EXTENSION));
        
        // Validation de l'extension de l'image
        $allowedPhotoExts = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($photoExt, $allowedPhotoExts)) {
            throw new Exception("Format d'image non valide (JPG, PNG, WEBP uniquement).");
        }

        // Génération d'un nom unique pour éviter les écrasements
        $photoName   = uniqid('photo_', true) . '.' . $photoExt;
        $photoTarget = $photoDir . $photoName;

        if (move_uploaded_file($photoFile['tmp_name'], $photoTarget)) {
            $photoPathDb = 'uploads/photos/' . $photoName; // Chemin à enregistrer en BDD
        } else {
            throw new Exception("Impossible d'enregistrer l'image sur le serveur.");
        }
    }

    // 8. Traitement du fichier Audio (Enregistrement vocal)
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $audioFile = $_FILES['audio'];
        $audioExt  = strtolower(pathinfo($audioFile['name'], PATHINFO_EXTENSION));
        
        // Validation de l'extension audio générée par le MediaRecorder
        $allowedAudioExts = ['webm', 'ogg', 'mp3', 'wav'];
        if (!in_array($audioExt, $allowedAudioExts)) {
            throw new Exception("Format audio non supporté.");
        }

        $audioName   = uniqid('audio_', true) . '.' . $audioExt;
        $audioTarget = $audioDir . $audioName;

        if (move_uploaded_file($audioFile['tmp_name'], $audioTarget)) {
            $audioPathDb = 'uploads/audios/' . $audioName; // Chemin à enregistrer en BDD
        } else {
            throw new Exception("Impossible d'enregistrer le fichier audio sur le serveur.");
        }
    }

    // 9. Insertion dans la Base de Données (Exemple avec PDO)
    require_once '../../app/config/db.php'; // Votre instance PDO de connexion
    $stmt = $pdo->prepare("INSERT INTO profiles (username, entrepriseName, country, city, langue, biographie, activity, entrepriseDesc, competence, walletAddress, adresslinkedin, photo, audio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $username, $entrepriseName, $country, $city, $langue, 
        $biographie, $activity, $entrepriseDesc, $competence, 
        $walletAddress, $adresslinkedin, $photoPathDb, $audioPathDb
    ]);

    // 10. Succès
    $response['success'] = true;
    $response['message'] = 'Profil créé avec succès !';

} catch (Exception $e) {
    // Gestion des erreurs
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 11. Renvoi de la réponse au format JSON
echo json_encode($response);
exit;
