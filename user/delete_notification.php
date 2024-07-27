<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header("Location: ../login.php");
    exit();
}
include "../connect.php";

// Vérifier que l'ID de la notification est bien passé en POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $cin = $_SESSION['cin'];

    // Supprimer la notification de la base de données
    $stmt_delete = $conn->prepare("DELETE FROM notifications_utilisateur WHERE id = ? AND cin = ?");
    $stmt_delete->bind_param("is", $id, $cin);

    if ($stmt_delete->execute()) {
        echo "Notification supprimée avec succès";
    } else {
        echo "Erreur lors de la suppression de la notification";
    }

    $stmt_delete->close();
}
$conn->close();
?>
