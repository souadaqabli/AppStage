<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header("Location: ../login.php");
    exit();
}

include '../connect.php';

// Récupérer les demandes de certificats de l'utilisateur
$cin = $_SESSION['cin']; // Assurez-vous que CIN est stocké dans la session
$sql = "SELECT * FROM certificat WHERE cin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();
$certificat_requests = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Demandes de Certificats</title>
    <style>
        
        body {
            font-family: sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 20px auto;
            padding: 0 20px;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .card {
            flex: 0 1 calc(80% - 20px); /* Utilisation de 80% de la largeur avec un espace entre les cartes */
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 20px;
        }
        .card p {
            margin: 10px 0;
            color: #555;
        }
        h1 {
            font-family:'calibri';
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .no-requests {
            text-align: center;
            width: 100%;
            margin-top: 20px;
            color: #888;
        }
        .back-button {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .back-button a {
            text-decoration: none;
            color: #fff;
            background-color: #28a745;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        .back-button a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mes Demandes de Certificats</h1>
        <div class="back-button">
            <a href="dashboard.php">Retour au Tableau de Bord</a>
        </div>
        
        <div class="card-container">
            <?php if (!empty($certificat_requests)): ?>
                <?php foreach ($certificat_requests as $request): ?>
                    <div class="card">
                        <p><strong>CIN:</strong> <?php echo htmlspecialchars($request['cin']); ?></p>
                        <p><strong>Nom:</strong> <?php echo htmlspecialchars($request['nom']); ?></p>
                        <p><strong>Prénom:</strong> <?php echo htmlspecialchars($request['prenom']); ?></p>
                        <p><strong>Adresse professionnelle:</strong> <?php echo htmlspecialchars($request['adresse_pro']); ?></p>
                        <p><strong>Type de demande:</strong> <?php echo htmlspecialchars($request['type_demande']); ?></p>
                        <p><strong>Date demande:</strong> <?php echo htmlspecialchars($request['date_demande']); ?></p>
                        <p><strong>Date dépôt:</strong> 
                            <?php
                            if ($request['date_depot'] === NULL) {
                                echo "Pas encore disponible";
                            } else {
                                echo htmlspecialchars($request['date_depot']);
                            }
                            ?>
                            </p>
                            <p><strong>Date production:</strong> 
                                <?php
                                if ($request['date_production'] === NULL) {
                                    echo "Pas encore disponible";
                                } else {
                                    echo htmlspecialchars($request['date_production']);
                                }
                                ?>
                                </p>
                                <p><strong>Date expiration:</strong> 
                                    <?php
                                    if ($request['date_expiration'] === NULL) {
                                        echo "Pas encore disponible";
                                    } else {
                                        echo htmlspecialchars($request['date_expiration']);
                                    }
                                    ?>
                                    </p>
                        <!-- Ajoutez d'autres détails ici selon vos besoins -->
                         <!-- Ajout du bouton pour ajuster les dates -->
                         <a href="adjust_dates.php?cin=<?php echo htmlspecialchars($request['cin']); ?>&date_demande=<?php echo htmlspecialchars($request['date_demande']); ?>" class="adjust-dates-button">Ajuster les Dates</a>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-requests">Aucune demande de certificat trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
