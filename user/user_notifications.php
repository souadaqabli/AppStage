<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header("Location: ../login.php");
    exit();
}
include "../connect.php";

// Récupérer le CIN de l'utilisateur connecté
$cin = $_SESSION['cin'];

// Récupérer les notifications pour cet utilisateur
$stmt_user_notifications = $conn->prepare("SELECT * FROM notifications_utilisateur WHERE cin = ? ORDER BY notification_time DESC");
$stmt_user_notifications->bind_param("s", $cin);
$stmt_user_notifications->execute();
$result_user_notifications = $stmt_user_notifications->get_result();

// Préparation du HTML pour afficher les notifications
$notifications_html = '';

// Vérifier s'il y a des notifications
if ($result_user_notifications->num_rows > 0) {
    // Parcourir et afficher les notifications
    while ($row = $result_user_notifications->fetch_assoc()) {
        $notifications_html .= "
            <div class='notification' data-id='{$row['id']}'>
                {$row['message']}
                <span class='delete-icon' onclick='deleteNotification({$row['id']})'>&#128465;</span>
            </div>";
    }
} else {
    $notifications_html = "<p class='no-notifications'>Aucune notification!</p>";
}

// Fermer la connexion à la base de données
$stmt_user_notifications->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      /* Style CSS pour les notifications */
      .notification {
        background-color: #fff;
        padding: 15px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-family: 'calibri';
        font-size: 16px;
        color: #333;
        position: relative;
      }

      .delete-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: red;
      }

      .notification:hover {
        background-color: #f7f7f7;
        cursor: pointer;
      }

      h2 {
        font-family: 'calibri';
        text-align: center;
        color: #333;
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: bold;
      }

      .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      .notifications {
        padding: 20px;
      }

      .no-notifications {
        font-size: 18px;
        color: #666;
        text-align: center;
        padding: 20px;
      }
      .return-button {
        display: block;
        width: 200px;
        margin: 20px auto;
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-family: 'calibri';
        font-size: 18px;
        transition: background-color 0.3s;
      }

      .return-button:hover {
        background-color: #28a745;
      }
    </style>
</head>
<body>
    <div class="container">
        <h2>Notifications Utilisateur</h2>
        <div class="notifications">
            <?php echo $notifications_html; ?>
        </div>
    </div>
    <!-- Ajout du bouton de retour -->
    <a href="dashboard.php" class="return-button">Retour</a>
    <script>
      function deleteNotification(notificationId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_notification.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send("id=" + notificationId);
        }
      }
</script>

</body>
</html>
