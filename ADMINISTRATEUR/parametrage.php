<?php

$error_message_porteur = '';
$success_message_porteur = '';

$step = isset($_SESSION['step']) ? $_SESSION['step'] : 'step1';

// Traitement du formulaire pour importer le fichier Excel pour porteurinfo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_porteurinfo'])) {
    $error_message_porteur = '';

    if ($_FILES['excel_file_porteurinfo']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_porteurinfo']['tmp_name'])) {
        $file = $_FILES['excel_file_porteurinfo']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table porteurinfo
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                $cin = $row[0];
                $civilite = $row[1];
                $prenom = $row[2];
                $nom = $row[3];
                $email = $row[4];
                $fonction = $row[5];
                $gsm = $row[6];
                $bureau = $row[7];
                $fax = $row[8];
                $adresse_pro = $row[9];
                $ville = $row[10];
                $pays = $row[11];

                // Insertion dans la base de données
                $sql = "INSERT INTO porteurinfo (cin, civilite, prenom, nom, email, fonction, gsm, bureau, fax, adresse_pro, ville, pays) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssss", $cin, $civilite , $prenom, $nom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays);

                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }

            // Afficher un message de succès ou d'erreur après le traitement
            $success_message_porteur = "$success_count lignes ont été importées avec succès pour porteurinfo.";
            if ($error_count > 0) {
                $error_message_porteur = "Erreur lors de l'importation de $error_count lignes pour porteurinfo.";
            }
        } catch (Exception $e) {
            $error_message_porteur = "Erreur lors de l'importation du fichier Excel pour porteurinfo : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_porteur = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour porteurinfo.";
    }
    
}
// Traitement du formulaire pour importer le fichier Excel pour civilité
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_civilite'])) {
    $error_message_civilite = '';

    // Vérifier si le fichier a été correctement uploadé
    if ($_FILES['excel_file_civilite']['error']== UPLOAD_ERR_OK && isset($_FILES['excel_file_civilite']['tmp_name'])) {
        $file = $_FILES['excel_file_civilite']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table civilite
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                // Vérifier que la colonne existe avant de l'assigner
                $civilite_nom = isset($row[0]) ? $row[0] : null;
                $gtr = isset($row[1]) ? $row[1] : null;

                if ($civilite_nom !== null) {
                    // Insertion dans la base de données
                    $sql = "INSERT INTO civilite (civilite) 
                            VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $civilite_nom);

                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }

            // Afficher un message de succès ou d'erreur après le traitement
            $success_message_civilite = "$success_count lignes ont été importées avec succès pour civilite.";
            if ($error_count > 0) {
                $error_message_civilite = "Erreur lors de l'importation de $error_count lignes pour civilite.";
            }
        } catch (Exception $e) {
            $error_message_civilite = "Erreur lors de l'importation du fichier Excel pour civilite : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données après le traitement
        $conn->close();

        // Mettre à jour l'étape actuelle
        $_SESSION['step'] = 'step2';
    } else {
        $error_message_civilite = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour civilite.";
    }
}


// Traitement du formulaire pour importer le fichier Excel pour mandatairecertif
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_mandatairecertif'])) {
    $error_message_certif = '';

    if ($_FILES['excel_file_mandatairecertif']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_mandatairecertif']['tmp_name'])) {
        $file = $_FILES['excel_file_mandatairecertif']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table mandatairecertif
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                // Adapter les colonnes selon votre structure pour mandatairecertif
                $cin = $row[0];
                $civilite = $row[1];
                $prenom = $row[2];
                $nom = $row[3];
                $email = $row[4];
                $fonction = $row[5];
                $gsm = $row[6];
                $bureau = $row[7];
                $fax = $row[8];
                $adresse_pro = $row[9];
                $ville = $row[10];
                $pays = $row[11];

                // Insertion dans la base de données
                $sql = "INSERT INTO mandatairecertif (cin, civilite, prenom, nom, email, fonction, gsm, bureau, fax, adresse_pro, ville, pays) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssss", $cin, $civilite, $prenom, $nom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays);

                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }

            // Afficher un message de succès ou d'erreur après le traitement
            $success_message_certif = "$success_count lignes ont été importées avec succès pour le mandataire de certificat.";
            if ($error_count > 0) {
                $error_message_certif = "Erreur lors de l'importation de $error_count lignes pour le mandataire de certificat.";
            }
        } catch (Exception $e) {
            $error_message_certif = "Erreur lors de l'importation du fichier Excel pour le mandataire de certificat : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_certif = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour le mandataire de certif.";
    }
    $step = 'step2';
    $_SESSION['step'] = $step;
}

