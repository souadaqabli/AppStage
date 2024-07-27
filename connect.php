<?php

// Inclure le fichier de configuration
require_once 'config.php';

// Créer une connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Définir l'encodage des caractères en UTF-8
$conn->set_charset("utf8mb4");

// Vérifier si l'encodage des caractères a été défini correctement
if (!$conn->set_charset("utf8mb4")) {
    die("Erreur lors de la définition de l'encodage des caractères UTF-8 : " . $conn->error);
}

// Votre code pour exécuter des requêtes et récupérer des données
?>
