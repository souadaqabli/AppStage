<?php
include '../connect.php';

// Récupérer les porteurs
$sql = "SELECT cin, nom, prenom, adresse_pro FROM porteurinfo";
$result = $conn->query($sql);

$porteurs = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $porteurs[] = $row;
    }
} else {
    echo "0 résultats";
}
$conn->close();


?>
<!DOCTYPE html>
<html>
<head>
    <title style="font-family: Open Sans;">Sélectionner les porteurs pour enregistrement initial</title>
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

        /* Style pour le bouton "Soumettre" */
        input[type="submit"] {
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
        input[type="submit"]:hover {
            background-color: #45a049; /* Vert plus foncé */
        }
    </style>
</head>
<body>
    <h1>Sélectionner les porteurs pour enregistrement initial</h1>
    <form method="post" action="enregistrement.php">
        <table border="1">
            <tr>
                <th>Choisir</th>
                <th>CIN</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Adresse professionnelle</th>
            </tr>
            <?php foreach($porteurs as $porteur): ?>
            <tr>
                <td><input type="checkbox" name="porteurs[]" value="<?php echo $porteur['cin']; ?>"></td>
                <td><?php echo $porteur['cin']; ?></td>
                <td><?php echo $porteur['nom']; ?></td>
                <td><?php echo $porteur['prenom']; ?></td>
                <td><?php echo $porteur['adresse_pro']; ?></td>

            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <input type="submit" value="Soumettre">
    </form>
</body>
</html>
