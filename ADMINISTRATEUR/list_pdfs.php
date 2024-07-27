<?php
// Chemin vers le répertoire contenant les fichiers PDF
$directory = __DIR__ . '/../pdf_storage1/';

// Vérifier si le répertoire existe
if (!is_dir($directory)) {
    die("Le répertoire des fichiers PDF n'existe pas.");
}

// Lire le contenu du répertoire
$pdf_files = array_diff(scandir($directory), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des PDFs générés</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
        }

        
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-family: 'Calibri';
            color: #333;
            
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        a {
            text-decoration: none;
            color: #4CAF50; /* Vert */
            transition: color 0.3s;
            display: flex;
            align-items: center;
        }

        a:hover {
            color: #3e8e41; /* Vert foncé */
        }

        .fa {
            margin-right: 8px;
        }

        .btn-back {
            text-align: center;
            margin-top: 20px;
            
        }

        .btn-back a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4CAF50; /* Vert */
        color: #fff;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s;
        }

        .btn-back a:hover {
            background-color: #3e8e41; /* Vert foncé */
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des PDFs générés</h1>
        <?php if (!empty($pdf_files)): ?>
            <ul>
                <?php foreach ($pdf_files as $file): ?>
                    <li>
                        <span><?php echo $file; ?></span>
                        <a href="<?php echo '../pdf_storage1/' . $file . '?t=' . time(); ?>" target="_blank">
                            <i class="fas fa-file-pdf"></i> Voir/Télécharger
                        </a>
                        <a href="delete_pdf.php?filename=<?php echo urlencode($file); ?>">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun fichier PDF trouvé.</p>
        <?php endif; ?>

        <div class="btn-back">
            <a href="dashboard1.php?section=dossier_demande">Retour</a>
        </div>
    </div>
</body>
</html>
