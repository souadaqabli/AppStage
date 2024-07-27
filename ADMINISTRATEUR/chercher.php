<?php
// Include your database connection script
require '../vendor/autoload.php';
include '../connect.php';
session_start(); // Start session to access session variables

// Initialize variables
$error_message = '';
$result_porteurinfo = $result_porteurnv = $result_certificat = null;

if (isset($_SESSION['success_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['success_message']) . '");</script>';
    // Clear the message after displaying it
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['error_message']) . '");</script>';
    // Clear the message after displaying it
    unset($_SESSION['error_message']);
}

// Display message if it exists
if (isset($_SESSION['message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['message']) . '");</script>';
    // Clear the message after displaying it
    unset($_SESSION['message']);
}

// Check if there's a CIN to search
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cherch_cin = $_POST['cherch_cin'];
    $_SESSION['cherch_cin'] = $cherch_cin; // Store the CIN in the session
} elseif (isset($_SESSION['cherch_cin'])) {
    $cherch_cin = $_SESSION['cherch_cin'];
}

// Perform the search if there's a CIN
if (!empty($cherch_cin)) {
    try {
        // Query porteurinfo
        $sql = "SELECT * FROM porteurinfo WHERE cin = ?";
        $stmt_porteurinfo = $conn->prepare($sql);
        $stmt_porteurinfo->bind_param("s", $cherch_cin);
        $stmt_porteurinfo->execute();
        $result_porteurinfo = $stmt_porteurinfo->get_result();

        // Query porteurnv
        $sql = "SELECT domaine_app, type_demande, bkam, agence_retrait, ville_retrait, adresse_poste, type_certif FROM porteurnv WHERE cin = ?";
        $stmt_porteurnv = $conn->prepare($sql);
        $stmt_porteurnv->bind_param("s", $cherch_cin);
        $stmt_porteurnv->execute();
        $result_porteurnv = $stmt_porteurnv->get_result();

        // Query certificat
        $sql = "SELECT cin, type_demande, date_production, date_depot, date_demande, date_expiration FROM certificat WHERE cin = ?";
        $stmt_certificat = $conn->prepare($sql);
        $stmt_certificat->bind_param("s", $cherch_cin);
        $stmt_certificat->execute();
        $result_certificat = $stmt_certificat->get_result();

        if (!$result_porteurinfo || !$result_porteurnv || !$result_certificat) {
            $error_message = "Erreur lors de la recherche";
        }

    } catch (Exception $e) {
        $error_message = "Erreur lors de la recherche : " . htmlspecialchars($e->getMessage());
    }

    if (isset($stmt_porteurinfo)) {
        $stmt_porteurinfo->close();
    }
    if (isset($stmt_porteurnv)) {
        $stmt_porteurnv->close();
    }
    if (isset($stmt_certificat)) {
        $stmt_certificat->close();
    }
}

// Récupérer les demandes de certificats de l'utilisateur
$cin = $_SESSION['cin']; // Assurez-vous que CIN est stocké dans la session
$sql = "SELECT * FROM certificat WHERE cin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();
$certificat_requests = $result->fetch_all(MYSQLI_ASSOC);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche par CIN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        form {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        label {
            margin-right: 10px;
        }

        input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            margin-right: 10px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin: 10px;
            width: 350px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h4 {
            color: #333;
            margin-bottom: 15px;
        }

        .card p {
            margin-bottom: 10px;
            font-size: 15px;
        }

        .card strong {
            font-weight: bold;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .btn-primary {
            color: green;
            margin-left: 40px;
        }

        .form-group {
            margin-bottom: 15px;
            width: 100%;
            max-width: 400px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input[type="date"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px; /* Ajout de marge supérieure pour espacement */
        }

        .form-group button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <h2>Résultats de recherche par CIN</h2>

    <form method="post" action="">
        <label for="cherch_cin">Rechercher par CIN:</label>
        <input type="text" id="cherch_cin" name="cherch_cin" required>
        <input type="submit" value="Rechercher">
    </form>

    <p><a href="dashboard1.php" class="btn btn-primary">Retour</a></p>

    <?php if ($error_message): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <div class="container">
        <?php if ($result_porteurinfo && $result_porteurinfo->num_rows > 0): ?>
            <div class="card">
                <h4>Informations personnelles du porteur</h4>
                <?php while ($row = $result_porteurinfo->fetch_assoc()): ?>
                    <p><strong>CIN:</strong> <?php echo htmlspecialchars($row['cin']); ?></p>
                    <p><strong>Civilité:</strong> <?php echo htmlspecialchars($row['civilite']); ?></p>
                    <p><strong>Nom:</strong> <?php echo htmlspecialchars($row['nom']); ?></p>
                    <p><strong>Prénom:</strong> <?php echo htmlspecialchars($row['prenom']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Fonction:</strong> <?php echo htmlspecialchars($row['fonction']); ?></p>
                    <p><strong>GSM:</strong> <?php echo htmlspecialchars($row['gsm']); ?></p>
                    <p><strong>Bureau:</strong> <?php echo htmlspecialchars($row['bureau']); ?></p>
                    <p><strong>Fax:</strong> <?php echo htmlspecialchars($row['fax']); ?></p>
                    <p><strong>Adresse professionnelle:</strong> <?php echo htmlspecialchars($row['adresse_pro']); ?></p>
                    <p><strong>Ville:</strong> <?php echo htmlspecialchars($row['ville']); ?></p>
                    <p><strong>Pays:</strong> <?php echo htmlspecialchars($row['pays']); ?></p>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <?php if ($result_porteurnv && $result_porteurnv->num_rows > 0): ?>
            <div class="card">
                <h4>Informations supplémentaires</h4>
                <?php while ($row = $result_porteurnv->fetch_assoc()): ?>
                    <p><strong>Agence de retrait:</strong> <?php echo htmlspecialchars($row['agence_retrait']); ?></p>
                    <p><strong>Ville de retrait:</strong> <?php echo htmlspecialchars($row['ville_retrait']); ?></p>
                    <p><strong>Adresse poste Maroc:</strong> <?php echo htmlspecialchars($row['adresse_poste']); ?></p>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <?php if ($result_certificat && $result_certificat->num_rows > 0): ?>
    <div class="card">
        <h4>Demande de Certificat</h4>
        <?php while ($row = $result_certificat->fetch_assoc()): ?>
            <p><strong>Type de demande:</strong> <?php echo htmlspecialchars($row['type_demande']); ?></p>
            <p><strong>Date de demande:</strong> <?php echo htmlspecialchars($row['date_demande']); ?></p>
            <p><strong>Date de dépôt:</strong> 
                <?php 
                if (is_null($row['date_depot'])) {
                    echo 'NULL';
                } else {
                    echo htmlspecialchars($row['date_depot']);
                }
                ?>
            </p>
            <p><strong>Date de production:</strong> <?php echo htmlspecialchars($row['date_production']); ?></p>
            <p><strong>Date d'expiration:</strong> <?php echo htmlspecialchars($row['date_expiration']); ?></p>

           

            <!-- Display the link to adjust dates -->
            <a href="update_date_depot.php?cin=<?php echo isset($row['cin']) ? htmlspecialchars($row['cin']) : 'NULL'; ?>&date_demande=<?php echo htmlspecialchars($row['date_demande']); ?>" class="adjust-dates-button">Ajuster la Date de Dépot</a>
        <?php endwhile; ?>

    </div>
<?php else: ?>
    <p>Aucun certificat trouvé pour ce CIN.</p>
<?php endif; ?>

        

    </div>

    <?php if ($error_message): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

</body>
</html>
