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

CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    entrepriseName VARCHAR(50) NULL UNIQUE,
    country VARCHAR(50) NULL UNIQUE,
    city VARCHAR(100) NULL, -- city of residence
    langue VARCHAR(25) NULL UNIQUE, -- ex: "zu", "sw", "en"
    biographie VARCHAR(255) NULL, -- biographie de l'entrepreneur
    activity ENUM('agriculture','entrepreneur', 'education', 'health', 'technology', 'other') NULL, -- secteur d'activité
    entrepriseDesc varchar(255) NULL, -- description de l'entreprise
    competence VARCHAR(64) NULL, -- compétences de l'entrepreneur
    walletAddress VARCHAR(255) NULL UNIQUE, -- Adresse Cardano principale
    adresslinkedin VARCHAR(255) NULL UNIQUE, -- Adresse profil LinkedIn
    photo VARCHAR(255) NULL UNIQUE, -- photo deans le profil
    audio VARCHAR(255) NULL UNIQUE, -- audio de présentation de l'entrepreneur
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10, 2) NOT NULL, -- Montant de la donation
    motif VARCHAR(255) NOT NULL, -- Motif de la donation
    dateLimit DATE NOT NULL, -- Date limite pour la donation
    benefit VARCHAR(255) NOT NULL, -- Bénéfice attendu pour le donateur
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS nfts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL, -- Titre du NFT
    description TEXT NOT NULL, -- Description du NFT
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);