// Traitement du formulaire pour importer le fichier Excel pour mandatairetech
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_mandatairetech'])) {
    $error_message_tech = '';

    if ($_FILES['excel_file_mandatairetech']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_mandatairetech']['tmp_name'])) {
        $file = $_FILES['excel_file_mandatairetech']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table mandatairetech
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                // Adapter les colonnes selon votre structure pour mandatairetech
                $cin = $row[0];
                $civilite = $row[1];
                $prenom = $row[2];
                $nom = $row[3];
                $email = $row[4];
                $fonction = $row[5];
                $gsm = $row[6];
                $bureau = $row[7];
                $fax = $row[8];
                $adresse_pro = $row[9];
                $ville = $row[10];
                $pays = $row[11];

                // Insertion dans la base de données
                $sql = "INSERT INTO mandatairetech (cin, civilite, prenom, nom, email, fonction, gsm, bureau, fax, adresse_pro, ville, pays) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssss", $cin, $civilite, $prenom, $nom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays);

                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }

            // Afficher un message de succès ou d'erreur après le traitement
            $success_message_tech = "$success_count lignes ont été importées avec succès pour mandataire technique.";
            if ($error_count > 0) {
                $error_message_tech = "Erreur lors de l'importation de $error_count lignes pour mandataire technique.";
            }
        } catch (Exception $e) {
            $error_message_tech = "Erreur lors de l'importation du fichier Excel pour mandataire technique : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_tech = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour mandataire technique.";
    }
    $step = 'step2';
    $_SESSION['step'] = $step;
}
// Traitement du formulaire pour importer le fichier Excel pour represenatantlegal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_excel_representantlegal'])) {
    $error_message_legal = '';

    if ($_FILES['excel_file_representantlegal']['error'] == UPLOAD_ERR_OK && isset($_FILES['excel_file_representantlegal']['tmp_name'])) {
        $file = $_FILES['excel_file_representantlegal']['tmp_name'];

        try {
            require '../vendor/autoload.php';
            include '../connect.php';

            // Charger le fichier Excel avec PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $success_count = 0;
            $error_count = 0;

            // Parcourir les lignes du fichier Excel et insérer dans la table mandatairetech
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header row if present

                // Adapter les colonnes selon votre structure pour mandatairetech
                $cin = $row[0];
                $civilite = $row[1];
                $prenom = $row[2];
                $nom = $row[3];
                $email = $row[4];
                $fonction = $row[5];
                $gsm = $row[6];
                $bureau = $row[7];
                $fax = $row[8];
                $adresse_pro = $row[9];
                $ville = $row[10];
                $pays = $row[11];

                // Insertion dans la base de données
                $sql = "INSERT INTO representantlegal (cin, civilite, prenom, nom, email, fonction, gsm, bureau, fax, adresse_pro, ville, pays) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssss", $cin, $civilite, $prenom, $nom, $email, $fonction, $gsm, $bureau, $fax, $adresse_pro, $ville, $pays);

                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }

            // Afficher un message de succès ou d'erreur après le traitement
            $success_message_legal = "$success_count lignes ont été importées avec succès pour representant legal.";
            if ($error_count > 0) {
                $error_message_legal = "Erreur lors de l'importation de $error_count lignes pour representant legal.";
            }
        } catch (Exception $e) {
            $error_message_legal = "Erreur lors de l'importation du fichier Excel pour representant legal : " . $e->getMessage();
        }

        // Fermer la connexion à la base de données
        $conn->close();
    } else {
        $error_message_legal = "Veuillez sélectionner un fichier Excel valide (.xlsx ou .xls) pour mandataire technique.";
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
            display: flex; /* Cacher par défaut */
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            border: 1px 
            margin-bottom: 20px;
            width: 100%;
        }
        .form-group-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            border: 1px 
            margin-bottom: 20px;
            margin-top: 20px;
            width: 100%;
        }
        .container {
        width: 100%;
        padding: 20px;
        margin-bottom: 20px;
        }

        .step.active {
            display: flex; /* Afficher la section active */
        }

        .form-container {
            flex: 1;
            margin-right: 20px;
            flex-direction: row;
            width: 100%;
        }

        .search-container {
            flex: 1;
            margin-right: 20px;
        }

        .next-button, .prev-button {
            display: inline-block; /* Afficher en ligne */
            width: 100px;
            margin-top: 10px;
            padding: 5px;S
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .next-button {
            margin-left: 10px; /* Marge entre les boutons */
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
    </style>
</head>

<div class="flex-container step <?php echo $step == 'step1' ? 'active' : ''; ?>" id="step1">
    <div class="form-container">
        <h3>Paramétrer la table porteur</h3>
        <?php if (!empty($error_message_porteur) && isset($_POST['submit_excel_porteurinfo'])): ?>
            <div class="message error"><?php echo $error_message_porteur; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message_porteur) && isset($_POST['submit_excel_porteurinfo'])): ?>
            <div class="message success"><?php echo $success_message_porteur; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file_porteurinfo" accept=".xlsx, .xls" required>
            <button type="submit" name="submit_excel_porteurinfo">Importer</button>
            
        </form>
    </div>

    <div class="form-container">
        <h3>Paramétrer la table civilité</h3>
        <?php if (!empty($error_message_civilite) && isset($_POST['submit_excel_civilite'])): ?>
            <div class="message error"><?php echo $error_message_civilite; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message_civilite) && isset($_POST['submit_excel_civilite'])): ?>
            <div class="message success"><?php echo $success_message_civilite; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file_civilite" accept=".xlsx, .xls" required>
            <button type="submit" name="submit_excel_civilite">Importer</button>
        </form>
    </div>

    <div class="search-container">
        <h3>Recherche par CIN</h3>
        <form method="post" action="search_result.php">
            <input type="text" name="search_cin" placeholder="Entrez le CIN">
            <button type="submit">Rechercher</button>
        </form>
    </div>
