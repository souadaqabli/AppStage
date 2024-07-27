<?php
if (isset($_GET['cinList'])) {
    $cinList = explode(",", $_GET['cinList']);
} else {
    die("Aucun porteur sélectionné.");
}

$error_message = '';
$success_message = '';
include '../connect.php';

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

                // Insertion dans la base de données
                $sql = "INSERT INTO porteurnv (domaine_app, type_demande, civilite, nom, prenom, cin, bkam ,email, gsm, adresse_pro, agence_retrait, ville_retrait, adresse_poste, type_certif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssssss", $domaine_app, $type_demande, $civilite, $nom, $prenom, $cin, $bkam, $email, $gsm, $adresse_pro, $agence_retrait, $ville_retrait, $adresse_poste, $type_certif);

                if ($stmt->execute()) {
                    $success_count++;

                    // Générer les dates de production et d'expiration
                    $date_demande = date('Y-m-d'); // Date actuelle
                    //$date_expiration = date('Y-m-d', strtotime('+2 year')); // Deux ans après la date actuelle

                    // Insertion dans la base de données certificat
                    $sql_cert = "INSERT INTO certificat (cin, nom, prenom, adresse_pro, type_demande, date_demande) VALUES (?, ?, ?, ?, ? , ?)";
                    $stmt_cert = $conn->prepare($sql_cert);
                    $stmt_cert->bind_param("ssssss", $cin, $nom, $prenom, $adresse_pro, $type_demande, $date_demande);

                    if (!$stmt_cert->execute()) {
                        $error_count++;
                        // Gérer les erreurs d'insertion dans la table certificat si nécessaire
                    }
                } else {
                    $error_count++;
                    // Gérer les erreurs d'insertion dans la table porteurnv si nécessaire
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
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importer un fichier Excel</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            padding: 40px;
            border: 1px solid #ccc;
            width: 600px;
            height: 400px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .message.error {
            color: red;
        }
        .message.success {
            color: green;
        }
        .button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 60px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #218838;
        }
        .form-container {
            margin-bottom: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-family: 'Calibri';
            
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Importer un fichier Excel</h1>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-container">
                <input type="file" name="excel_file" accept=".xlsx, .xls" required>
            </div>
            <div class="form-container">
                <button type="submit" name="submit_excel" class="button">Importer</button>
                <button type="button"  class="button" onclick="window.location.href='dashboard1.php';">Retour</button>
            </div>
        </form>
    </div>

    <script>
        function showSection(sectionId) {
            document.getElementById('step1').style.display = 'none';
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
</body>
</html>
