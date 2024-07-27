<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header("Location: ../login.php");
    exit();
}

include '../connect.php';

$success_message = '';
//$notif = []; // Initialisez la variable $notif avec une valeur vide
//$change_details='';

// Traiter le formulaire de soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cin = $_SESSION['cin']; // Assurez-vous que CIN est stocké dans la session
    $civilite = $_POST['civilite'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    //$cin = $_POST['cin'];
    $email = $_POST['email'];
    $fonction = $_POST['fonction'];
    $gsm = $_POST['gsm'];
    $bureau = $_POST['bureau'];
    $fax = $_POST['fax'];
    $adresse_pro = $_POST['adresse_pro'];
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];


     // Nouveau - pour l'agence de retrait
    $agence_retrait = $_POST['agence_retrait'];
    $adresse_poste = $_POST['adresse_poste'];
    $ville_retrait = $_POST['ville_retrait'];


    // Vérifier si les informations de l'utilisateur existent déjà
$sql = "SELECT * FROM porteurinfo WHERE cin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Mettre à jour les informations existantes
    $sql = "UPDATE porteurinfo SET civilite=?, nom=?, prenom=?, email=?, fonction=?, gsm=?, bureau=?, fax=?, adresse_pro=?, ville=?, pays=? WHERE cin=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $civilite, $nom, $prenom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays, $cin);
    if ($stmt->execute()) {
        $success_message = "Informations mises à jour avec succès.";
        // Votre code pour gérer les notifications et autres actions après la mise à jour
    } else {
        $success_message = "Erreur lors de la mise à jour des informations.";
    }
} else {
    $success_message = "Aucun enregistrement trouvé pour le CIN fourni.";
}

        $change_details = [
            "Civilité" => $civilite,
            "Nom" => $nom,
            "Prénom" => $prenom,
            "CIN" => $cin,
            "Email" => $email,
            "Fonction" => $fonction,
            "GSM" => $gsm,
            "Bureau" => $bureau,
            "Fax" => $fax,
            "Adresse professionnelle" => $adresse_pro,
            "Ville" => $ville,
            "Pays" => $pays,
            "Agence de retrait" => $agence_retrait,
            "Adresse postale de retrait" => $adresse_poste,
            "Ville de retrait" => $ville_retrait
        ];
        $change_details_json = json_encode($change_details); // Convertir en JSON
        $notification_time = date('Y-m-d H:i:s'); // Date et heure actuelles
        
        $sql_notification = "INSERT INTO admin_notifications (cin, change_details, notification_time) VALUES (?, ?, ?)";
        $stmt_notification = $conn->prepare($sql_notification);
        $stmt_notification->bind_param("sss", $cin, $change_details_json, $notification_time);
        $stmt_notification->execute();


        // Nouveau - insérer/mettre à jour les informations de retrait
        $sql_nv = "SELECT * FROM porteurnv WHERE cin = ?";
        $stmt_nv = $conn->prepare($sql_nv);
        $stmt_nv->bind_param("s", $cin);
        $stmt_nv->execute();
        $result_nv = $stmt_nv->get_result();
        

        if ($result_nv->num_rows > 0) {
            $sql_update_nv = "UPDATE porteurnv SET agence_retrait=?, adresse_poste=?, ville_retrait=?, civilite=?, nom=?, prenom=?, email=?, gsm=?, adresse_pro=? WHERE cin=?";
            $stmt_update_nv = $conn->prepare($sql_update_nv);
            $stmt_update_nv->bind_param("ssssssssss", $agence_retrait, $adresse_poste, $ville_retrait, $civilite, $nom, $prenom, $email, $gsm, $adresse_pro, $cin);
            $stmt_update_nv->execute();
        } else {
            $sql_insert_nv = "INSERT INTO porteurnv (cin, civilite, nom, prenom, email, gsm, adresse_pro, agence_retrait, adresse_poste, ville_retrait) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert_nv = $conn->prepare($sql_insert_nv);
            $stmt_insert_nv->bind_param("ssssssssss", $cin, $civilite, $nom, $prenom, $email, $gsm, $adresse_pro, $agence_retrait, $adresse_poste, $ville_retrait);
            $stmt_insert_nv->execute();
        }
        
    } else {
        $success_message = "Erreur lors de la mise à jour des informations.";
    }
    


// Récupérer les informations de l'utilisateur
$cin = $_SESSION['cin']; // Assurez-vous que user_id est stocké dans la session
$sql = "SELECT * FROM porteurinfo WHERE cin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();



$sql = "SELECT agence_retrait, ville_retrait, adresse_poste FROM porteurnv WHERE cin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();
$user_info_nv = $result->fetch_assoc();