</div>

<div class="flex-container step <?php echo $step == 'step2' ? 'active' : ''; ?>" id="step2">
    <div class="form-container">
        <h3>Paramétrer la table mandataire de certificat</h3>
        <?php if (!empty($error_message_certif) && isset($_POST['submit_excel_mandatairecertif'])): ?>
            <div class="message error"><?php echo $error_message_certif; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message_certif) && isset($_POST['submit_excel_mandatairecertif'])): ?>
            <div class="message success"><?php echo $success_message_certif; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file_mandatairecertif" accept=".xlsx, .xls" required>
            <button type="submit" name="submit_excel_mandatairecertif">Importer</button>
        </form>
    </div>

    <div class="form-container">
        <h3>Paramétrer la table mandataire technique</h3>
        <?php if (!empty($error_message_tech) && isset($_POST['submit_excel_mandatairetech'])): ?>
            <div class="message error"><?php echo $error_message_tech; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message_tech) && isset($_POST['submit_excel_mandatairetech'])): ?>
            <div class="message success"><?php echo $success_message_tech; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file_mandatairetech" accept=".xlsx, .xls" required>
            <button type="submit" name="submit_excel_mandatairetech">Importer</button>
        </form>
    </div>

    <div class="form-container">
        <h3>Paramétrer la table représentant légal</h3>
        <?php if (!empty($error_message_legal) && isset($_POST['submit_excel_representantlegal'])): ?>
            <div class="message error"><?php echo $error_message_legal; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message_legal) && isset($_POST['submit_excel_representantlegal'])): ?>
            <div class="message success"><?php echo $success_message_legal; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file_representantlegal" accept=".xlsx, .xls" required>
            <button type="submit" name="submit_excel_representantlegal">Importer</button>
        </form>
    </div>

    <div class="search-container">
        <h3>Recherche par CIN</h3>
        <form method="post" action="search_result2.php">
            <input type="text" name="search_cin2" placeholder="Entrez le CIN">
            <button type="submit">Rechercher</button>
        </form>
    </div>
</div>

 <!-- Button to navigate to parametrage2.php -->
 <div style="text-align: center; margin-top: 20px;">
        <a href="parametrage2.php" class="next-page-button">Suivant</a>
    </div>
<script>
function showSection(section) {
    var steps = document.querySelectorAll('.step');
    steps.forEach(function (step) {
        step.classList.remove('active');
    });
    document.getElementById(section).classList.add('active');
}
</script>