<?php
if (isset($_GET['cinList'])) {
    $cinList = explode(",", $_GET['cinList']);
} else {
    die("Aucun porteur sélectionné.");
}

$error_message = '';
$success_message = '';
include '../connect.php';





$sql_domaine = "SELECT  nom_domaine FROM domaine_application";
$result_domaine = $conn->query($sql_domaine);
$domaine = [];
while ($row = $result_domaine->fetch_assoc()) {
    $domaine[] = $row['nom_domaine'];
}

// Create an array to store the agence retrait data
$agence_retrait_data = [];
$sql_agence = "SELECT nom, adresse_complete_retrait, ville_retrait FROM agence_retrait_postemaroc";
$result_agence = $conn->query($sql_agence);
while ($row = $result_agence->fetch_assoc()) {
    $agence_retrait_data[$row['nom']] = [
        'adresse_poste' => $row['adresse_complete_retrait'],
        'ville_retrait' => $row['ville_retrait']
    ];
}


$sql_adresse = "SELECT adresse_complete_retrait  FROM agence_retrait_postemaroc";
$result_adresse = $conn->query($sql_adresse);
$adresse = [];
while ($row = $result_adresse->fetch_assoc()) {
    $adresse[] = $row['adresse_complete_retrait'];
}

$sql_ville = "SELECT ville_retrait  FROM agence_retrait_postemaroc";
$result_ville = $conn->query($sql_ville);
$ville = [];
while ($row = $result_ville->fetch_assoc()) {
    $ville[] = $row['ville_retrait'];
}

// Récupérer les types de certificat depuis la base de données
$sql_certificats = "SELECT  typecertif FROM type_certificat";
$result_certificats = $conn->query($sql_certificats);
$certificats = [];
while ($row = $result_certificats->fetch_assoc()) {
    $certificats[] = $row['typecertif'];
}

// Récupérer les types de demande depuis la base de données
$sql_demandes = "SELECT type FROM type_demande";
$result_demandes = $conn->query($sql_demandes);
$demandes = [];
while ($row = $result_demandes->fetch_assoc()) {
    $demandes[] = $row['type'];
}