$agence_retrait_data = [];
$sql_agence = "SELECT nom, adresse_complete_retrait, ville_retrait FROM agence_retrait_postemaroc";
$result_agence = $conn->query($sql_agence);
while ($row = $result_agence->fetch_assoc()) {
    $agence_retrait_data[$row['nom']] = [
        'adresse_poste' => $row['adresse_complete_retrait'],
        'ville_retrait' => $row['ville_retrait']
    ];
}


$_SESSION['nom'] = $user_info['nom'];
$_SESSION['prenom'] = $user_info['prenom'];



// Préparez une requête pour récupérer les détails des porteurs sélectionnés
$sql = "SELECT  adresse_pro FROM adresse_professionnelle ";
$result = $conn->query($sql);

$adresseprofessionnelle = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $adresseprofessionnelle[] = $row;
    }
} else {
    die("Aucune adresse trouvée.");
}


$sql_civilite = "SELECT civilite  FROM civilite";
$result_civilite = $conn->query($sql_civilite);
$civilite = [];
while ($row = $result_civilite->fetch_assoc()) {
    $civilite[] = $row['civilite'];
}


// Récupérer le nombre de notifications non lues pour cet utilisateur
$stmt_count_notifications = $conn->prepare("SELECT COUNT(*) as unread_count FROM notifications_utilisateur WHERE cin = ? AND read_status = 0");
$stmt_count_notifications->bind_param("s", $cin);
$stmt_count_notifications->execute();
$result_count = $stmt_count_notifications->get_result();
$unread_count = $result_count->fetch_assoc()['unread_count'];



$conn->close();



?>
<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 40px auto;
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.header {
    background-color: #333;
    color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #444;
}

.header a {
    color: #fff;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    background-color: #555;
    transition: background-color 0.2s ease;
}

.header a:hover {
    background-color: #777;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
    font-family: 'Calibri';
}

.user-info {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #f9f9f9;
}


form, .user-info {
    display: none;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #f9f9f9;
}


form.active, .user-info.active {
     display: block;
}



label {
    margin-top: 10px;
    color: #555;
}

input[type="text"], input[type="email"] {
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

.success-message {
    color: green;
    margin-top: 20px;
    font-size: 16px;
    text-align: center;
}

.input-wrapper {
    padding: 0px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
}

.input-wrapper select {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    height: 38px;
}
.hidden {
    display: none;
}

.show {
    display: block;
}
.menu-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
}
        
.menu-link:hover {
    background-color: #f0f0f0;
}

.menu-link i {
    margin-right: 8px;
}

.notification-button {
    position: relative;
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
        function confirmLogout() {
            if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
                window.location.href = '../logout.php'; // Rediriger vers le script de déconnexion si l'utilisateur clique sur OK
            }
            // Sinon, rien ne se passe si l'utilisateur clique sur Annuler
        }
    </script>
</head>
<body>
<div class="header">
<a href="javascript:void(0)" onclick="showSection('info')" class="menu-link">
    <i class="fas fa-user"></i> Mes Infos
</a>

<a href="javascript:void(0)" onclick="showSection('edit')" class="menu-link">
    <i class="fas fa-edit"></i> Modifier Mes Infos
</a>

<a href="certificat_requests.php" class="menu-link">
    <i class="fas fa-certificate"></i> Mes Demandes de Certificats
</a>

<a href="reponses.php" class="menu-link">
    <i class="fas fa-question-circle"></i> Questions secrets
</a>

<a href="../pdf_storage1/Demande_<?php echo $cin; ?>.pdf?timestamp=<?php echo time(); ?>" target="_blank" class="menu-link">
    <i class="fas fa-file-pdf"></i> Mon Dossier PDF
</a>


<a href="user_notifications.php" class="menu-link notification-button">
    <i class="fas fa-bell"></i> Notifications
    <?php if ($unread_count > 0) { echo "<span class='notification-count'>$unread_count</span>"; } ?>
</a>

<a href="javascript:void(0)" onclick="confirmLogout()" class="menu-link">
    <i class="fas fa-sign-out-alt"></i> Déconnexion
</a>


