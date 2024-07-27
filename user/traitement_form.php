<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['cin'])) {
    $cin = $_SESSION['cin'];
    $q01 = $_POST['q01'];
    $q02 = $_POST['q02'];
    $q03 = $_POST['q03'];
    $q04 = $_POST['q04'];
    $q05 = $_POST['q05'];
    $q06 = $_POST['q06'];
    $q07 = $_POST['q07'];
    $q08 = $_POST['q08'];
    $q09 = $_POST['q09'];
    $q10 = $_POST['q10'];

    // Connexion à la base de données (à adapter selon votre configuration)
    include '../connect.php';

    // Préparation de la requête SQL d'insertion
    $sql = "INSERT INTO reponses (cin, q01, q02, q03, q04, q05, q06, q07, q08, q09, q10) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $cin, $q01, $q02, $q03, $q04, $q05, $q06, $q07, $q08, $q09, $q10);

    // Exécution de la requête
    if ($stmt->execute()) {
        // Réussite : rediriger vers une page de confirmation ou autre action
        header("Location: reponses.php");
        exit();
    } else {
        // Erreur : gérer l'échec de l'insertion
        echo "Erreur lors de l'enregistrement des réponses.";
    }

    // Fermeture de la connexion
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Questions</title>
    <style>
        /* Votre style CSS ici */
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($message)) : ?>
            <div class="<?php echo ($message === "Réponses enregistrées avec succès.") ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
