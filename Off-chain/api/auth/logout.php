<?php
// api/auth/logout.php
header('Content-Type: application/json');
session_start();

// Si la variable de session de l'utilisateur n'existe pas, on le redirige immédiatement
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

// Destruction de la session sur le serveur
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

echo json_encode(['success' => true, 'message' => 'Déconnexion réussie.']);