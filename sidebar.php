<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu Latéral</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    background-color: #333;
    color: white;
    padding-top: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    /* Remove align-items: center; to align elements to the left */
    }
    .sidebar h2 {
    margin-bottom: 20px;
    text-align: center;
    width: 100%; /* Add this to make the title take full width */
    display: block; /* Make the title a block element to center it */
}
    .sidebar a {
    padding: 10px 15px;
    text-decoration: none;
    font-size: 18px;
    color: white;
    display: block;
    /* Add text-align: left; to align text to the left */
    text-align: left;
    }
    .sidebar a:hover {
        background-color: #555;
    }
        .submenu {
            display: none;
            padding-left: 20px;
        }
        .submenu a {
            font-size: 16px;
        }
        .content {
            margin-left: 260px; /* Ajustez cette valeur en fonction de la largeur de votre sidebar */
            padding: 20px;
        }
        .badge {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
        }
    .logo img {
    width: 80px; /* Ajustez la taille du logo selon vos besoins */
    margin-bottom: 20px; /* Ajoute un espacement en bas du logo */
    display: block; /* Make the image a block element to center it */
    margin: 0 auto; /* Center the image horizontally */
}
    </style>
    <script>
        function toggleSubmenu(event) {
            event.preventDefault();
            var submenu = event.target.nextElementSibling;
            submenu.classList.toggle('active');
            if (submenu.classList.contains('active')) {
                submenu.style.display = 'block';
            } else {
                submenu.style.display = 'none';
            }
        }
        function confirmLogout(event) {
            // Afficher une boîte de dialogue de confirmation
            var confirmation = confirm("Êtes-vous sûr de vouloir vous déconnecter ?");
            // Si l'utilisateur annule, empêcher l'action par défaut du lien
            if (!confirmation) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
    <?php
    //session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header("Location: ../login.php");
        exit();
    }

    include '../connect.php';
    // Assuming you have an active database connection in $conn
    $sql_count_unread = "SELECT COUNT(*) as unread_count FROM admin_notifications WHERE read_status = 0";
    $result_unread = $conn->query($sql_count_unread);
    $unread_count = $result_unread->fetch_assoc()['unread_count'];
    ?>
    <div class="sidebar">
        <div class="logo">
            <img src="../image/logo_tgr.png" alt="Logo de votre application">
        </div>
        <h2>Menu Admin</h2>
        <a href="dashboard1.php"><i class="fas fa-home"></i> Accueil</a>
        <a href="dashboard1.php?section=parametrage"><i class="fas fa-cogs"></i> Paramétrage</a>
        <a href="javascript:void(0);" onclick="toggleSubmenu(event)"><i class="fas fa-folder"></i> Préparation des dossiers</a>
        <div class="submenu">
            <a href="dashboard1.php?section=enregistrement_initial"><i class="fas fa-file-alt"></i> Dossier pour enregistrement initial</a>
            <a href="dashboard1.php?section=renouvellement"><i class="fas fa-sync-alt"></i> Dossier pour renouvellement</a>
        </div>
        <a href="dashboard1.php?section=dossier_demande"><i class="fas fa-folder-open"></i> Dossier de demande</a>
        <a href="list_pdfs.php"><i class="fas fa-file-pdf"></i> Voir les PDFs générés</a>
        
        <a href="chercher.php"><i class="fas fa-search"></i> Chercher un porteur</a>
        <a href="notifications_admin.php"><i class="fas fa-bell"></i> Notifications <?php if ($unread_count > 0) { echo "<span class='badge'>$unread_count</span>"; } ?></a>
        <a href="../logout.php" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
    <div class="content">
        <!-- Contenu de la page ici -->
    </div>
</body>
</html>
