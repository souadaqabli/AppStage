<?php
session_start();

// Vérification du rôle de l'utilisateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'utilisateur') {
    header("Location: ../login.php"); // Redirection vers la page de connexion si le rôle n'est pas correct
    exit();
}

// Vérification de la présence de CIN dans la session
if (!isset($_SESSION['cin'])) {  
    echo "Erreur :  La CIN non défini dans la session.";
    exit();
}

include '../connect.php';

// Traitement du formulaire de soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_SESSION['cin']; // Assurez-vous que le CIN est stocké dans la session

    $civilite = $_POST['civilite'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $fonction = $_POST['fonction'];
    $gsm = $_POST['gsm'];
    $fax = $_POST['fax'];
    $adresse_pro = $_POST['adresse_pro'];
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];
    $bureau = $_POST['bureau']; // Added bureau to match the form

    // Vérification si les informations de l'utilisateur existent déjà
    $sql_select = "SELECT * FROM porteurinfo WHERE cin = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("s", $cin);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        // Mettre à jour les informations existantes
        $sql_update = "UPDATE porteurinfo SET civilite=?, nom=?, prenom=?, email=?, fonction=?, gsm=?,bureau=?, fax=?, adresse_pro=?, ville=?, pays=?, bureau=? WHERE cin=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssssssss", $civilite, $nom, $prenom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays, $bureau, $cin);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Insérer de nouvelles informations
        $sql_insert = "INSERT INTO porteurinfo (cin, civilite, nom, prenom, email, fonction, gsm, fax, adresse_pro, ville, pays, bureau) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssssssssss", $cin, $civilite, $nom, $prenom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays, $bureau);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    // Enregistrer la notification pour l'administrateur
    $change_details = json_encode([
        'cin' => $cin,
        'civilite' => $civilite,
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'fonction' => $fonction,
        'gsm' => $gsm,
        'bureau' => $bureau,
        'fax' => $fax,
        'adresse_pro' => $adresse_pro,
        'ville' => $ville,
        'pays' => $pays,
    ]);

    $admin_notification_sql = "INSERT INTO admin_notifications (cin, change_details, notification_time) VALUES (?, ?, NOW())";
    $stmt_notification = $conn->prepare($admin_notification_sql);
    $stmt_notification->bind_param("ss", $cin, $change_details);
    $stmt_notification->execute();
    $stmt_notification->close();

    $stmt_select->close();
    $conn->close();
}

// Récupérer les informations de l'utilisateur
$sql_user_info = "SELECT * FROM porteurinfo WHERE cin = ?";
$stmt_user_info = $conn->prepare($sql_user_info);
$stmt_user_info->bind_param("s", $_SESSION['cin']);
$stmt_user_info->execute();
$result_user_info = $stmt_user_info->get_result();
$user_info = $result_user_info->fetch_assoc();
$stmt_user_info->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
        }
        .header a {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #555;
        }
        .header a:hover {
            background-color: #777;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form, .user-info {
            display: none;
        }
        form.active, .user-info.active {
            display: block;
        }
        label {
            margin-top: 10px;
            color: #555;
        }
        input[type="text"], input[type="email"], select {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            border: none;
            background-color: #5cb85c;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .user-info p {
            margin: 5px 0;
            color: #333;
        }
    </style>
    <script>
        function showSection(section) {
            document.querySelector('.user-info').classList.remove('active');
            document.querySelector('form').classList.remove('active');
            if (section === 'info') {
                document.querySelector('.user-info').classList.add('active');
            } else if (section === 'edit') {
                document.querySelector('form').classList.add('active');
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <a href="javascript:void(0)" onclick="showSection('info')">Mes Infos</a>
        <a href="javascript:void(0)" onclick="showSection('edit')">Modifier Mes Infos</a>
    </div>
    <div class="container">
        <h1>Bienvenue, <?php echo htmlspecialchars($user_info['prenom'] . ' ' . $user_info['nom']); ?></h1>
        
        <div class="user-info active">
            <h2>Vos Informations:</h2>
            <p><strong>Civilité:</strong> <?php echo htmlspecialchars($user_info['civilite']); ?></p>
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user_info['nom']); ?></p>
            <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user_info['prenom']); ?></p>
            <p><strong>CIN:</strong> <?php echo htmlspecialchars($user_info['cin']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
            <p><strong>Fonction:</strong> <?php echo htmlspecialchars($user_info['fonction']); ?></p>
            <p><strong>Bureau:</strong> <?php echo htmlspecialchars($user_info['bureau']); ?></p>
            <p><strong>GSM:</strong> <?php echo htmlspecialchars($user_info['gsm']); ?></p>
            <p><strong>Fax:</strong> <?php echo htmlspecialchars($user_info['fax']); ?></p>
            <p><strong>Adresse Professionnelle:</strong> <?php echo htmlspecialchars($user_info['adresse_pro']); ?></p>
            <p><strong>Ville:</strong> <?php echo htmlspecialchars($user_info['ville']); ?></p>
            <p><strong>Pays:</strong> <?php echo htmlspecialchars($user_info['pays']); ?></p>
        </div>

        <form method="POST" action="" >
            <label for="civilite">Civilité:</label>
            <input type="text" id="civilite" name="civilite" value="<?php echo htmlspecialchars($user_info['civilite']); ?>" required>

            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user_info['nom']); ?>" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user_info['prenom']); ?>" required>

            <label for="email">Adresse Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>

            <label for="fonction">Fonction:</label>
            <input type="text" id="fonction" name="fonction" value="<?php echo htmlspecialchars($user_info['fonction']); ?>" required>

            <label for="bureau">Bureau:</label>
            <input type="text" id="bureau" name="bureau" value="<?php echo htmlspecialchars($user_info['bureau']); ?>" required>

            <label for="gsm">GSM:</label>
            <input type="text" id="gsm" name="gsm" value="<?php echo htmlspecialchars($user_info['gsm']); ?>" required>


            <label for="bureau">Bureau:</label>
            <input type="text" id="bureau" name="bureau" value="<?php echo htmlspecialchars($user_info['bureau']); ?>" required>


            <label for="fax">Fax:</label>
            <input type="text" id="fax" name="fax" value="<?php echo htmlspecialchars($user_info['fax']); ?>" required>

            <label for="adresse_pro">Adresse professionnelle:</label>
            <input type="text" id="adresse_pro" name="adresse_pro" value="<?php echo htmlspecialchars($user_info['adresse_pro']); ?>" required>

            <label for="ville">Ville:</label>
            <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($user_info['ville']); ?>" required>

            <label for="pays">Pays:</label>
            <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($user_info['pays']); ?>" required>

            <button type="submit">Sauvegarder</button>
        </form>
    </div>
</body>
</html>