</div>
    <div class="container">
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></h1>
        
        <div class="user-info active" id="info">
            <h2>Vos Informations:</h2>
            <p><strong>Civilité:</strong> <?php echo htmlspecialchars($user_info['civilite']); ?></p>
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user_info['nom']); ?></p>
            <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user_info['prenom']); ?></p>
            <p><strong>CIN:</strong> <?php echo htmlspecialchars($user_info['cin']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
            <p><strong>Fonction:</strong> <?php echo htmlspecialchars($user_info['fonction']); ?></p>
            <p><strong>GSM:</strong> <?php echo htmlspecialchars($user_info['gsm']); ?></p>
            <p><strong>Bureau:</strong> <?php echo htmlspecialchars($user_info['bureau']); ?></p>
            <p><strong>Fax:</strong> <?php echo htmlspecialchars($user_info['fax']); ?></p>
            <p><strong>Adresse Professionnelle:</strong> <?php echo htmlspecialchars($user_info['adresse_pro']); ?></p>
            <p><strong>Ville:</strong> <?php echo htmlspecialchars($user_info['ville']); ?></p>
            <p><strong>Pays:</strong> <?php echo htmlspecialchars($user_info['pays']); ?></p>
            <p><strong>Agence de retrait:</strong> <?php echo htmlspecialchars($user_info_nv['agence_retrait']); ?></p>
            <p><strong>Adresse postale de retrait:</strong> <?php echo htmlspecialchars($user_info_nv['adresse_poste']); ?></p>
            <p><strong>Ville de retrait:</strong> <?php echo htmlspecialchars($user_info_nv['ville_retrait']); ?></p>
        </div>

        <form method="POST" action=""  id="edit" class="hidden">
        
            <?php if (!$success_message) { echo '<div class="success-message">' . htmlspecialchars($success_message) . '</div>'; } ?>
            <input type="hidden" name="admin_email" value="souadhajar16@gmail.com">

            <label for="civilite">Civilité:</label>
            <input type="text" id="civilite" name="civilite" value="<?php echo isset($user_info['civilite']) ? htmlspecialchars($user_info['civilite']) : ''; ?>" required>


            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo isset($user_info['nom']) ? htmlspecialchars($user_info['nom']) : ''; ?>" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo isset($user_info['prenom']) ? htmlspecialchars($user_info['prenom']) : ''; ?>" required>

            <label for="cin">CIN:</label>
            <input type="text" id="cin" name="cin" value="<?php echo isset($user_info['cin']) ? htmlspecialchars($user_info['cin']) : ''; ?>" required>

            <label for="email">Adresse Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($user_info['email']) ? htmlspecialchars($user_info['email']) : ''; ?>" required>

            <label for="fonction">Fonction:</label>
            <input type="text" id="fonction" name="fonction" value="<?php echo isset($user_info['fonction']) ? htmlspecialchars($user_info['fonction']) : ''; ?>" required>

            <label for="gsm">GSM:</label>
            <input type="text" id="gsm" name="gsm" value="<?php echo isset($user_info['gsm']) ? htmlspecialchars($user_info['gsm']) : ''; ?>" required>

            <label for="bureau">Bureau:</label>
            <input type="text" id="bureau" name="bureau" value="<?php echo isset($user_info['bureau']) ? htmlspecialchars($user_info['bureau']) : ''; ?>" required>

            <label for="fax">Fax:</label>
            <input type="text" id="fax" name="fax" value="<?php echo isset($user_info['fax']) ? htmlspecialchars($user_info['fax']) : ''; ?>" required>
            
            <label for="adresse_pro">Adresse professionnelle :</label>
                <select id="adresse_pro" name="adresse_pro" style="padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;" required>
                    <?php foreach ($adresseprofessionnelle as $adressepro): ?>
                        <option value="<?php echo $adressepro['adresse_pro']; ?>"><?php echo $adressepro['adresse_pro']; ?></option>
                    <?php endforeach; ?>
                </select>

            <label for="ville">Ville:</label>
            <input type="text" id="ville" name="ville" value="<?php echo isset($user_info['ville']) ? htmlspecialchars($user_info['ville']) : ''; ?>" required>

            <label for="pays">Pays:</label>
            <input type="text" id="pays" name="pays" value="<?php echo isset($user_info['pays']) ? htmlspecialchars($user_info['pays']) : ''; ?>" required>
            <br>

            <label for="agence_retrait">Agence retrait :</label>
            <select name="agence_retrait" id="agence_retrait"   style="padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;" required> 
                <option value="">Sélectionnez une agence de retrait</option>
                <?php foreach ($agence_retrait_data as $agencyName => $agencyData): ?>
                    <option value="<?php echo $agencyName; ?>"><?php echo $agencyName; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="adresse_poste">Adresse postale de retrait :</label>
                <input type="text" name="adresse_poste" id="adresse_poste" readonly>
            <br>
            <label for="ville_retrait">Ville de retrait :</label>
                <input type="text" name="ville_retrait" id="ville_retrait" readonly>
            

            <button type="submit">Sauvegarder</button>
        </form>




    <script>
    const agenceRetraitData = <?php echo json_encode($agence_retrait_data); ?>;

    document.getElementById('agence_retrait').addEventListener('change', function() {
        const selectedAgence = this.value;
        const adressePosteInput = document.getElementById('adresse_poste');
        const villeRetraitInput = document.getElementById('ville_retrait');

        if (selectedAgence in agenceRetraitData) {
            adressePosteInput.value = agenceRetraitData[selectedAgence].adresse_poste;
            villeRetraitInput.value = agenceRetraitData[selectedAgence].ville_retrait;
        } else {
            adressePosteInput.value = '';
            villeRetraitInput.value = '';
        }
    });
</script>
    
    </div>
</body>
</html>