<?php
// 1. Démarre la session de manière sécurisée si elle n'est pas déjà lancée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    $username = $_SESSION['username'] = 'Guest'; // Default if the user is not logged in
}