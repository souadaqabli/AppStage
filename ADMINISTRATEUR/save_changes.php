<?php
// Include your database connection script
require '../vendor/autoload.php';
include '../connect.php';
session_start(); // Start session to use session variables

// Function to update data
function updateData($conn, $table, $data) {
    $fields = '';
    $values = [];
    $update_params = '';

    foreach ($data as $key => $value) {
        if ($key !== 'cin') { // Exclude 'cin' from the fields to update
            $fields .= "`$key` = ?, ";
            $values[] = $value;
        }
    }

    // Add 'cin' as the last value in $values array
    $values[] = $data['cin'];

    $fields = rtrim($fields, ', ');
    $sql = "UPDATE $table SET $fields WHERE cin = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically based on the number of fields
    $types = str_repeat('s', count($values)); // 'sss...' for string parameters
    $stmt->bind_param($types, ...$values);

    $success = $stmt->execute();
    $stmt->close();

    return $success ? [$table] : false;
}

// Get data from the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $cin = $_POST['cin'];

    // Prepare data array for update
    $data = [
        'civilite' => $_POST['civilite'],
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'email' => $_POST['email'],
        'fonction' => $_POST['fonction'],
        'gsm' => $_POST['gsm'],
        'bureau' => $_POST['bureau'],
        'fax' => $_POST['fax'],
        'adresse_pro' => $_POST['adresse_pro'],
        'ville' => $_POST['ville'],
        'pays' => $_POST['pays'],
        'cin' => $cin, // Ensure to include the CIN for WHERE clause
    ];

    // Update data in the database
    $updatedTables = updateData($conn, $table, $data);

    // Check if update was successful
    if ($updatedTables !== false) {
        // Construct success message
        $_SESSION['message'] = "Les informations ont été mises à jour avec succès dans la table : " . implode(', ', $updatedTables);
    } else {
        // Construct error message
        $_SESSION['message'] = "Erreur lors de la mise à jour des informations.";
    }
}

// Close database connection
$conn->close();

// Redirect back to the search results page after saving changes
header("Location: search_result2.php");
exit();
?>
