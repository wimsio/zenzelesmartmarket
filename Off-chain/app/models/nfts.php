<?php
// app/models/Profiles.php
header('Content-Type: application/json');
class nfts {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Validation des données de profil
    public function validateNFTsData($title, $description) {
        if (empty(trim($title))) return "Title is required.";
        if (empty(trim($description))) return "Description is required.";



        return true; // Données valides
    }

    // creation du profile
    public function createNFT($title, $description) {
        $stmt = $this->db->prepare("INSERT INTO nfts (title, description) VALUES (?, ?)");
        return $stmt->execute([$title, $description]);
    }

}