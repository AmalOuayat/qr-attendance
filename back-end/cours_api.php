<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'gestion_presence_qr';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

// Options PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password, $options);
} catch (PDOException $e) {
    sendJsonResponse(['success' => false, 'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()], 500);
    exit;
}

// Headers pour CORS et JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si c'est une requête OPTIONS, renvoyer juste les en-têtes CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Récupérer l'action de l'API
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Router les actions
switch ($action) {
    case 'load_formateur':
        loadFormateur();
        break;
    case 'load_modules':
        loadModules();
        break;
    case 'load_groupes':
        loadGroupes();
        break;
    case 'load_salles':
        loadSalles();
        break;
    case 'load_seances':
        loadSeances();
        break;
    case 'add_seance':
        addSeance();
        break;
    case 'update_seance':
        updateSeance();
        break;
    case 'delete_seance':
        deleteSeance();
        break;
    case 'get_seance':
        getSeance();
        break;
    default:
        sendJsonResponse(['success' => false, 'message' => 'Action non reconnue'], 400);
        break;
}

// Fonction pour charger les informations d'un formateur
function loadFormateur()
{
    global $pdo;

    // Vérifier que l'ID du formateur est fourni
    if (!isset($_GET['id'])) {
        sendJsonResponse(['success' => false, 'message' => 'ID du formateur non fourni'], 400);
        return;
    }

    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare('SELECT id, nom, prenom, email FROM formateur WHERE id = ?');
        $stmt->execute([$id]);
        $formateur = $stmt->fetch();

        if ($formateur) {
            // Formater le nom complet
            $formateur['nom'] = $formateur['prenom'] . ' ' . $formateur['nom'];
            sendJsonResponse($formateur);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Formateur non trouvé'], 404);
        }
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors du chargement du formateur: ' . $e->getMessage()], 500);
    }
}

// Fonction pour charger les modules d'un formateur
function loadModules()
{
    global $pdo;

    // Vérifier que l'ID du formateur est fourni
    if (!isset($_GET['id_formateur'])) {
        sendJsonResponse(['success' => false, 'message' => 'ID du formateur non fourni'], 400);
        return;
    }

    $id_formateur = intval($_GET['id_formateur']);

    try {
        $stmt = $pdo->prepare('SELECT id, nom FROM module WHERE id_formateur = ?');
        $stmt->execute([$id_formateur]);
        $modules = $stmt->fetchAll();

        sendJsonResponse($modules);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors du chargement des modules: ' . $e->getMessage()], 500);
    }
}

// Fonction pour charger tous les groupes
function loadGroupes()
{
    global $pdo;

    try {
        $stmt = $pdo->query('SELECT id, nom FROM groupe');
        $groupes = $stmt->fetchAll();

        sendJsonResponse($groupes);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors du chargement des groupes: ' . $e->getMessage()], 500);
    }
}

// Fonction pour charger toutes les salles
function loadSalles()
{
    global $pdo;

    try {
        $stmt = $pdo->query('SELECT id, nom FROM salle');
        $salles = $stmt->fetchAll();

        sendJsonResponse($salles);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors du chargement des salles: ' . $e->getMessage()], 500);
    }
}

// Fonction pour charger les séances d'un formateur
function loadSeances()
{
    global $pdo;

    // Vérifier que l'ID du formateur est fourni
    if (!isset($_GET['id_formateur'])) {
        sendJsonResponse(['success' => false, 'message' => 'ID du formateur non fourni'], 400);
        return;
    }

    $id_formateur = intval($_GET['id_formateur']);

    try {
        // Requête pour obtenir toutes les séances du formateur avec les informations associées
        $stmt = $pdo->prepare('
            SELECT 
                s.id,
                DATE(s.date_debut) as date,
                TIME(s.date_debut) as heure_debut,
                TIME(s.date_fin) as heure_fin,
                m.id as id_module,
                m.nom as module_nom,
                g.id as id_groupe,
                g.nom as groupe_nom,
                sa.id as id_salle,
                sa.nom as salle_nom,
                s.status
            FROM 
                seance s
            JOIN 
                module m ON s.id_cours = m.id
            JOIN 
                groupe g ON s.id_groupe = g.id
            JOIN 
                salle sa ON s.id_salle = sa.id
            WHERE 
                s.id_formateur = ?
            ORDER BY 
                s.date_debut
        ');
        $stmt->execute([$id_formateur]);
        $seances = $stmt->fetchAll();

        sendJsonResponse($seances);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors du chargement des séances: ' . $e->getMessage()], 500);
    }
}

// Fonction pour récupérer une séance spécifique
function getSeance()
{
    global $pdo;

    // Vérifier que l'ID de la séance est fourni
    if (!isset($_GET['id'])) {
        sendJsonResponse(['success' => false, 'message' => 'ID de la séance non fourni'], 400);
        return;
    }

    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare('
            SELECT 
                s.id,
                DATE(s.date_debut) as date,
                TIME(s.date_debut) as heure_debut,
                TIME(s.date_fin) as heure_fin,
                s.id_cours as id_module,
                s.id_groupe,
                s.id_salle,
                s.id_formateur
            FROM 
                seance s
            WHERE 
                s.id = ?
        ');
        $stmt->execute([$id]);
        $seance = $stmt->fetch();

        if ($seance) {
            sendJsonResponse($seance);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Séance non trouvée'], 404);
        }
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors de la récupération de la séance: ' . $e->getMessage()], 500);
    }
}

// Fonction pour ajouter une nouvelle séance
function addSeance()
{
    global $pdo;

    // Récupérer les données envoyées
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier que toutes les données nécessaires sont présentes
    $requiredFields = ['date', 'heure_debut', 'heure_fin', 'id_module', 'id_groupe', 'id_salle', 'id_formateur'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            sendJsonResponse(['success' => false, 'message' => 'Champ obligatoire manquant: ' . $field], 400);
            return;
        }
    }

    try {
        // Créer d'abord une entrée dans la table cours
        $stmtCours = $pdo->prepare('
            INSERT INTO cours (date_time, id_module, id_groupe, id_salle, id_filiere, id_formateur)
            VALUES (NOW(), ?, ?, ?, 1, ?)
        ');
        $stmtCours->execute([
            $input['id_module'],
            $input['id_groupe'],
            $input['id_salle'],
            $input['id_formateur']
        ]);

        $coursId = $pdo->lastInsertId();

        // Créer ensuite la séance
        $dateDebut = $input['date'] . ' ' . $input['heure_debut'];
        $dateFin = $input['date'] . ' ' . $input['heure_fin'];

        $stmtSeance = $pdo->prepare('
            INSERT INTO seance (date_debut, date_fin, id_cours, id_salle, id_formateur, id_groupe, status)
            VALUES (?, ?, ?, ?, ?, ?, "Planifiée")
        ');
        $stmtSeance->execute([
            $dateDebut,
            $dateFin,
            $coursId,
            $input['id_salle'],
            $input['id_formateur'],
            $input['id_groupe']
        ]);

        $seanceId = $pdo->lastInsertId();

        // Ajouter éventuellement à l'emploi du temps
        // Cette partie peut être adaptée selon les besoins exacts

        sendJsonResponse(['success' => true, 'message' => 'Séance ajoutée avec succès', 'id' => $seanceId]);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors de l\'ajout de la séance: ' . $e->getMessage()], 500);
    }
}

// Fonction pour mettre à jour une séance existante
function updateSeance()
{
    global $pdo;

    // Récupérer les données envoyées
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier que toutes les données nécessaires sont présentes
    $requiredFields = ['id', 'date', 'heure_debut', 'heure_fin', 'id_module', 'id_groupe', 'id_salle', 'id_formateur'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            sendJsonResponse(['success' => false, 'message' => 'Champ obligatoire manquant: ' . $field], 400);
            return;
        }
    }

    try {
        // Récupérer l'ID du cours associé à la séance
        $stmtGetCours = $pdo->prepare('SELECT id_cours FROM seance WHERE id = ?');
        $stmtGetCours->execute([$input['id']]);
        $seance = $stmtGetCours->fetch();

        if (!$seance) {
            sendJsonResponse(['success' => false, 'message' => 'Séance non trouvée'], 404);
            return;
        }

        $coursId = $seance['id_cours'];

        // Mettre à jour le cours
        $stmtCours = $pdo->prepare('
            UPDATE cours
            SET id_module = ?, id_groupe = ?, id_salle = ?, id_formateur = ?
            WHERE id = ?
        ');
        $stmtCours->execute([
            $input['id_module'],
            $input['id_groupe'],
            $input['id_salle'],
            $input['id_formateur'],
            $coursId
        ]);

        // Mettre à jour la séance
        $dateDebut = $input['date'] . ' ' . $input['heure_debut'];
        $dateFin = $input['date'] . ' ' . $input['heure_fin'];

        $stmtSeance = $pdo->prepare('
            UPDATE seance
            SET date_debut = ?, date_fin = ?, id_salle = ?, id_formateur = ?, id_groupe = ?
            WHERE id = ?
        ');
        $stmtSeance->execute([
            $dateDebut,
            $dateFin,
            $input['id_salle'],
            $input['id_formateur'],
            $input['id_groupe'],
            $input['id']
        ]);

        sendJsonResponse(['success' => true, 'message' => 'Séance mise à jour avec succès']);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors de la mise à jour de la séance: ' . $e->getMessage()], 500);
    }
}

// Fonction pour supprimer une séance
function deleteSeance()
{
    global $pdo;

    // Récupérer les données envoyées
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier que l'ID est fourni
    if (!isset($input['id']) || empty($input['id'])) {
        sendJsonResponse(['success' => false, 'message' => 'ID de la séance non fourni'], 400);
        return;
    }

    $id = intval($input['id']);

    try {
        // Récupérer l'ID du cours associé à la séance
        $stmtGetCours = $pdo->prepare('SELECT id_cours FROM seance WHERE id = ?');
        $stmtGetCours->execute([$id]);
        $seance = $stmtGetCours->fetch();

        if (!$seance) {
            sendJsonResponse(['success' => false, 'message' => 'Séance non trouvée'], 404);
            return;
        }

        $coursId = $seance['id_cours'];

        // Supprimer la séance d'abord (contrainte de clé étrangère)
        $stmtSeance = $pdo->prepare('DELETE FROM seance WHERE id = ?');
        $stmtSeance->execute([$id]);

        // Supprimer ensuite le cours
        $stmtCours = $pdo->prepare('DELETE FROM cours WHERE id = ?');
        $stmtCours->execute([$coursId]);

        sendJsonResponse(['success' => true, 'message' => 'Séance supprimée avec succès']);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression de la séance: ' . $e->getMessage()], 500);
    }
}

// Fonction utilitaire pour envoyer une réponse JSON
function sendJsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}