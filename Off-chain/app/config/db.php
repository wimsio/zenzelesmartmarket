<?php
// app/config/db.php

// Définition des paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Souvent 'root' en local
define('DB_PASS', ''); // Souvent '' ou 'root' sur MAMP/XAMPP
define('DB_NAME', 'zenzelesmartmarket');
define('DB_CHARSET', 'utf8mb4');

try {
    // Configuration de la chaîne de connexion (DSN)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    // Options PDO pour la sécurité et la gestion des erreurs
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Active les alertes en cas d'erreur SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne les données sous forme de tableaux associatifs
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées pour bloquer les injections SQL
    ];

    // Initialisation de l'instance unique de connexion
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // En cas d'échec de connexion, on arrête le script et on affiche un message propre
    // (En production, il faudra masquer le message détaillé $e->getMessage() pour des raisons de sécurité)
    die("Erreur critique de connexion à la base de données : " . $e->getMessage());
}