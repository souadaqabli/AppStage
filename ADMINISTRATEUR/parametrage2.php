<?php

$error_message_demande = '';
$success_message_demande = '';
$error_message_cert = '';
$success_message_cert = '';
$error_message_organisme = '';
$success_message_organisme = '';

$step = isset($_SESSION['step']) ? $_SESSION['step'] : 'step1';

// Traitement du formulaire pour importer le fichier Excel pour type de demande
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_typedemande'])) {
    $error_message_demande = '';
    $success_message_demande = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_typedemande']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_typedemande']['tmp_name'])) {
        $file = $_FILES['excel_file_typedemande']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table type_demande
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $typedemande = isset($row[0]) ? $row[0] : null;

                if ($typedemande !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO type_demande (type) 
                            VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $typedemande);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_demande = "$success_count lignes ont été importées avec succès pour type de demande.";
            }
            if ($error_count > 0) {
                $error_message_demande = "Erreur lors de l'importation de $error_count lignes pour type de demande.";
            }
        } catch (Exception $e) {
            $error_message_demande = "Erreur lors de l'importation du fichier Excel pour type de demande : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_demande = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour type de demande.";
    }
}

// Traitement du formulaire pour importer le fichier Excel pour type de certificat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_certificat'])) {
    $error_message_cert = '';
    $success_message_cert = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_certificat']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_certificat']['tmp_name'])) {
        $file = $_FILES['excel_file_certificat']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table type_certificat
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $certif = isset($row[0]) ? $row[0] : null;

                if ($certif !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO type_certificat (typecertif) 
                            VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $certif);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
            }
        }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_cert = "$success_count lignes ont été importées avec succès pour certificat.";
            }
            if ($error_count > 0) {
                $error_message_cert = "Erreur lors de l'importation de $error_count lignes pour certificat.";
            }
        } catch (Exception $e) {
            $error_message_cert = "Erreur lors de l'importation du fichier Excel pour certificat : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_cert = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour certificat.";
    }
}

