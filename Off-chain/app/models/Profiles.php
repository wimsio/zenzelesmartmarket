<?php
// app/models/Profiles.php
header('Content-Type: application/json');
class Profiles {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Validation des données de profil
    public function validateProfilesData($username, $entrepriseName, $country, $city, $langue,$biographie,$activity,$entrepriseDesc,$competence,$walletAddress,$adresslinkedin,$photo,$audio) {
        if (empty(trim($username))) return "Le nom ne peut pas être vide.";
        if (empty(trim($entrepriseName))) return "Le nom de l'entreprise ne peut pas être vide.";
        if (empty(trim($country))) return "Le pays est requis.";
        if (empty(trim($city))) return "La ville est requise.";
        if (empty(trim($langue))) return "La langue est requise.";
        if (empty(trim($biographie))) return "La biographie est requise.";
        if (empty(trim($activity))) return "L'activité est requise.";
        if (empty(trim($entrepriseDesc))) return "La description de l'entreprise est requise.";
        if (empty(trim($competence))) return "Les compétences sont requises.";
        if (empty(trim($walletAddress))) return "L'adresse du portefeuille est requise.";
        if (empty(trim($adresslinkedin))) return "L'adresse LinkedIn est requise.";
        if (empty(trim($photo))) return "La photo est requise.";
        if (empty(trim($audio))) return "L'audio est requis.";

        return true; // Données valides
    }

    // creation du profile
    public function profiles($username, $entrepriseName, $country, $city, $langue,$biographie,$activity,$entrepriseDesc,$competence,$walletAddress,$adresslinkedin,$photo,$audio) {
         $stmt = $this->db->prepare("INSERT INTO profiles (username, entreprise_name, country, city, langue, biographie, activity, entreprise_desc, competence, wallet_address, adresslinkedin, photo, audio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $entrepriseName, $country, $city, $langue, $biographie, $activity, $entrepriseDesc, $competence, $walletAddress, $adresslinkedin, $photo, $audio]);
    }

}