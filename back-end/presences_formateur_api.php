<?php
// back-end/historique_presences_formateur_api.php

require_once 'api.php';
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 0);

// Vérification de l'authentification et du rôle
// Actuellement commenté pour le développement, à décommenter en production
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Non authentifié']);
//     exit;
// }

// if ($_SESSION['user_type'] !== 'formateur') {
//     echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
//     exit;
// }

$formateur_id = $_SESSION['user_id'] ?? 1; // Utiliser ID 1 pour le développement, à supprimer en production
$conn = connect();

try {
    if (!isset($_GET['action'])) {
        throw new Exception('Action non spécifiée');
    }

    $action = $_GET['action'];

    switch ($action) {
        case 'getGroupes':
            // Récupère uniquement les groupes associés au formateur
            $stmt = $conn->prepare("
                SELECT DISTINCT g.* 
                FROM groupe g
                JOIN seance s ON g.id = s.id_groupe
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE m.id_formateur = :formateur_id
            ");
            $stmt->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'groupes' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        case 'getEtudiants':
            // Récupérer les étudiants d'un groupe spécifique enseigné par le formateur
            $groupeId = $_GET['groupeId'] ?? null;

            if (!$groupeId) {
                throw new Exception('ID de groupe manquant');
            }

            // Vérifier que le formateur enseigne bien à ce groupe
            $check = $conn->prepare("
                SELECT COUNT(*) 
                FROM seance s
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE s.id_groupe = :groupeId
                AND m.id_formateur = :formateur_id
            ");
            $check->bindParam(':groupeId', $groupeId, PDO::PARAM_INT);
            $check->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $check->execute();

            if ($check->fetchColumn() == 0) {
                throw new Exception('Groupe non enseigné par ce formateur');
            }

            // Récupérer les étudiants
            $stmt = $conn->prepare("
                SELECT id, nom, prenom, email 
                FROM etudiant 
                WHERE id_groupe = :groupeId
                ORDER BY nom, prenom
            ");
            $stmt->bindParam(':groupeId', $groupeId, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'etudiants' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        case 'getHistoriquePresences':
            // Récupérer l'historique des présences pour un étudiant spécifique
            $etudiantId = $_GET['etudiantId'] ?? null;

            if (!$etudiantId) {
                throw new Exception('ID d\'étudiant manquant');
            }

            // Vérifier que l'étudiant appartient à un groupe enseigné par le formateur
            $check = $conn->prepare("
                SELECT COUNT(*) FROM etudiant e
                JOIN groupe g ON e.id_groupe = g.id
                JOIN seance s ON g.id = s.id_groupe
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE e.id = :etudiantId
                AND m.id_formateur = :formateur_id
            ");
            $check->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
            $check->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $check->execute();

            if ($check->fetchColumn() == 0) {
                throw new Exception('Étudiant non enseigné par ce formateur');
            }

            // Récupérer l'historique des présences pour cet étudiant avec les informations des séances et modules
            $stmt = $conn->prepare("
                SELECT p.id, p.date_time, p.status, 
                       s.date_debut, s.date_fin, s.status as seance_status,
                       m.nom as module_nom, sl.nom as salle_nom,
                       e.nom as etudiant_nom, e.prenom as etudiant_prenom
                FROM presence p
                JOIN etudiant e ON p.id_etudiant = e.id
                JOIN seance s ON p.id_seance = s.id
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                JOIN salle sl ON s.id_salle = sl.id
                WHERE p.id_etudiant = :etudiantId
                AND m.id_formateur = :formateur_id
                ORDER BY p.date_time DESC
            ");
            $stmt->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
            $stmt->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'historique' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        case 'getStatistiquesPresence':
            // Récupérer les statistiques de présence pour un étudiant spécifique
            $etudiantId = $_GET['etudiantId'] ?? null;

            if (!$etudiantId) {
                throw new Exception('ID d\'étudiant manquant');
            }

            // Vérifier que l'étudiant appartient à un groupe enseigné par le formateur
            $check = $conn->prepare("
                SELECT COUNT(*) FROM etudiant e
                JOIN groupe g ON e.id_groupe = g.id
                JOIN seance s ON g.id = s.id_groupe
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE e.id = :etudiantId
                AND m.id_formateur = :formateur_id
            ");
            $check->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
            $check->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $check->execute();

            if ($check->fetchColumn() == 0) {
                throw new Exception('Étudiant non enseigné par ce formateur');
            }

            // Récupérer les statistiques par module
            $stmt = $conn->prepare("
                SELECT m.nom as module_nom, 
                       COUNT(CASE WHEN p.status = 'present' THEN 1 END) as presents,
                       COUNT(CASE WHEN p.status = 'absent' THEN 1 END) as absents,
                       COUNT(CASE WHEN p.status = 'late' THEN 1 END) as retards,
                       COUNT(p.id) as total
                FROM presence p
                JOIN seance s ON p.id_seance = s.id
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE p.id_etudiant = :etudiantId
                AND m.id_formateur = :formateur_id
                GROUP BY m.id, m.nom
            ");
            $stmt->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
            $stmt->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $stmt->execute();
            $stats_module = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les statistiques globales
            $stmt = $conn->prepare("
                SELECT 
                    COUNT(CASE WHEN p.status = 'present' THEN 1 END) as presents,
                    COUNT(CASE WHEN p.status = 'absent' THEN 1 END) as absents,
                    COUNT(CASE WHEN p.status = 'late' THEN 1 END) as retards,
                    COUNT(p.id) as total
                FROM presence p
                JOIN seance s ON p.id_seance = s.id
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE p.id_etudiant = :etudiantId
                AND m.id_formateur = :formateur_id
            ");
            $stmt->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
            $stmt->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $stmt->execute();
            $stats_global = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'statistiques' => [
                    'global' => $stats_global,
                    'par_module' => $stats_module
                ]
            ]);
            break;

        case 'getStatistiquesGroupe':
            // Récupérer les statistiques de présence pour un groupe spécifique
            $groupeId = $_GET['groupeId'] ?? null;

            if (!$groupeId) {
                throw new Exception('ID de groupe manquant');
            }

            // Vérifier que le groupe est enseigné par le formateur
            $check = $conn->prepare("
                SELECT COUNT(*) 
                FROM seance s
                JOIN cours c ON s.id_cours = c.id
                JOIN module m ON c.id_module = m.id
                WHERE s.id_groupe = :groupeId
                AND m.id_formateur = :formateur_id
            ");
            $check->bindParam(':groupeId', $groupeId, PDO::PARAM_INT);
            $check->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $check->execute();

            if ($check->fetchColumn() == 0) {
                throw new Exception('Groupe non enseigné par ce formateur');
            }

            // Récupérer les statistiques pour chaque étudiant du groupe
            $stmt = $conn->prepare("
                SELECT e.id, e.nom, e.prenom,
                       COUNT(CASE WHEN p.status = 'present' THEN 1 END) as presents,
                       COUNT(CASE WHEN p.status = 'absent' THEN 1 END) as absents,
                       COUNT(CASE WHEN p.status = 'late' THEN 1 END) as retards,
                       COUNT(p.id) as total
                FROM etudiant e
                LEFT JOIN presence p ON e.id = p.id_etudiant
                LEFT JOIN seance s ON p.id_seance = s.id
                LEFT JOIN cours c ON s.id_cours = c.id
                LEFT JOIN module m ON c.id_module = m.id
                WHERE e.id_groupe = :groupeId
                AND (m.id_formateur = :formateur_id OR m.id_formateur IS NULL)
                GROUP BY e.id, e.nom, e.prenom
                ORDER BY e.nom, e.prenom
            ");
            $stmt->bindParam(':groupeId', $groupeId, PDO::PARAM_INT);
            $stmt->bindParam(':formateur_id', $formateur_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'statistiques' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        default:
            throw new Exception('Action non reconnue');
    }
} catch (PDOException $e) {
    error_log("Erreur DB: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>