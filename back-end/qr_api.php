<?php
header('Content-Type: application/json');
require_once 'api.php'; // Fichier de configuration avec la connexion à la DB

$action = $_GET['action'] ?? '';
$conn = connect();

try {
    switch ($action) {
        case 'getModules':
            $stmt = $conn->query("SELECT id, nom FROM module");
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'modules' => $modules
            ]);
            break;

        case 'getSeances':
            $moduleId = $_GET['moduleId'] ?? 0;

            if (!$moduleId) {
                echo json_encode(['status' => 'error', 'message' => 'Module ID manquant']);
                exit;
            }

            $stmt = $conn->prepare("
                SELECT s.id, s.date_debut, s.date_fin, s.status
                FROM seance s
                JOIN cours c ON s.id_cours = c.id
                WHERE c.id_module = :moduleId
                ORDER BY s.date_debut DESC
            ");
            $stmt->execute([':moduleId' => $moduleId]);
            $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'seances' => $seances
            ]);
            break;

        case 'getQRCodes':
            $stmt = $conn->query("
                SELECT q.id, m.nom as nom_module, 
                       f.nom as nom_formateur, f.prenom as prenom_formateur,
                       s.date_debut, s.date_fin, q.qr_url
                FROM qr_codes q
                JOIN seance s ON q.id_seance = s.id
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                JOIN formateur f ON s.id_formateur = f.id
                ORDER BY s.date_debut DESC
            ");
            $qrCodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'qrCodes' => $qrCodes
            ]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Action non reconnue']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}