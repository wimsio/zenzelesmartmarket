-- database/schema.sql

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NULL UNIQUE,
    phone VARCHAR(20) NULL UNIQUE,
    password_hash VARCHAR(255) NULL, -- Nullable si connexion 100% Wallet
    wallet_address VARCHAR(255) NULL UNIQUE, -- Adresse Cardano principale
    country VARCHAR(3) NOT NULL, -- Code ISO ex: "ZAF", "KEN", "BRA"
    preferred_language VARCHAR(5) DEFAULT 'en', -- ex: "zu", "sw", "en"
    account_type ENUM('entrepreneur', 'donor', 'fund_seeker', 'trainer') NOT NULL,
    nonce VARCHAR(64) NULL, -- Pour le défi d'authentification Web3
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);