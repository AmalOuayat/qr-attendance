<?php
// Connexion à la base de données
function connect()
{
    $host = "localhost:3306";
    $dbname = "gestion_presence_qr";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES utf8");
        return $conn;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// Configuration des en-têtes
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = connect();

try {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        switch ($action) {
            case 'getGroupes':
                getGroupes($conn);
                break;

            case 'getSeances':
                $groupeId = $_GET['groupeId'] ?? null;
                $date = $_GET['date'] ?? null;
                getSeances($conn, $groupeId, $date);
                break;

            case 'getEtudiants':
                $groupeId = $_GET['groupeId'] ?? null;
                $seanceId = $_GET['seanceId'] ?? null;
                getEtudiants($conn, $groupeId, $seanceId);
                break;

            case 'updatePresence':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $etudiantId = $_POST['etudiantId'] ?? null;
                    $seanceId = $_POST['seanceId'] ?? null;
                    $status = $_POST['status'] ?? null;
                    $moduleId = $_POST['moduleId'] ?? null;
                    updatePresence($conn, $etudiantId, $seanceId, $status, $moduleId);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune action spécifiée']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}

function getGroupes($conn)
{
    try {
        $stmt = $conn->prepare("SELECT id, nom FROM groupe ORDER BY nom");
        $stmt->execute();
        $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'groupes' => $groupes]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des groupes : ' . $e->getMessage()]);
    }
}

function getSeances($conn, $groupeId, $date)
{
    if (!$groupeId || !$date) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        return;
    }

    try {
        $stmt = $conn->prepare("
            SELECT s.id, s.date_debut, s.date_fin, s.status, 
                   m.nom as module_nom, f.nom as formateur_nom, f.prenom as formateur_prenom,
                   sa.nom as salle_nom, m.id as module_id
            FROM seance s
            JOIN cours c ON s.id_cours = c.id
            JOIN module m ON c.id_module = m.id
            JOIN formateur f ON s.id_formateur = f.id
            JOIN salle sa ON s.id_salle = sa.id
            WHERE s.id_groupe = :groupeId 
            AND DATE(s.date_debut) = :date
            ORDER BY s.date_debut ASC
        ");
        $stmt->bindParam(':groupeId', $groupeId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normaliser les statuts pour correspondre au front-end
        foreach ($seances as &$seance) {
            switch ($seance['status']) {
                case 'Planifiée':
                    $seance['status'] = 'planifiee';
                    break;
                case 'En cours':
                    $seance['status'] = 'en_cours';
                    break;
                case 'Terminée':
                    $seance['status'] = 'terminee';
                    break;
                case 'Annulée':
                    $seance['status'] = 'annulee';
                    break;
            }
        }

        echo json_encode(['success' => true, 'seances' => $seances]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des séances : ' . $e->getMessage()]);
    }
}

function getEtudiants($conn, $groupeId, $seanceId)
{
    if (!$groupeId || !$seanceId) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        return;
    }

    try {
        // Récupérer tous les étudiants du groupe
        $stmt = $conn->prepare("
            SELECT id, cne, nom, prenom, email
            FROM etudiant
            WHERE id_groupe = :groupeId
            ORDER BY nom, prenom
        ");
        $stmt->bindParam(':groupeId', $groupeId, PDO::PARAM_INT);
        $stmt->execute();
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les présences existantes
        $stmt = $conn->prepare("
            SELECT id_etudiant, status
            FROM presence
            WHERE id_seance = :seanceId
        ");
        $stmt->bindParam(':seanceId', $seanceId, PDO::PARAM_INT);
        $stmt->execute();
        $presences = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Créer un tableau associatif pour les statuts
        $presenceMap = [];
        foreach ($presences as $presence) {
            $presenceMap[$presence['id_etudiant']] = $presence['status'];
        }

        // Combiner les données
        $result = [];
        foreach ($etudiants as $etudiant) {
            $status = $presenceMap[$etudiant['id']] ?? 'absent';
            $result[] = array_merge($etudiant, ['status' => $status]);
        }

        echo json_encode(['success' => true, 'etudiants' => $result]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des étudiants : ' . $e->getMessage()]);
    }
}

function updatePresence($conn, $etudiantId, $seanceId, $status, $moduleId)
{
    if (!$etudiantId || !$seanceId || !$status || !$moduleId) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        return;
    }

    // Valider le statut
    $allowedStatuses = ['present', 'absent', 'late', 'justifie'];
    if (!in_array($status, $allowedStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Statut non valide']);
        return;
    }

    try {
        $conn->beginTransaction();

        // Vérifier si la présence existe déjà
        $stmt = $conn->prepare("
            SELECT id FROM presence 
            WHERE id_etudiant = :etudiantId AND id_seance = :seanceId
            FOR UPDATE
        ");
        $stmt->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
        $stmt->bindParam(':seanceId', $seanceId, PDO::PARAM_INT);
        $stmt->execute();
        $existingPresence = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingPresence) {
            // Mise à jour
            $stmt = $conn->prepare("
                UPDATE presence 
                SET status = :status, date_time = NOW() 
                WHERE id = :id
            ");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $existingPresence['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Insertion
            $stmt = $conn->prepare("
                INSERT INTO presence (id_etudiant, id_seance, date_time, status, id_module) 
                VALUES (:etudiantId, :seanceId, NOW(), :status, :moduleId)
            ");
            $stmt->bindParam(':etudiantId', $etudiantId, PDO::PARAM_INT);
            $stmt->bindParam(':seanceId', $seanceId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
            $stmt->execute();
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
    }
}
?>