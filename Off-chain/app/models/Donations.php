<?php
// app/models/Profiles.php
header('Content-Type: application/json');
class Donations {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Validation des données de profil
    public function validateDonationsData($amount, $motif, $dateLimit, $benefit) {
        if (empty(trim($amount))) return "Amount is required.";
        if (empty(trim($motif))) return "Motif is required.";
        if (empty(trim($dateLimit))) return "Date limit is required.";
        if (empty(trim($benefit))) return "Beneficiary is required.";


        return true; // Données valides
    }

    // creation du profile
    public function donations($amount, $motif, $dateLimit, $benefit) {
        $stmt = $this->db->prepare("INSERT INTO donations (amount, motif, dateLimit, benefit) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$amount, $motif, $dateLimit, $benefit]);
    }

}