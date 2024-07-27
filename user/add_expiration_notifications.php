<?php
include "../connect.php";

// Date limite pour les notifications (40 jours avant l'expiration)
$limit_date = date('Y-m-d', strtotime('+40 days'));

// Trouver les certificats proches de l'expiration
$stmt = $conn->prepare("SELECT cin, date_expiration FROM certificat WHERE date_expiration <= ?");
$stmt->bind_param("s", $limit_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cin = $row['cin'];
    $expiration_date = $row['date_expiration'];

    // Créer un message de notification
    $message = "Votre certificat (CIN: $cin) expire le $expiration_date. Veuillez renouveler votre certificat.";

    // Vérifier si une notification existe déjà pour ce CIN et message
    //$stmt_check = $conn->prepare("SELECT * FROM notifications_utilisateur WHERE cin = ? AND message = ?");
    //$stmt_check->bind_param("ss", $cin, $message);
    //$stmt_check->execute();
    //$result_check = $stmt_check->get_result();

    //if ($result_check->num_rows === 0) {
        // Ajouter la notification si elle n'existe pas déjà
        $stmt_insert = $conn->prepare("INSERT INTO notifications_utilisateur (cin, message, notification_time, read_status) VALUES (?, ?, NOW(), 0)");
        $stmt_insert->bind_param("ss", $cin, $message);
        $stmt_insert->execute();
    //}
}

// Fermer la connexion à la base de données
$conn->close();
?>
