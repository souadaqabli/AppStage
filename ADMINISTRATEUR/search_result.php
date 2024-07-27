<?php
session_start();

$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['error_message'], $_SESSION['success_message']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_cin'])) {
    $search_cin = $_POST['search_cin'];

    try {
        require '../vendor/autoload.php';
        include '../connect.php';

        // Query pour récupérer les données basées sur le CIN
        $sql = "SELECT * FROM porteurinfo WHERE cin = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_cin);
        $stmt->execute();
        $result = $stmt->get_result();

        // Afficher les résultats s'ils sont trouvés
        if ($result && $result->num_rows > 0) {
            $search_result = $result->fetch_assoc();
            // Afficher les données récupérées, vous pouvez les formater selon vos besoins
            $nom = $search_result['nom'];
            $prenom = $search_result['prenom'];
            $civilite = $search_result['civilite'];
            $email = $search_result['email'];
            $fonction = $search_result['fonction'];
            $gsm = $search_result['gsm'];
            $bureau = $search_result['bureau'];
            $fax = $search_result['fax'];
            $adresse_pro = $search_result['adresse_pro'];
            $ville = $search_result['ville'];
            $pays = $search_result['pays'];
        } else {
            $error_message = "Aucun résultat trouvé pour CIN: $search_cin";
        }

        //$stmt->close();
        //$conn->close();
        $sql_adr = "SELECT  * FROM adresse_professionnelle ";
        $result = $conn->query($sql_adr);

        $adresseprofessionnelle = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $adresseprofessionnelle[] = $row;
            }
        } else {
            die("Aucune adresse trouvée.");
        }

    } catch (Exception $e) {
        $error_message = "Erreur lors de la recherche : " . $e->getMessage();
    }
} elseif (isset($_GET['cin'])) {
    $search_cin = $_GET['cin'];

    try {
        require '../vendor/autoload.php';
        include '../connect.php';

        // Query pour récupérer les données basées sur le CIN
        $sql = "SELECT * FROM porteurinfo WHERE cin = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_cin);
        $stmt->execute();
        $result = $stmt->get_result();

        // Afficher les résultats s'ils sont trouvés
        if ($result && $result->num_rows > 0) {
            $search_result = $result->fetch_assoc();
            // Afficher les données récupérées, vous pouvez les formater selon vos besoins
            $nom = $search_result['nom'];
            $prenom = $search_result['prenom'];
            $civilite = $search_result['civilite'];
            $email = $search_result['email'];
            $fonction = $search_result['fonction'];
            $gsm = $search_result['gsm'];
            $bureau = $search_result['bureau'];
            $fax = $search_result['fax'];
            $adresse_pro = $search_result['adresse_pro'];
            $ville = $search_result['ville'];
            $pays = $search_result['pays'];
        } else {
            $error_message = "Aucun résultat trouvé pour CIN: $search_cin";
        }
        
        //Préparez une requête pour récupérer les détails des porteurs sélectionnés
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        $error_message = "Erreur lors de la recherche : " . $e->getMessage();
    }
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de la recherche</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .card h3 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type=text], .form-group input[type=email] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group input[type=submit], .form-group .btn-back {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group .btn-back {
            background-color: #4CAF50;
            margin-right: 10px;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php elseif (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($search_result)): ?>
            <div class="card">
                <form method="post" action="save_changes1.php">
                    <h3>Informations du Porteur</h3>
                    <input type="hidden" name="cin" value="<?php echo $search_result['cin']; ?>">
                    
                    <fieldset>
                        <legend>Informations Personnelles</legend>
                        <div class="form-group">
                            <label>Nom:</label>
                            <input type="text" name="nom" value="<?php echo $nom; ?>">
                        </div>
                        <div class="form-group">
                            <label>Prénom:</label>
                            <input type="text" name="prenom" value="<?php echo $prenom; ?>">
                        </div>
                        <div class="form-group">
                            <label>Civilité:</label>
                            <input type="text" name="civilite" value="<?php echo $civilite; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" value="<?php echo $email; ?>">
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Informations Professionnelles</legend>
                        <div class="form-group">
                            <label>Fonction:</label>
                            <input type="text" name="fonction" value="<?php echo $fonction; ?>">
                        </div>
                        <div class="form-group">
                            <label>GSM:</label>
                            <input type="text" name="gsm" value="<?php echo $gsm; ?>">
                        </div>
                        <div class="form-group">
                            <label>Bureau:</label>
                            <input type="text" name="bureau" value="<?php echo $bureau; ?>">
                        </div>
                        <div class="form-group">
                            <label>Fax:</label>
                            <input type="text" name="fax" value="<?php echo $fax; ?>">
                        </div>
                        <div class="form-group">
                            <label for="adresse_pro">Adresse professionnelle :</label>
                            <select id="adresse_pro" name="adresse_pro" style="padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;" required>
                                <?php foreach ($adresseprofessionnelle as $adressepro): ?>
                                    <option value="<?php echo $adressepro['adresse_pro']; ?>" <?php echo ($adressepro['adresse_pro'] == $adresse_pro) ? 'selected' : ''; ?>>
                                        <?php echo $adressepro['adresse_pro']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ville:</label>
                            <input type="text" name="ville" value="<?php echo $ville; ?>">
                        </div>
                        <div class="form-group">
                            <label>Pays:</label>
                            <input type="text" name="pays" value="<?php echo $pays; ?>">
                        </div>
                    </fieldset>

                    <div class="form-group">
                        <button type="button" class="btn-back" onclick="window.location.href='dashboard1.php?section=parametrage'">Retour</button>
                        <input type="submit" name="save_changes" value="Sauvegarder">
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
