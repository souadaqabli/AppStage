<?php
// ADMINISTRATEUR/delete_pdf.php

function deleteFile($filename = null) { // Add default value to $filename
    if ($filename === null) {
        return "Paramètre de fichier manquant.";
    }

    $filepath = __DIR__ . '/../pdf_storage1/' . $filename;

    // Vérifier si le fichier existe
    if (file_exists($filepath)) {
        // Supprimer le fichier
        if (unlink($filepath)) {
            return "Le fichier $filename a été supprimé.";
        } else {
            return "Erreur lors de la suppression du fichier $filename.";
        }
    } else {
        return "Le fichier $filename n'existe pas.";
    }
}

// Vérifier si le nom de fichier est passé en tant que paramètre

if (isset($_GET['filename']) && !empty($_GET['filename'])) {

    $filename = urldecode($_GET['filename']);
    echo deleteFile($filename);
} else {
    echo "Paramètre de fichier manquant.";
}

?>