<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['porteurs']) && !empty($_POST['porteurs'])) {
        $porteurs = $_POST['porteurs'];
        
        // Rediriger vers la page de renouvellement avec les CIN sélectionnés
        $cinList = implode(",", $porteurs);
        header("Location: remplir_renouvellement.php?cinList=" . urlencode($cinList));
        exit();
    } else {
        echo "Aucun porteur sélectionné.";
    }
}
?>