// Traitement du formulaire pour importer le fichier Excel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel'])) {
    if ($_FILES['excel_file']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file']['tmp_name'])) {
        $file = $_FILES['excel_file']['tmp_name'];

        try {
            require '../vendor/autoload.php'; // Assurez-vous d'inclure le fichier autoload.php correctement
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la base de données
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present
                $domaine_app = $row[0];
                $type_demande = $row[1];
                $civilite = $row[2];
                $prenom = $row[3];
                $nom = $row[4];
                $cin = $row[5];
                $bkam = $row[6];
                $email = $row[7];
                $gsm = $row[8];
                $adresse_pro = $row[9];
                $agence_retrait = $row[10];
                $ville_retrait = $row[11];
                $adresse_poste = $row[12];
                $type_certif = $row[13];

                            
                            // Check if CIN already exists in porteurnv table
                $sql_check_cin = "SELECT COUNT(*) as count FROM porteurnv WHERE cin = ?";
                $stmt_check_cin = $conn->prepare($sql_check_cin);
                $stmt_check_cin->bind_param("s", $cin);
                $stmt_check_cin->execute();
                $result_check_cin = $stmt_check_cin->get_result();
                $count = $result_check_cin->fetch_assoc()['count'];

                if ($count == 0) {
                    // Insert new record
                    $sql = "INSERT INTO porteurnv (domaine_app, type_demande, civilite, prenom, nom, cin, bkam, email, gsm, adresse_pro, agence_retrait, ville_retrait, adresse_poste, type_certif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssssssssss", $domaine_app, $type_demande, $civilite, $nom, $prenom, $cin, $bkam, $email, $gsm, $adresse_pro, $agence_retrait, $ville_retrait, $adresse_poste, $type_certif);
                    if ($stmt->execute()) {
                        $success_count++;
                
                        // Générer les dates de production et d'expiration
                        $date_demande = date('Y-m-d'); // Date actuelle
                        //$date_expiration = date('Y-m-d', strtotime('+2 year')); // Un an après la date actuelle
                
                        // Insertion dans la base de données certificat
                        $sql_cert = "INSERT INTO certificat (cin, nom, prenom, adresse_pro, type_demande, date_demande) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt_cert = $conn->prepare($sql_cert);
                        $stmt_cert->bind_param("ssssss", $cin, $nom, $prenom, $adresse_pro, $type_demande, $date_demande);
                        if ($stmt_cert->execute()) {

                            // Récupérer le CIN ou d'autres informations pertinentes de l'utilisateur
                                //$cin = $_POST['cin'];
                                $message = "Vous avez une demande de type " . $type_demande . " enregistrée avec succès.";

                                // Insérer dans la table notifications_utilisateur
                                $stmt_notification = $conn->prepare("INSERT INTO notifications_utilisateur (cin, message) VALUES (?, ?)");
                                $stmt_notification->bind_param("ss", $cin, $message);
                                $stmt_notification->execute();

                            // Insertion réussie
                        } else {
                            $error_count++;
                            $error_message = "Erreur lors de l'importation du fichier Excel : Duplicate entry '$cin' for key 'PRIMARY' dans la table certificat";
                        }
                    } else {
                        $error_count++;
                        $error_message = "Erreur lors de l'importation du fichier Excel : Erreur d'insertion dans la table porteurnv";
                    }
                } else {
                    // Skip inserting if CIN already exists
                    $error_message = "CIN $cin already exists in the database.";
                }
            }

            // Afficher un message de succès ou d'erreur après le traitement
            $success_message = "$success_count lignes ont été importées avec succès.";
            if ($error_count > 0) {
                $error_message = "Erreur lors de l'importation de $error_count lignes.";
            }
        } catch (Exception $e) {
            $error_message = "Erreur lors de l'importation du fichier Excel : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls).";
    }

}   elseif (isset($_POST['submit_data'])) {
            // Récupération des données du formulaire
            $domaine_app = $_POST['domaine_app'];
            $type_demande = $_POST['type_demande'];
            $civilite = $_POST['civilite'];
            $prenom = $_POST['prenom'];
            $nom = $_POST['nom'];
            $cin = $_POST['cin'];
            $bkam = $_POST['bkam'];
            $email = $_POST['email'];
            $gsm = $_POST['gsm'];
            $adresse_pro = $_POST['adresse_pro'];
            $agence_retrait = $_POST['agence_retrait'];
            $ville_retrait = $_POST['ville_retrait'];
            $adresse_poste = $_POST['adresse_poste'];
            $type_certif = $_POST['type_certif'];

            $sql = "INSERT INTO porteurnv (domaine_app, type_demande, civilite, prenom, nom, cin, bkam, email, gsm, adresse_pro, agence_retrait, ville_retrait, adresse_poste, type_certif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssssss", $domaine_app, $type_demande, $civilite, $prenom, $nom,  $cin, $bkam, $email, $gsm, $adresse_pro, $agence_retrait, $ville_retrait, $adresse_poste, $type_certif);
        
            if ($stmt->execute()) {
                $date_demande = date('Y-m-d');
                //$date_expiration = date('Y-m-d', strtotime('+2 years'));
        
                $sql_cert = "INSERT INTO certificat (cin, nom, prenom, adresse_pro, type_demande, date_demande) VALUES (?, ?, ?, ?, ? ,? ,?)";
                $stmt_cert = $conn->prepare($sql_cert);
                $stmt_cert->bind_param("ssssss", $cin, $nom, $prenom, $adresse_pro,$type_demande, $date_demande);
        
                if ($stmt_cert->execute()) {
                    $success_message = "Enregistrement manuel effectué avec succès.";
                    // Récupérer le CIN ou d'autres informations pertinentes de l'utilisateur
                    //$cin = $_POST['cin'];
                    $message = "Vous avez une demande enregistrée avec succès.";

                    // Insérer dans la table notifications_utilisateur
                    $stmt_notification = $conn->prepare("INSERT INTO notifications_utilisateur (cin, message) VALUES (?, ?)");
                    $stmt_notification->bind_param("ss", $cin, $message);
                    $stmt_notification->execute();

                } else {
                    $error_message = "Erreur lors de l'enregistrement dans la table certificat : " . $conn->error;
                }
            } else {
                $error_message = "Erreur lors de l'enregistrement manuel dans la table porteurnv : " . $conn->error;
            }
}

include '../connect.php';

// Préparez une requête pour récupérer les détails des porteurs sélectionnés
$sql = "SELECT cin, nom, prenom, email, adresse_pro, gsm FROM porteurinfo WHERE cin IN (" . implode(",", array_map(function($cin) { return "'" . $cin . "'"; }, $cinList)) . ")";
$result = $conn->query($sql);

