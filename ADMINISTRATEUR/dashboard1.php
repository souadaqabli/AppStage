<?php
session_start();

// Vérifier si l'utilisateur est authentifié et est un administrateur
if (!isset($_SESSION['cin']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .content {
            margin-left: 260px; /* Ajustez cette valeur en fonction de la largeur de votre sidebar */
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            margin: auto;
        }
        input[type="file"] {
            margin-top: 20px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        // Fonction pour afficher la section suivante et gérer la navigation entre les étapes
        function showSection(section) {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'none';
            document.getElementById(section).style.display = 'block';
        }
    </script>
</head>
<body>
    <!-- Inclure la barre de navigation latérale -->
    <?php include '../sidebar.php'; ?>

    <!-- Contenu principal -->
    <div class="content">
        <?php
        // Gestion des messages d'erreur et de succès
        if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php
        // Gestion des sections
        if (isset($_GET['section'])) {
            $section = $_GET['section'];
            switch ($section) {
                case 'parametrage':
                    include 'parametrage.php';
                    break;
                case 'admin_notifications':
                    include 'notifications_admin.php';
                    break;
                //case 'preparation_dossier':
                   // include 'preparation_dossier.php';
                    //break;
                case 'enregistrement_initial':
                    include 'enregistrement_initial.php';
                    break;
                //case 'dossier_2':
                    //include 'renouvellemnt.php';
                    //break;
                case 'renouvellement':
                    include 'preparation_dossier_renouv.php';
                    break;
                case 'dossier_demande':
                    include 'dossier_demande.php';
                    break;
                default:
                    echo "Section non trouvée.";
                    break;
            }
        } else {
            echo "Bienvenue dans le Dashboard Admin.";
        }
        ?>
    </div>
</body>
</html>