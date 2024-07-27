<?php
require_once('../vendor/autoload.php');
include '../connect.php';

use setasign\Fpdi\Tcpdf\Fpdi;

if (!isset($_GET['cin']) || empty($_GET['cin'])) {
    die("cin utilisateur non fourni.");
}

// Récupérer les données de l'utilisateur de manière sécurisée
$cin = $_GET['cin'];

// Préparer la requête SQL pour récupérer les informations de l'utilisateur
$sql = "SELECT * FROM porteurinfo WHERE cin =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Utilisateur non trouvé.");
}

$sql = "SELECT * FROM porteurnv WHERE cin =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_nv = $result->fetch_assoc();
} else {
    die("Utilisateur non trouvé.");
}

$sql = "SELECT * FROM reponses WHERE cin =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_rep = $result->fetch_assoc();
} else {
    die("Utilisateur non trouvé.");
}

$sql = "SELECT * FROM mandatairecertif ";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_man = $result->fetch_assoc();
} else {
    die("Utilisateur non trouvé.");
}

$sql = "SELECT * FROM certificat WHERE cin =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_cert = $result->fetch_assoc();
} else {
    die("Utilisateur non trouvé.");
}




$pdf = new Fpdi();

// Chargez le template PDF existant
$template = 'C:/Users/hp/Documents/template.pdf';
$pdf->setSourceFile($template);

// Importez la première page du template
$tplId = $pdf->importPage(1);

// Ajoutez une nouvelle page
$pdf->AddPage();

// Utilisez le template comme fond de page
$pdf->useTemplate($tplId, 10, 10, 200);

// Définir la police
$pdf->SetFont('helvetica', '', 10);

// Ajouter des informations personnalisées à partir de la base de données
$pdf->SetXY(48, 67);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['civilite']));

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(48, 72);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['prenom']));

$pdf->SetXY(48, 78);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['nom']));

$pdf->SetXY(48, 84);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['cin']));

$pdf->SetXY(52, 89);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['email']));

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(85, 95);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['email']));

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(46, 98.4);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['fonction']));

$pdf->SetXY(82, 103);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['gsm']));

$pdf->SetXY(42, 103);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['gsm']));

$pdf->SetXY(146, 103);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['fax']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(110, 115);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['type_demande']));

$pdf->SetXY(114, 128);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['domaine_app']));

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(135, 132);
$pdf->Cell(0, 10, is_null($user_nv['bkam']) ? 'NULL' : iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['bkam']));

$pdf->SetFont('helvetica', '', 6.4);
$pdf->SetXY(56, 145);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['adresse_pro']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(83, 149.3);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['ville_retrait']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(137, 149.3);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['pays']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(133, 200);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['nom']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(133, 205);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['prenom']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(58, 200);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['nom']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(58, 205);
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['prenom']));

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(45,225 );
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_cert['date_demande'])); 

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(75,225 );
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['ville'])); 

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(120,219.1 );
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_cert['date_demande'])); 

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(150,219.1 );
$pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['ville'])); 




// Importez les pages suivantes du template
for ($i = 2; $i <= 5; $i++) {
    $tplId = $pdf->importPage($i);
    $pdf->AddPage();
    $pdf->useTemplate($tplId, 10, 10, 200);


    if ($i == 2) {
        // Ajouter du texte à la deuxième page
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(130, 111);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['agence_retrait']));

        $pdf->SetXY(45, 116.5);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['adresse_poste']));

        $pdf->SetXY(45, 128);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_nv['ville_retrait']));

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(45, 156 );
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_cert['date_demande'])); 

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(75, 156 );
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['ville'])); 

        $pdf->SetXY(85, 195);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['nom']));

        $pdf->SetXY(85, 205);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['prenom']));

    }

    if ($i == 3) {
        // Ajouter du texte à la troixième page
        $pdf->SetFont('helvetica', '', 10);

        $pdf->SetXY(77, 88);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['nom']));

        $pdf->SetXY(124, 88);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['prenom']));

        $pdf->SetXY(30, 106);
    $pdf->Cell(0, 10, is_null($user_rep['q01']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q01']));

    $pdf->SetXY(30, 115.3);
    $pdf->Cell(0, 10, is_null($user_rep['q02']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q02']));

    $pdf->SetXY(30, 125);
    $pdf->Cell(0, 10, is_null($user_rep['q03']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q03']));

    $pdf->SetXY(30, 134.4);
    $pdf->Cell(0, 10, is_null($user_rep['q04']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q04']));

    $pdf->SetXY(30, 144);
    $pdf->Cell(0, 10, is_null($user_rep['q05']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q05']));

    $pdf->SetXY(30, 153.6);
    $pdf->Cell(0, 10, is_null($user_rep['q06']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q06']));

    $pdf->SetXY(30, 163);
    $pdf->Cell(0, 10, is_null($user_rep['q07']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q07']));

    $pdf->SetXY(30, 172.4);
    $pdf->Cell(0, 10, is_null($user_rep['q08']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q08']));

    $pdf->SetXY(30, 182);
    $pdf->Cell(0, 10, is_null($user_rep['q09']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q09']));

    $pdf->SetXY(30, 191.4);
    $pdf->Cell(0, 10, is_null($user_rep['q10']) ? 'Pas encore de réponses' : iconv('UTF-8', 'UTF-8//IGNORE', $user_rep['q10']));

        $pdf->SetXY(90, 215);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['nom']));

        $pdf->SetXY(95, 221);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['prenom']));
    }

    if ($i == 5) {
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(115, 249.3);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['nom']));
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(115,253.3 );
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_man['prenom'])); 


        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(150, 249.3);
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['nom']));
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(150,253.3 );
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user['prenom']));

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(115,210 );
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_cert['date_demande'])); 
        
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(165,210 );
        $pdf->Cell(0, 10, iconv('UTF-8', 'UTF-8//IGNORE', $user_cert['date_demande'])); 
    }



}


// Utilisez le template comme fond de page

// Format the filename with cin, nom, and prenom
$filename = sprintf('Demande_%s_%s_%s.pdf', $cin, $user['nom'], $user['prenom']);

// Define the full file path
$directoryPath = realpath(__DIR__ . '/../pdf_storage1');

// Ensure the directory exists
if (!is_dir($directoryPath)) {
    mkdir($directoryPath, 0777, true);
}

$pdfFilePath = $directoryPath . '/' . $filename;

// Save the PDF to a file
$pdf->Output($pdfFilePath, 'F');

// Output the PDF to the browser
$pdf->Output($filename, 'I');

// Fermez la connexion à la base de données
$conn->close();

// Afficher un message de succès
//echo "Le fichier PDF a été généré avec succès !";
?>
