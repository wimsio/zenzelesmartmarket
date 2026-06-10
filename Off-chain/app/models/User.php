<?php
// app/models/User.php

class User {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Validation des données d'inscription standard
    public function validateRegistrationData($username, $email, $password, $country, $accountType) {
        if (empty(trim($username))) return "Le nom d'utilisateur ne peut pas être vide.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "L'adresse e-mail n'est pas valide.";
        if (strlen($password) < 8) return "Le mot de passe doit contenir au moins 8 caractères.";
        if (empty($country)) return "Le pays est requis.";
        
        $allowedTypes = ['entrepreneur', 'donor', 'fund_seeker', 'trainer'];
        if (!in_array($accountType, $allowedTypes)) return "Type de compte invalide.";

        return true; // Données valides
    }

    // Inscription d'un utilisateur classique
    public function registerStandard($username, $email, $password, $country, $lang, $accountType) {
        // Hachage du mot de passe sécurisé (Bcrypt)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, country, preferred_language, account_type) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $passwordHash, $country, $lang, $accountType]);
    }

    // Générer un défi unique (nonce) pour la connexion par portefeuille
    public function generateNonceForWallet($walletAddress) {
        $nonce = bin2hex(random_bytes(32)); // Génère un token aléatoire sécurisé
        
        // Vérifie si le portefeuille existe déjà, sinon crée un compte temporaire ou met à jour le nonce
        $stmt = $this->db->prepare("SELECT id FROM users WHERE wallet_address = ?");
        $stmt->execute([$walletAddress]);
        $user = $stmt->fetch();

        if ($user) {
            $updateStmt = $this->db->prepare("UPDATE users SET nonce = ? WHERE wallet_address = ?");
            $updateStmt->execute([$nonce, $walletAddress]);
        } else {
            // Premier contact avec ce wallet : on pré-enregistre un profil anonyme à compléter
            $dummyUsername = "user_" . substr($walletAddress, -8);
            $insertStmt = $this->db->prepare("INSERT INTO users (username, wallet_address, nonce, country, account_type) VALUES (?, ?, ?, 'ZZ', 'donor')");
            $insertStmt->execute([$dummyUsername, $walletAddress, $nonce]);
        }

        return $nonce;
    }

    // Vérifier les identifiants classiques
    public function verifyCredentials($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user; // Authentification réussie
        }
        return false;
    }
}