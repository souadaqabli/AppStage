<?php
// Include your database connection script
require '../vendor/autoload.php';
include '../connect.php';
session_start(); // Start session to access session variables

// Initialize variables
$error_message = '';
$result_certif = $result_tech = $result_legal = null;

// Display message if it exists
if (isset($_SESSION['message'])) {
    echo '<script>alert("' . $_SESSION['message'] . '");</script>';
    // Clear the message after displaying it
    unset($_SESSION['message']);
}

// Check if there's a CIN to search
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_cin = $_POST['search_cin2'];
    $_SESSION['search_cin2'] = $search_cin; // Store the CIN in the session
} elseif (isset($_SESSION['search_cin2'])) {
    $search_cin = $_SESSION['search_cin2'];
}

// Perform the search if there's a CIN
if (!empty($search_cin)) {
    try {
        // Query mandatairecertif
        $sql = "SELECT * FROM mandatairecertif WHERE cin = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_cin);
        $stmt->execute();
        $result_certif = $stmt->get_result();

        // Query mandatairetech
        $sql = "SELECT * FROM mandatairetech WHERE cin = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_cin);
        $stmt->execute();
        $result_tech = $stmt->get_result();

        // Query representantlegal
        $sql = "SELECT * FROM representantlegal WHERE cin = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_cin);
        $stmt->execute();
        $result_legal = $stmt->get_result();


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

    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche par CIN</title>
    <style>
        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
            background-color: #f9f9f9;
        }
        .card input[type=text], .card input[type=email] {
            width: 90%;
            padding: 5px;
            margin-bottom: 10px;
        }
        .card input[type=submit], .card .btn-back {
            background-color: #4CAF50;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            margin-right: 10px; /* Space between buttons */
        }
        .btn-back {
            background-color: #4CAF50;
            margin-right: 10px;
        }
        
    </style>
</head>
<body>
    <h2>Résultats de recherche par CIN</h2>

    <?php if ($result_certif && $result_certif->num_rows > 0): ?>
        <?php while ($row = $result_certif->fetch_assoc()): ?>
            <div class="card">
                <form action="save_changes.php" method="post">
                    <h3>Mandataire de certificat</h3>
                    <input type="hidden" name="table" value="mandatairecertif">
                    <input type="hidden" name="cin" value="<?php echo $row['cin']; ?>">
                    <label>CIN: <?php echo $row['cin']; ?></label><br>
                    <label>Civilité: </label>
                    <input type="text" name="civilite" value="<?php echo $row['civilite']; ?>"><br>
                    <label>Nom: </label>
                    <input type="text" name="nom" value="<?php echo $row['nom']; ?>"><br>
                    <label>Prénom: </label>
                    <input type="text" name="prenom" value="<?php echo $row['prenom']; ?>"><br>
                    <label>Email: </label>
                    <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
                    <label>Fonction: </label>
                    <input type="text" name="fonction" value="<?php echo $row['fonction']; ?>"><br>
                    <label>GSM: </label>
                    <input type="text" name="gsm" value="<?php echo $row['gsm']; ?>"><br>
                    <label>Bureau: </label>
                    <input type="text" name="bureau" value="<?php echo $row['bureau']; ?>"><br>
                    <label>Fax: </label>
                    <input type="text" name="fax" value="<?php echo $row['fax']; ?>"><br>
                    <div class="form-group">
                        <label for="adresse_pro">Adresse professionnelle :</label>
                            <select id="adresse_pro" name="adresse_pro" style="padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;" required>
                                <?php foreach ($adresseprofessionnelle as $adressepro): ?>
                                        <option value="<?php echo $adressepro['adresse_pro']; ?>" <?php echo ($adressepro['adresse_pro'] == $row['adresse_pro']) ? 'selected' : ''; ?>>
                                            <?php echo $adressepro['adresse_pro']; ?>
                                        </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                    <label>Ville: </label>
                    <input type="text" name="ville" value="<?php echo $row['ville']; ?>"><br>
                    <label>Pays: </label>
                    <input type="text" name="pays" value="<?php echo $row['pays']; ?>"><br>
                    
                    <div class="btn-group">
                        <input type="submit" value="Sauvegarder">
                        <button type="button" class="btn-back" onclick="window.location.href='dashboard1.php?section=parametrage'">Retour</button>
                    </div>
                </form>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <?php if ($result_tech && $result_tech->num_rows > 0): ?>
        <?php while ($row = $result_tech->fetch_assoc()): ?>
            <div class="card">
                <form action="save_changes.php" method="post">
                    <h3>Mandataire technique</h3>
                    <input type="hidden" name="table" value="mandatairetech">
                    <input type="hidden" name="cin" value="<?php echo $row['cin']; ?>">
                    <label>CIN: <?php echo $row['cin']; ?></label><br>
                    <label>Civilité: </label>
                    <input type="text" name="civilite" value="<?php echo $row['civilite']; ?>"><br>
                    <label>Nom: </label>
                    <input type="text" name="nom" value="<?php echo $row['nom']; ?>"><br>
                    <label>Prénom: </label>
                    <input type="text" name="prenom" value="<?php echo $row['prenom']; ?>"><br>
                    <label>Email: </label>
                    <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
                    <label>Fonction: </label>
                    <input type="text" name="fonction" value="<?php echo $row['fonction']; ?>"><br>
                    <label>GSM: </label>
                    <input type="text" name="gsm" value="<?php echo $row['gsm']; ?>"><br>
                    <label>Bureau: </label>
                    <input type="text" name="bureau" value="<?php echo $row['bureau']; ?>"><br>
                    <label>Fax: </label>
                    <input type="text" name="fax" value="<?php echo $row['fax']; ?>"><br>
                    <div class="form-group">
                        <label for="adresse_pro">Adresse professionnelle :</label>
                            <select id="adresse_pro" name="adresse_pro" style="padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;" required>
                                <?php foreach ($adresseprofessionnelle as $adressepro): ?>
                                        <option value="<?php echo $adressepro['adresse_pro']; ?>" <?php echo ($adressepro['adresse_pro'] == $row['adresse_pro']) ? 'selected' : ''; ?>>
                                            <?php echo $adressepro['adresse_pro']; ?>
                                        </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                    <label>Ville: </label>
                    <input type="text" name="ville" value="<?php echo $row['ville']; ?>"><br>
                    <label>Pays: </label>
                    <input type="text" name="pays" value="<?php echo $row['pays']; ?>"><br>
                    
                    <input type="submit" value="Sauvegarder">
                    <button type="button" class="btn-back" onclick="window.location.href='dashboard1.php?section=parametrage'">Retour</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <?php if ($result_legal && $result_legal->num_rows > 0): ?>
        <?php while ($row = $result_legal->fetch_assoc()): ?>
            <div class="card">
                <form action="save_changes.php" method="post">
                    <h3>Représentant légal</h3>
                    <input type="hidden" name="table" value="representantlegal">
                    <input type="hidden" name="cin" value="<?php echo $row['cin']; ?>">
                    <label>CIN: <?php echo $row['cin']; ?></label>
                    <br>
                    <label>Civilité: </label>
                    <input type="text" name="civilite" value="<?php echo $row['civilite']; ?>"><br>
                    <label>Nom: </label>
                    <input type="text" name="nom" value="<?php echo $row['nom']; ?>"><br>
                    <label>Prénom: </label>
                    <input type="text" name="prenom" value="<?php echo $row['prenom']; ?>"><br>
                    <label>Email: </label>
                    <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
                    <label>Fonction: </label>
                    <input type="text" name="fonction" value="<?php echo $row['fonction']; ?>"><br>
                    <label>GSM: </label>
                    <input type="text" name="gsm" value="<?php echo $row['gsm']; ?>"><br>
                    <label>Bureau: </label>
                    <input type="text" name="bureau" value="<?php echo $row['bureau']; ?>"><br>
                    <label>Fax: </label>
                    <input type="text" name="fax" value="<?php echo $row['fax']; ?>"><br>
                    <div class="form-group">
                        <label for="adresse_pro">Adresse professionnelle :</label>
                            <select id="adresse_pro" name="adresse_pro" style="padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;" required>
                                <?php foreach ($adresseprofessionnelle as $adressepro): ?>
                                        <option value="<?php echo $adressepro['adresse_pro']; ?>" <?php echo ($adressepro['adresse_pro'] == $row['adresse_pro']) ? 'selected' : ''; ?>>
                                            <?php echo $adressepro['adresse_pro']; ?>
                                        </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                    <label>Ville: </label>
                    <input type="text" name="ville" value="<?php echo $row['ville']; ?>"><br>
                    <label>Pays: </label>
                    <input type="text" name="pays" value="<?php echo $row['pays']; ?>"><br>
                    <input type="submit" value="Sauvegarder">
                </form>
                
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <?php if (!$result_certif && !$result_tech && !$result_legal): ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
    
</body>
</html>
