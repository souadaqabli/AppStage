<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'utilisateur') {
    header("Location: ../login.php");
    exit();
}

include '../connect.php';

// Vérifier si les paramètres GET sont présents
if (!isset($_GET['cin']) || !isset($_GET['date_demande'])) {
    header("Location: dashboard.php");
    exit();
}

$cin = $_GET['cin'];
$date_demande = $_GET['date_demande'];

// Récupérer les détails du certificat depuis la base de données
$sql = "SELECT * FROM certificat WHERE cin = ? AND date_demande = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $cin, $date_demande);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Redirection si le certificat n'est pas trouvé
    header("Location: dashboard.php");
    exit();
}

$certificat = $result->fetch_assoc();

// Traitement du formulaire pour mettre à jour les dates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_production = $_POST['date_production'];
    $date_expiration = $_POST['date_expiration'];

    // Mettre à jour les dates dans la base de données
    $update_sql = "UPDATE certificat SET date_production = ?, date_expiration = ? WHERE cin = ? AND date_demande = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssss", $date_production, $date_expiration, $cin, $date_demande);

    if ($update_stmt->execute()) {
        // Redirection après la mise à jour réussie
        $_SESSION['success_message'] = "Dates de production et expiration mises à jour avec succès.";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Erreur lors de la mise à jour des dates.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajuster les Dates de Certificat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
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
        }
        .form-group button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajuster les Dates de Certificat</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?cin=' . urlencode($cin) . '&date_demande=' . urlencode($date_demande); ?>" method="POST">
            <div class="form-group">
                <label for="date_production">Date de Production:</label>
                <input type="date" id="date_production" name="date_production" value="<?php echo htmlspecialchars($certificat['date_production']); ?>" required>
            </div>
            <div class="form-group">
                <label for="date_expiration">Date d'Expiration:</label>
                <input type="date" id="date_expiration" name="date_expiration" value="<?php echo htmlspecialchars($certificat['date_expiration']); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</body>
</html>
