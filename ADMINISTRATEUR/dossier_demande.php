<?php
include '../connect.php';

// Récupérer les utilisateurs de la base de données
$sql = "SELECT cin, nom, prenom, adresse_pro FROM porteurinfo";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sélectionner un porteur</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-family: 'Calibri';
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }
        .action-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        
    </style>
</head>
<body>
    <h1>Sélectionner un porteur pour générer un PDF</h1>
    <table border="1">
        <tr>
            <th>CIN</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Adresse professionnelle</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["cin"]) . "</td>
                        <td>" . htmlspecialchars($row["nom"]) . "</td>
                        <td>" . htmlspecialchars($row["prenom"]) . "</td>
                        <td>" . htmlspecialchars($row["adresse_pro"]) . "</td>
                        <td><a href='generer_avec_temp.php?cin=" . urlencode($row["cin"]) . "' target='_blank'  class='action-button'>Générer PDF</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun utilisateur trouvé</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
