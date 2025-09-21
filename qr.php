<?php
include('./back-end/api.php'); // Inclure votre fichier de connexion à la base de données

// Charger l'autoload de Composer
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Vérifier si le texte est passé via GET ou POST
if (isset($_REQUEST['idm'], $_REQUEST['ids'])) {
    $idm = $_GET['idm'];
    $ids = $_GET['ids'];

    // Valider l'ID (doit être numérique)
    if (!is_numeric($idm)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'ID invalide']);
        exit;
    }

    // Récupérer les informations de la séance depuis la base de données
    $conn = connect();
    $stmt = $conn->prepare("
       SELECT id ,nom FROM `module`
        WHERE id = ?
    ");
    $stmt->execute([$idm]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$module) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'module non trouvée']);
        exit;
    }


    $qrContent = $module['id'] . ';' . $ids . ';' . $module['nom'];


    // Créer le QR code
    $qrCode = new QrCode($qrContent);

    // Utiliser PngWriter pour générer l'image PNG
    $writer = new PngWriter();
    $image = $writer->write($qrCode);

    // Défini les en-têtes pour l'image PNG
    header('Content-Type: image/png');

    // Affiche l'image PNG directement dans le navigateur
    echo $image->getString();
}
?>