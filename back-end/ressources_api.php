<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

include('api.php'); // Inclure votre fichier de connexion à la base de données

// Connexion à la base de données
$conn = connect();
header('Content-Type: application/json');

// Activer les erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier l'existence de l'action
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'getRessources') {
        // Récupérer l'ID de l'étudiant
        $etudiant_id = $_GET['etudiant_id'];

        try {
            // Requête optimisée pour récupérer les ressources
            $stmt = $conn->prepare("
    SELECT 
        r.id, r.titre, r.description, r.fichier_url, r.image_url
    FROM 
        ressources r
    JOIN 
        module m ON r.id_module = m.id
    JOIN 
        cours c ON m.id = c.id_module
    JOIN 
        etudiant e ON c.id_groupe = e.id_groupe
    WHERE 
        e.id = :etudiant_id
");
            $stmt->bindParam(':etudiant_id', $etudiant_id);
            $stmt->execute();

            $ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($ressources) {
                echo json_encode(['success' => true, 'ressources' => $ressources]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucune ressource trouvée pour cet étudiant.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Aucune action spécifiée']);
}
?>