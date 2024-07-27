<?php
session_start();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_changes'])) {
    $cin = $_POST['cin'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $civilite = $_POST['civilite'];
    $email = $_POST['email'];
    $fonction = $_POST['fonction'];
    $gsm = $_POST['gsm'];
    $bureau = $_POST['bureau'];
    $fax = $_POST['fax'];
    $adresse_pro = $_POST['adresse_pro'];
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];

    try {
        require '../vendor/autoload.php';
        include '../connect.php';

        // Query pour mettre à jour les données basées sur le CIN
        $sql = "UPDATE porteurinfo SET nom=?, prenom=?, civilite=?, email=?, fonction=?, gsm=?, bureau=?, fax=?, adresse_pro=?, ville=?, pays=? WHERE cin=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $nom, $prenom, $civilite, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays, $cin);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Les informations ont été mises à jour avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour des informations.";
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Erreur lors de la mise à jour : " . $e->getMessage();
    }

    header("Location: search_result.php?cin=" . $cin);
    exit();
}
?>
