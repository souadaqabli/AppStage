<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../connect.php';

// Mark notifications as read if 'mark_as_read' is set
if (isset($_GET['mark_as_read'])) {
    $notification_id = $_GET['mark_as_read'];
    $sql_update_read = "UPDATE admin_notifications SET read_status = 1 WHERE id = ?";
    $stmt_update_read = $conn->prepare($sql_update_read);
    $stmt_update_read->bind_param("i", $notification_id);
    $stmt_update_read->execute();
    header("Location: notifications_admin.php"); // Redirect to the same page
    exit();
}

// Delete notification if 'delete' is set
if (isset($_GET['delete'])) {
    $notification_id = $_GET['delete'];
    $sql_delete = "DELETE FROM admin_notifications WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $notification_id);
    $stmt_delete->execute();
    header("Location: notifications_admin.php"); // Redirect to the same page
    exit();
}

// Retrieve all notifications
$sql = "SELECT n.*, u.prenom, u.nom FROM admin_notifications n JOIN porteurnv u ON n.cin = u.cin ORDER BY n.notification_time DESC";
$result = $conn->query($sql);
$notifications = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications Administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .notification {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notification:last-child {
            border-bottom: none;
        }
        .notification.unread {
            background-color: #f9f9f9;
        }
        .notification p {
            margin: 5px 0;
        }
        .notification time {
            font-size: 0.9em;
            color: #888;
        }
        .mark-read, .delete {
            color: blue;
            cursor: pointer;
            margin-left: 10px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .mark-read:hover {
            background-color: #e0e0e0;
        }
        .delete {
            color: white;
            background-color: red;
        }
        .delete:hover {
            background-color: darkred;
        }
        .actions {
            display: flex;
            align-items: center;
        }
        .back-button {
            display: block;
            margin: 0 auto 20px auto;
            text-align: center;
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard1.php" class="back-button">Retour au Dashboard</a>
        <h1>Notifications Administrateur</h1>
        <?php foreach ($notifications as $notif): ?>
            <div class="notification <?php if ($notif['read_status'] == 0) { echo 'unread'; } ?>">
                <div>
                    <p><strong>Utilisateur:</strong> <?php echo htmlspecialchars($notif['prenom'] . ' ' . $notif['nom']); ?></p>
                    <?php
                    // Décoder le JSON pour accéder aux détails des changements
                    $change_details = json_decode($notif['change_details'], true);
                    
                    // Afficher seulement les clés qui ont été modifiées
                    foreach ($change_details as $key => $value) {
                        echo "<p><strong>$key:</strong> $value</p>";
                    }
                    ?>
                    <time><?php echo $notif['notification_time']; ?></time>
                </div>
                <div class="actions">
                    <?php if ($notif['read_status'] == 0) { ?>
                        <a class="mark-read" href="notifications_admin.php?mark_as_read=<?php echo $notif['id']; ?>">Marquer comme lu</a>
                    <?php } ?>
                    <a class="delete" href="notifications_admin.php?delete=<?php echo $notif['id']; ?>">Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