// Traitement du formulaire pour importer le fichier Excel pour organisme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_organisme'])) {
    $error_message_organisme = '';
    $success_message_organisme = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_organisme']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_organisme']['tmp_name'])) {
        $file = $_FILES['excel_file_organisme']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table organisme
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $nom_organisme = isset($row[0]) ? $row[0] : null;

                if ($nom_organisme !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO organisme (nom_organisme) 
                            VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $nom_organisme);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_organisme = "$success_count lignes ont été importées avec succès pour organisme.";
            }
            if ($error_count > 0) {
                $error_message_organisme = "Erreur lors de l'importation de $error_count lignes pour organisme.";
            }
        } catch (Exception $e) {
            $error_message_organisme = "Erreur lors de l'importation du fichier Excel pour organisme : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_organisme = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour organisme.";
    }
}
// Traitement du formulaire pour importer le fichier Excel pour domaine_application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_domaine'])) {
    $error_message_domaine = '';
    $success_message_domaine = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_domaine']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_domaine']['tmp_name'])) {
        $file = $_FILES['excel_file_domaine']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table organisme
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $nom_domaine = isset($row[0]) ? $row[0] : null;

                if ($nom_domaine !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO domaine_application (nom_domaine) 
                            VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $nom_domaine);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_domaine = "$success_count lignes ont été importées avec succès pour domaine application.";
            }
            if ($error_count > 0) {
                $error_message_domaine = "Erreur lors de l'importation de $error_count lignes pour domaine application.";
            }
        } catch (Exception $e) {
            $error_message_domaine = "Erreur lors de l'importation du fichier Excel pour domaine application : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_domaine = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour domaine application.";
    }
}
// Traitement du formulaire pour importer le fichier Excel pour agence retrait
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_retrait'])) {
    $error_message_retrait = '';
    $success_message_retrait = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_retrait']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_retrait']['tmp_name'])) {
        $file = $_FILES['excel_file_retrait']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table organisme
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $nom = isset($row[0]) ? $row[0] : null;
                $adresse_complete = isset($row[1]) ? $row[1] : null;
                $ville_retrait = isset($row[2]) ? $row[2] : null;

                if ($nom !== null && $adresse_complete !== null && $ville_retrait !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO agence_retrait_postemaroc (nom, adresse_complete_retrait, ville_retrait ) 
                            VALUES (?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $nom, $adresse_complete, $ville_retrait);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_retrait = "$success_count lignes ont été importées avec succès pour agence retrait .";
            }
            if ($error_count > 0) {
                $error_message_retrait = "Erreur lors de l'importation de $error_count lignes pour agence retrait .";
            }
        } catch (Exception $e) {
            $error_message_retrait = "Erreur lors de l'importation du fichier Excel pour agence retrait : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_retrait = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour agence retrait.";
    }
}
// Traitement du formulaire pour importer le fichier Excel pour agence depot
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_depot'])) {
    $error_message_depot = '';
    $success_message_depot = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_depot']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_depot']['tmp_name'])) {
        $file = $_FILES['excel_file_depot']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table organisme
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $nom = isset($row[0]) ? $row[0] : null;
                $adresse_comp = isset($row[1]) ? $row[1] : null;
                $ville_depot = isset($row[2]) ? $row[2] : null;

                if ($nom !== null && $adresse_comp !== null && $ville_depot !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO agence_depot_postemaroc (nom, adresse_complete_depot, ville_depot ) 
                            VALUES (?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $nom, $adresse_comp, $ville_depot);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_depot = "$success_count lignes ont été importées avec succès pour agence depot .";
            }
            if ($error_count > 0) {
                $error_message_depot = "Erreur lors de l'importation de $error_count lignes pour agence depot .";
            }
        } catch (Exception $e) {
            $error_message_depot = "Erreur lors de l'importation du fichier Excel pour agence depot : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_depot = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour agence depot .";
    }
}
// Traitement du formulaire pour importer le fichier Excel pour adresse_professionnelle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_adresse_pro'])) {
    $error_message_adresse_pro = '';
    $success_message_adresse_pro = ''; // Réinitialisation du message de succès

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_adresse_pro']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_adresse_pro']['tmp_name'])) {
        $file = $_FILES['excel_file_adresse_pro']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table adresse_professionnelle
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $adresse = isset($row[0]) ? $row[0] : null;

                if ($adresse !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO adresse_professionnelle (adresse_pro) 
                            VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $adresse);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Mettre à jour le message de succès ou d'erreur après le traitement
            if ($success_count > 0) {
                $success_message_adresse_pro = "$success_count lignes ont été importées avec succès pour adresse professionnelle.";
            }
            if ($error_count > 0) {
                $error_message_adresse_pro = "Erreur lors de l'importation de $error_count lignes pour adresse professionnelle.";
            }
        } catch (Exception $e) {
            $error_message_adresse_pro = "Erreur lors de l'importation du fichier Excel pour adresse professionnelle : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_adresse_pro = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour adresse professionnelle.";
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <style>



        h3 {
            
            margin-bottom: 20px;
            font-family: 'Calibri';
            color: #333;
                    
        }

        .flex-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            width: 100%;
        }

        .form-container {
            flex: 1;
            margin-right: 20px;
        }
        .flex-container2 {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            width: 100%;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .message.success {
            background-color: #dff0d8;
            border: 1px solid #b2dba1;
            color: #3c763d;
        }

        .message.error {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .btn {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #28a745; /* Vert */
        color: #fff;
        
    }

    a{
    text-align: center;   
    display: block;
    width: 100%;
    }

</style>
</head>

<body>
    <div class="flex-container">
        <div class="form-container">
            <h3>Paramétrer la table type de demande</h3>
            <?php if (!empty($error_message_demande)): ?>
                <div class="message error"><?php echo $error_message_demande; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_demande)): ?>
                <div class="message success"><?php echo $success_message_demande; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_typedemande" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_typedemande" class="btn btn-success">Importer</button>
            </form>
        </div>

        <div class="form-container">
            <h3>Paramétrer la table type de certificat</h3>
            <?php if (!empty($error_message_cert)): ?>
                <div class="message error"><?php echo $error_message_cert; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_cert)): ?>
                <div class="message success"><?php echo $success_message_cert; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_certificat" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_certificat" class="btn btn-success">Importer</button>
            </form>
        </div>

        <div class="form-container">
            <h3>Paramétrer la table organisme</h3>
            <?php if (!empty($error_message_organisme)): ?>
                <div class="message error"><?php echo $error_message_organisme; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_organisme)): ?>
                <div class="message success"><?php echo $success_message_organisme; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_organisme" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_organisme" class="btn btn-success">Importer</button>
            </form>
        </div>
        
    </div>
    <div class="flex-container2">
    <div class="form-container">
            <h3>Paramétrer la table domaine d'application</h3>
            <?php if (!empty($error_message_domaine)): ?>
                <div class="message error"><?php echo $error_message_domaine; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_domaine)): ?>
                <div class="message success"><?php echo $success_message_domaine; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_domaine" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_domaine" class="btn btn-success">Importer</button>
            </form>
        </div>
        <div class="form-container">
            <h3>Paramétrer la table agence dépot</h3>
            <?php if (!empty($error_message_depot)): ?>
                <div class="message error"><?php echo $error_message_depot; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_depot)): ?>
                <div class="message success"><?php echo $success_message_depot; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_depot" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_depot" class="btn btn-success">Importer</button>
            </form>
        </div>
        <div class="form-container">
            <h3>Paramétrer la table agence retrait</h3>
            <?php if (!empty($error_message_demande)): ?>
                <div class="message error"><?php echo $error_message_retrait; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_retrait)): ?>
                <div class="message success"><?php echo $success_message_retrait; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_retrait" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_retrait" class="btn btn-success">Importer</button>
            </form>
        </div>
        <div class="form-container">
            <h3>Paramétrer la table adresse professionnelle</h3>
            <?php if (!empty($error_message_adresse_pro)): ?>
                <div class="message error"><?php echo $error_message_adresse_pro; ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message_adresse_pro)): ?>
                <div class="message success"><?php echo $success_message_adresse_pro; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="excel_file_adresse_pro" accept=".xlsx, .xls" required>
                <button type="submit" name="submit_excel_adresse_pro" class="btn btn-success">Importer</button>
            </form>
        </div>
    </div>
     <!-- Bouton pour revenir à la page précédente -->
     <div class="back-button">
        <a href="dashboard1.php?section=parametrage">&lt; Retour</a>
    </div>
</body>

</html>
