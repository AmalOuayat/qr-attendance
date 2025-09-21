<?php
session_start();

// Authentication check
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
    // Si l'action est 'getHistoriquePresences'
    if ($_GET['action'] == 'getHistoriquePresences') {
        // Récupérer l'ID de l'étudiant
        $etudiant_id = isset($_GET['etudiant_id']) ? $_GET['etudiant_id'] : $_SESSION['user_id'];
        
        try {
            // Requête pour récupérer l'historique des présences de l'étudiant
            $stmt = $conn->prepare("
                SELECT
                    m.nom AS nom_module,
                    p.date_time,
                    p.status,
                    s.date_debut AS heure_debut,
                    s.date_fin AS heure_fin,
                    f.nom AS nom_formateur,
                    f.prenom AS prenom_formateur,
                    sl.nom AS nom_salle
                FROM
                    presence p
                JOIN
                    seance s ON p.id_seance = s.id
                JOIN
                    module m ON p.id_module = m.id
                LEFT JOIN
                    formateur f ON s.id_formateur = f.id
                LEFT JOIN
                    salle sl ON s.id_salle = sl.id
                WHERE
                    p.id_etudiant = :etudiant_id
                ORDER BY
                    p.date_time DESC
            ");
            $stmt->bindParam(':etudiant_id', $etudiant_id);
            $stmt->execute();
            
            // Récupérer les résultats
            $presences = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($presences) > 0) {
                echo json_encode(['success' => true, 'presences' => $presences]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucune présence trouvée pour cet étudiant.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }
} else {
    // Si l'action n'est pas définie dans l'URL
    echo json_encode(['success' => false, 'message' => 'Aucune action spécifiée']);
}
?>