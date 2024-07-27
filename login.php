<?php
session_start();
include 'connect.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier d'abord dans la table des utilisateurs
    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Authentification réussie pour un utilisateur
            $_SESSION['cin'] = $user['cin']; // Assurez-vous que 'cin' correspond au nom de la colonne dans votre table
            $_SESSION['role'] = 'utilisateur';

            // Vérifier les certificats proches de l'expiration
            checkCertificatExpiration($conn);

            header("Location: user/dashboard.php");
            exit();
        } else {
            $error_message = "Identifiants incorrects.";
        }
    } else {
        // Si l'utilisateur n'est pas trouvé, vérifier dans la table des administrateurs
        $sql = "SELECT * FROM administrateur WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                // Authentification réussie pour un administrateur
                $_SESSION['cin'] = $admin['cin'];
                $_SESSION['role'] = 'admin';

                // Vérifier les certificats proches de l'expiration
                checkCertificatExpiration($conn);

                header("Location: ADMINISTRATEUR/dashboard1.php");
                exit();
            } else {
                $error_message = "Identifiants incorrects.";
            }
        } else {
            $error_message = "Utilisateur ou administrateur non trouvé.";
        }
    }
    $stmt->close();
    $conn->close();
}

function checkCertificatExpiration($conn) {
    // Date limite pour les notifications (40 jours avant l'expiration)
    $limit_date = date('Y-m-d', strtotime('+40 days'));

    // Trouver les certificats proches de l'expiration
    $stmt = $conn->prepare("SELECT cin, date_expiration FROM certificat WHERE date_expiration <= ?");
    $stmt->bind_param("s", $limit_date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cin = $row['cin'];
        $expiration_date = $row['date_expiration'];

        // Créer un message de notification
        $message = "Votre certificat (CIN: $cin) expire le $expiration_date. Veuillez renouveler votre certificat.";

        // Vérifier si une notification existe déjà pour ce CIN et message
        $stmt_check = $conn->prepare("SELECT * FROM notifications_utilisateur WHERE cin = ? AND message = ?");
        $stmt_check->bind_param("ss", $cin, $message);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows === 0) {
            // Ajouter la notification si elle n'existe pas déjà
            $stmt_insert = $conn->prepare("INSERT INTO notifications_utilisateur (cin, message, notification_time, read_status) VALUES (?, ?, NOW(), 0)");
            $stmt_insert->bind_param("ss", $cin, $message);
            $stmt_insert->execute();
        }
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
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
        input[type="email"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            Email: <input type="email" name="email" required><br>
            Mot de passe(Votre cin): <input type="password" name="password" required><br>
            <input type="submit" value="Connexion">
        </form>
        <p>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
    </div>
</body>
</html>
