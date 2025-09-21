<?php
include('api.php'); // Inclure votre fichier de connexion à la base de données

// Connexion à la base de données
$conn = connect();
header('Content-Type: application/json');

// Activer les erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier l'existence de l'action
if (isset($_GET['action'])) {
    // Si l'action est 'getCoursEtudiant'
    if ($_GET['action'] == 'getCoursEtudiant') {
        // Récupérer l'ID de l'étudiant
        $etudiant_id = $_GET['etudiant_id'];

        try {
            // Requête pour récupérer les cours de l'étudiant
            $stmt = $conn->prepare("
                SELECT 
                    m.nom AS nom_module,
                    f.nom AS nom_formateur,
                    f.prenom AS prenom_formateur,
                    s.nom AS nom_salle,
                    c.date_time AS date_debut,
                    se.status
                FROM 
                    etudiant e
                JOIN 
                    groupe g ON e.id_groupe = g.id
                JOIN 
                    cours c ON g.id = c.id_groupe
                JOIN 
                    module m ON c.id_module = m.id
                JOIN 
                    formateur f ON m.id_formateur = f.id
                JOIN 
                    salle s ON c.id_salle = s.id
                JOIN 
                    seance se ON c.id = se.id_cours
                WHERE 
                    e.id = :etudiant_id
            ");
            $stmt->bindParam(':etudiant_id', $etudiant_id);
            $stmt->execute();

            // Récupérer les résultats
            $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($cours) > 0) {
                echo json_encode(['success' => true, 'cours' => $cours]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucun cours trouvé pour cet étudiant.']);
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