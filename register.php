<?php
include 'connect.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cin = $_POST['cin'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if ($role == 'admin') {
        $sql = "INSERT INTO administrateur (cin, nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    } else {
        $sql = "INSERT INTO utilisateurs (cin, nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $cin, $nom, $prenom, $email, $password_hash, $role);
        if ($stmt->execute()) {
            echo '<div class="success">Inscription réussie. Vous pouvez maintenant <a href="login.php">vous connecter</a>.</div>';
        } else {
            $error_message = "Erreur lors de l'inscription: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_message = "Erreur de préparation de la requête: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        body {
            font-family: Calibri;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: calc(100% - 22px);
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        p {
            margin-top: 20px;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Inscription</h2>
        <?php
        if (!empty($error_message)) {
            echo '<div class="error">' . $error_message . '</div>';
        }
        ?>
        <form method="POST" action="register.php">
            CIN: <input type="text" name="cin" required><br>
            Nom: <input type="text" name="nom" required><br>
            Prénom: <input type="text" name="prenom" required><br>
            Email: <input type="email" name="email" required><br>
            Mot de passe(Votre cin): <input type="password" name="password" required><br>
            Rôle: 
            <select name="role">
                <option value="utilisateur">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select><br>
            <input type="submit" value="S'inscrire">
        </form>
    </div>
</body>
</html>