$porteursDetails = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $porteursDetails[] = $row;
    }
} else {
    die("Aucun détail de porteur trouvé pour les CIN sélectionnés.");
}
$conn->close();
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remplir enregistrement</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .message.error {
            background-color: #ffcccc;
            border: 1px solid #ff9999;
        }
        .message.success {
            background-color: #ccffcc;
            border: 1px solid #99ff99;
        }
        form {
            margin-top: 20px;
            text-align: center;
        }
        form input[type="file"], form input[type="text"], form input[type="email"], form input[type="tel"], form input[type="date"], form select {
            margin-bottom: 10px;
            padding: 8px;
            width: calc(100% - 16px); /* Pour compenser les paddings */
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Remplir enregistrement initial pour les porteurs sélectionnés</h1>
    <?php if (!empty($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (!isset($_POST['submit_data']) && !isset($_POST['submit_excel'])): ?>
        <form method="post" enctype="multipart/form-data">
            <h3>Importer un fichier Excel</h3>
            <input type="file" name="excel_file" accept=".xlsx, .xls" required>
            <br>
            <button type="submit" name="submit_excel">Importer</button>
        </form>
        <hr>
        <form method="post">
            <h3>OU Remplir manuellement :</h3>
            
            <label for="domaine_app">Domaine d'application :</label>
            <select name="domaine_app" id="domaine_app" required>
                <option value="">Sélectionnez un domaine d'application</option>
                <?php foreach ($domaine as $id => $libelle): ?>
                    <option value="<?php echo $libelle; ?>"><?php echo $libelle; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="type_demande">Type de demande :</label>
            <select name="type_demande" id="type_demande" required>
                <option value="">Sélectionnez un type de demande</option>
                <?php foreach ($demandes as $id => $libelle): ?>
                    <option value="<?php echo $libelle; ?>"><?php echo $libelle; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="civilite">Civilité :</label>
            <select name="civilite" id="civilite" required>
                <option value="Monsieur">Monsieur</option>
                <option value="Madame">Madame</option>
            </select>
            <br>
            <label for="prenom">Prénom :</label>
            <select name="prenom" id="prenom" required>
                <?php foreach ($porteursDetails as $porteur): ?>
                    <option value="<?php echo $porteur['prenom']; ?>"><?php echo $porteur['prenom']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="nom">Nom :</label>
            <select name="nom" id="nom" required>
                <?php foreach ($porteursDetails as $porteur): ?>
                    <option value="<?php echo $porteur['nom']; ?>"><?php echo $porteur['nom']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="cin">CIN :</label>
                <select name="cin" id="cin" required>
                    <?php foreach ($cinList as $cin): ?>
                        <option value="<?php echo $cin; ?>"><?php echo $cin; ?></option>
                    <?php endforeach; ?>
                </select>

            <br>
            <label for="bkam">BKAM :</label>
            <input type="text" name="bkam" id="bkam" >
            <br>
            <label for="email">Email :</label>
            <select name="email" id="email" required>
                <?php foreach ($porteursDetails as $porteur): ?>
                    <option value="<?php echo $porteur['email']; ?>"><?php echo $porteur['email']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="gsm">GSM :</label>
            <select name="gsm" id="gsm" required>
                <?php foreach ($porteursDetails as $porteur): ?>
                    <option value="<?php echo $porteur['gsm']; ?>"><?php echo $porteur['gsm']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="adresse_pro">Adresse professionnelle :</label>
            <select name="adresse_pro" id="adresse_pro" required>
                <?php foreach ($porteursDetails as $porteur): ?>
                    <option value="<?php echo $porteur['adresse_pro']; ?>"><?php echo $porteur['adresse_pro']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="agence_retrait">Agence retrait :</label>
            <select name="agence_retrait" id="agence_retrait" required>
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
            
            <br>
            <label for="type_certif">Type de certificat :</label>
            <select name="type_certif" id="type_certif" required>
                <option value="">Sélectionnez un type de certificat</option>
                <?php foreach ($certificats as $id => $libelle): ?>
                    <option value="<?php echo $libelle; ?>"><?php echo $libelle; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <div class="button-container">
                <button type="submit" name="submit_data">Enregistrer</button>
                <button type="button" onclick="window.location.href='dashboard1.php';">Retour</button>
            </div>
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
    <?php endif; ?>
</div>
</body>
</html>