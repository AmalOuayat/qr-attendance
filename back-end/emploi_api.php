<?php
/**
 * API de gestion de l'emploi du temps - Version corrigée
 * Avec journalisation des erreurs pour le débogage
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_presence_qr');

// Activer la journalisation des erreurs
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');
error_reporting(E_ALL);

// Fonction pour journaliser les erreurs
function log_debug($message)
{
    error_log(date('Y-m-d H:i:s') . " - " . $message);
}

// Connexion à la base de données
function connect()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        log_debug("Erreur de connexion à la base de données: " . $conn->connect_error);
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

// Récupérer toutes les données nécessaires pour les listes déroulantes et l'emploi du temps
function getSelectsData($conn)
{
    // Récupérer tous les modules
    $modules = [];
    $result = $conn->query("SELECT id, nom FROM module");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $modules[] = $row;
        }
    } else {
        log_debug("Erreur lors de la récupération des modules: " . $conn->error);
    }

    // Récupérer tous les formateurs
    $formateurs = [];
    $result = $conn->query("SELECT id, nom, prenom FROM formateur");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $formateurs[] = $row;
        }
    } else {
        log_debug("Erreur lors de la récupération des formateurs: " . $conn->error);
    }

    // Récupérer tous les groupes
    $groupes = [];
    $result = $conn->query("SELECT id, nom FROM groupe");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $groupes[] = $row;
        }
    } else {
        log_debug("Erreur lors de la récupération des groupes: " . $conn->error);
    }

    // Récupérer toutes les salles
    $salles = [];
    $result = $conn->query("SELECT id, nom FROM salle");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $salles[] = $row;
        }
    } else {
        log_debug("Erreur lors de la récupération des salles: " . $conn->error);
    }

    return [
        'modules' => $modules,
        'formateurs' => $formateurs,
        'groupes' => $groupes,
        'salles' => $salles
    ];
}

// Récupérer l'emploi du temps avec les informations détaillées
function getEmploiDuTemps($conn)
{
    $sql = "SELECT 
                e.id, 
                e.jour, 
                e.creneau,
                e.id_module,
                e.id_formateur,
                e.id_salle,
                e.id_groupe,
                e.id_seance,
                m.nom AS module_nom,
                f.nom AS formateur_nom,
                f.prenom AS formateur_prenom,
                s.nom AS salle_nom,
                g.nom AS groupe_nom
            FROM 
                emploi_du_temps e
            JOIN 
                module m ON e.id_module = m.id
            JOIN 
                formateur f ON e.id_formateur = f.id
            JOIN 
                salle s ON e.id_salle = s.id
            JOIN 
                groupe g ON e.id_groupe = g.id
            ORDER BY 
                FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'),
                e.creneau";

    $result = $conn->query($sql);
    $emploi_du_temps = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['frequence'] = 'chaque_semaine'; // Valeur par défaut
            $emploi_du_temps[] = $row;
        }
    } else {
        log_debug("Erreur lors de la récupération de l'emploi du temps: " . $conn->error);
    }

    return $emploi_du_temps;
}

// Ajouter une séance - Version corrigée
function addSeance($conn, $data)
{
    log_debug("Début de la fonction addSeance avec les données: " . json_encode($data));

    // Récupérer les données du formulaire
    $jour = $conn->real_escape_string($data['jour']);
    $creneau = $conn->real_escape_string($data['creneau']);
    $id_module = (int) $data['id_module'];
    $id_groupe = (int) $data['id_groupe'];
    $id_formateur = (int) $data['id_formateur'];
    $id_salle = (int) $data['id_salle'];
    $frequence = $conn->real_escape_string($data['frequence'] ?? 'chaque_semaine');

    log_debug("Données validées: jour=$jour, creneau=$creneau, module=$id_module, groupe=$id_groupe, formateur=$id_formateur, salle=$id_salle");

    // Vérifier si une séance existe déjà à cette heure avec cette salle
    $sql_check = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE jour = '$jour' AND creneau = '$creneau' AND id_salle = $id_salle";
    $result_check = $conn->query($sql_check);
    if (!$result_check) {
        log_debug("Erreur lors de la vérification de la disponibilité de la salle: " . $conn->error);
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $conn->error];
    }

    $row_check = $result_check->fetch_assoc();
    if ($row_check['count'] > 0) {
        log_debug("La salle est déjà occupée à ce créneau");
        return ['success' => false, 'message' => 'Cette salle est déjà occupée à ce créneau horaire'];
    }

    // Vérifier si le formateur est disponible à ce créneau
    $sql_check_formateur = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE jour = '$jour' AND creneau = '$creneau' AND id_formateur = $id_formateur";
    $result_check_formateur = $conn->query($sql_check_formateur);
    if (!$result_check_formateur) {
        log_debug("Erreur lors de la vérification de la disponibilité du formateur: " . $conn->error);
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $conn->error];
    }

    $row_check_formateur = $result_check_formateur->fetch_assoc();
    if ($row_check_formateur['count'] > 0) {
        log_debug("Le formateur est déjà occupé à ce créneau");
        return ['success' => false, 'message' => 'Ce formateur est déjà occupé à ce créneau horaire'];
    }

    // Vérifier si le groupe est disponible à ce créneau
    $sql_check_groupe = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE jour = '$jour' AND creneau = '$creneau' AND id_groupe = $id_groupe";
    $result_check_groupe = $conn->query($sql_check_groupe);
    if (!$result_check_groupe) {
        log_debug("Erreur lors de la vérification de la disponibilité du groupe: " . $conn->error);
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $conn->error];
    }

    $row_check_groupe = $result_check_groupe->fetch_assoc();
    if ($row_check_groupe['count'] > 0) {
        log_debug("Le groupe est déjà occupé à ce créneau");
        return ['success' => false, 'message' => 'Ce groupe est déjà occupé à ce créneau horaire'];
    }

    // Commencer une transaction
    log_debug("Début de la transaction");
    $conn->begin_transaction();

    try {
        // Chercher ou créer un cours
        $sql_check_cours = "SELECT id FROM cours WHERE id_module = $id_module AND id_groupe = $id_groupe";
        log_debug("Vérification du cours existant: $sql_check_cours");
        $result_check_cours = $conn->query($sql_check_cours);

        if (!$result_check_cours) {
            throw new Exception("Erreur lors de la vérification du cours: " . $conn->error);
        }

        // Si aucun cours n'existe, en créer un
        if ($result_check_cours->num_rows === 0) {
            $sql_cours = "INSERT INTO cours (date_time, id_module, id_groupe, id_salle, id_filiere, id_formateur) 
                         VALUES (NOW(), $id_module, $id_groupe, $id_salle, 
                         (SELECT id_filiere FROM module WHERE id = $id_module LIMIT 1), $id_formateur)";

            log_debug("Création d'un nouveau cours: $sql_cours");
            if (!$conn->query($sql_cours)) {
                throw new Exception("Erreur lors de la création du cours: " . $conn->error);
            }

            $id_cours = $conn->insert_id;
            log_debug("Nouveau cours créé avec ID: $id_cours");
        } else {
            $row_cours = $result_check_cours->fetch_assoc();
            $id_cours = $row_cours['id'];
            log_debug("Cours existant trouvé avec ID: $id_cours");
        }

        // Calculer les dates début et fin en fonction du jour et du créneau
        $date_now = new DateTime();
        $jour_num = ['Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7][$jour];
        $date_now->modify('+' . (($jour_num - $date_now->format('N') + 7) % 7) . ' days');

        $heures_debut = [
            '8h30-11h00' => '08:30:00',
            '11h00-13h30' => '11:00:00',
            '13h30-16h00' => '13:30:00',
            '16h00-18h30' => '16:00:00'
        ];

        $heures_fin = [
            '8h30-11h00' => '11:00:00',
            '11h00-13h30' => '13:30:00',
            '13h30-16h00' => '16:00:00',
            '16h00-18h30' => '18:30:00'
        ];

        $date_debut = $date_now->format('Y-m-d') . ' ' . $heures_debut[$creneau];
        $date_fin = $date_now->format('Y-m-d') . ' ' . $heures_fin[$creneau];

        log_debug("Dates calculées: debut=$date_debut, fin=$date_fin");

        // Créer une séance dans la table seance
        $sql_seance = "INSERT INTO seance (date_debut, date_fin, id_cours, id_salle, id_formateur, id_groupe, status) 
                      VALUES ('$date_debut', '$date_fin', $id_cours, $id_salle, $id_formateur, $id_groupe, 'Planifiée')";

        log_debug("Création d'une nouvelle séance: $sql_seance");
        if (!$conn->query($sql_seance)) {
            throw new Exception("Erreur lors de la création de la séance: " . $conn->error);
        }

        $id_seance = $conn->insert_id;
        log_debug("Nouvelle séance créée avec ID: $id_seance");

        // Ajouter la séance à l'emploi du temps
        $sql = "INSERT INTO emploi_du_temps (jour, creneau, id_module, id_formateur, id_salle, id_groupe, id_seance) 
                VALUES ('$jour', '$creneau', $id_module, $id_formateur, $id_salle, $id_groupe, $id_seance)";

        log_debug("Ajout à l'emploi du temps: $sql");
        if (!$conn->query($sql)) {
            throw new Exception("Erreur lors de l'ajout à l'emploi du temps: " . $conn->error);
        }

        log_debug("Commit de la transaction");
        $conn->commit();
        log_debug("Séance ajoutée avec succès");
        return ['success' => true, 'message' => 'Séance ajoutée avec succès'];

    } catch (Exception $e) {
        log_debug("Erreur détectée, rollback de la transaction: " . $e->getMessage());
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Mettre à jour une séance - Version corrigée
function updateSeance($conn, $data)
{
    log_debug("Début de la fonction updateSeance avec les données: " . json_encode($data));

    // Récupérer les données du formulaire
    $id = (int) $data['id'];
    $jour = $conn->real_escape_string($data['jour']);
    $creneau = $conn->real_escape_string($data['creneau']);
    $id_module = (int) $data['id_module'];
    $id_groupe = (int) $data['id_groupe'];
    $id_formateur = (int) $data['id_formateur'];
    $id_salle = (int) $data['id_salle'];
    $frequence = $conn->real_escape_string($data['frequence'] ?? 'chaque_semaine');

    log_debug("Données validées: id=$id, jour=$jour, creneau=$creneau, module=$id_module, groupe=$id_groupe, formateur=$id_formateur, salle=$id_salle");

    // Vérifier si une autre séance existe déjà à cette heure avec cette salle (sauf celle qu'on modifie)
    $sql_check = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE jour = '$jour' AND creneau = '$creneau' AND id_salle = $id_salle AND id != $id";
    $result_check = $conn->query($sql_check);
    if (!$result_check) {
        log_debug("Erreur lors de la vérification de la disponibilité de la salle: " . $conn->error);
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $conn->error];
    }

    $row_check = $result_check->fetch_assoc();
    if ($row_check['count'] > 0) {
        log_debug("La salle est déjà occupée à ce créneau");
        return ['success' => false, 'message' => 'Cette salle est déjà occupée à ce créneau horaire'];
    }

    // Vérifier si le formateur est disponible à ce créneau (sauf pour la séance qu'on modifie)
    $sql_check_formateur = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE jour = '$jour' AND creneau = '$creneau' AND id_formateur = $id_formateur AND id != $id";
    $result_check_formateur = $conn->query($sql_check_formateur);
    if (!$result_check_formateur) {
        log_debug("Erreur lors de la vérification de la disponibilité du formateur: " . $conn->error);
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $conn->error];
    }

    $row_check_formateur = $result_check_formateur->fetch_assoc();
    if ($row_check_formateur['count'] > 0) {
        log_debug("Le formateur est déjà occupé à ce créneau");
        return ['success' => false, 'message' => 'Ce formateur est déjà occupé à ce créneau horaire'];
    }

    // Vérifier si le groupe est disponible à ce créneau (sauf pour la séance qu'on modifie)
    $sql_check_groupe = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE jour = '$jour' AND creneau = '$creneau' AND id_groupe = $id_groupe AND id != $id";
    $result_check_groupe = $conn->query($sql_check_groupe);
    if (!$result_check_groupe) {
        log_debug("Erreur lors de la vérification de la disponibilité du groupe: " . $conn->error);
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $conn->error];
    }

    $row_check_groupe = $result_check_groupe->fetch_assoc();
    if ($row_check_groupe['count'] > 0) {
        log_debug("Le groupe est déjà occupé à ce créneau");
        return ['success' => false, 'message' => 'Ce groupe est déjà occupé à ce créneau horaire'];
    }

    // Commencer une transaction
    log_debug("Début de la transaction pour mise à jour");
    $conn->begin_transaction();

    try {
        // Récupérer l'ID de la séance associée
        $sql_get_seance = "SELECT id_seance FROM emploi_du_temps WHERE id = $id";
        log_debug("Récupération de la séance: $sql_get_seance");
        $result_get_seance = $conn->query($sql_get_seance);

        if (!$result_get_seance || $result_get_seance->num_rows === 0) {
            throw new Exception("Séance non trouvée dans l'emploi du temps");
        }

        $row_get_seance = $result_get_seance->fetch_assoc();
        $id_seance = $row_get_seance['id_seance'];
        log_debug("Séance trouvée avec ID: $id_seance");

        // Calculer les dates début et fin en fonction du jour et du créneau
        $date_now = new DateTime();
        $jour_num = ['Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7][$jour];
        $date_now->modify('+' . (($jour_num - $date_now->format('N') + 7) % 7) . ' days');

        $heures_debut = [
            '8h30-11h00' => '08:30:00',
            '11h00-13h30' => '11:00:00',
            '13h30-16h00' => '13:30:00',
            '16h00-18h30' => '16:00:00'
        ];

        $heures_fin = [
            '8h30-11h00' => '11:00:00',
            '11h00-13h30' => '13:30:00',
            '13h30-16h00' => '16:00:00',
            '16h00-18h30' => '18:30:00'
        ];

        $date_debut = $date_now->format('Y-m-d') . ' ' . $heures_debut[$creneau];
        $date_fin = $date_now->format('Y-m-d') . ' ' . $heures_fin[$creneau];

        log_debug("Dates calculées pour mise à jour: debut=$date_debut, fin=$date_fin");

        // Mettre à jour la séance dans la table seance
        $sql_update_seance = "UPDATE seance SET 
                             date_debut = '$date_debut', 
                             date_fin = '$date_fin', 
                             id_cours = $id_module, 
                             id_salle = $id_salle, 
                             id_formateur = $id_formateur, 
                             id_groupe = $id_groupe 
                             WHERE id = $id_seance";

        log_debug("Mise à jour de la séance: $sql_update_seance");
        if (!$conn->query($sql_update_seance)) {
            throw new Exception("Erreur lors de la mise à jour de la séance: " . $conn->error);
        }

        // Mettre à jour la séance dans l'emploi du temps
        $sql = "UPDATE emploi_du_temps SET 
                jour = '$jour', 
                creneau = '$creneau', 
                id_module = $id_module, 
                id_formateur = $id_formateur, 
                id_salle = $id_salle, 
                id_groupe = $id_groupe
                WHERE id = $id";

        log_debug("Mise à jour de l'emploi du temps: $sql");
        if (!$conn->query($sql)) {
            throw new Exception("Erreur lors de la mise à jour de l'emploi du temps: " . $conn->error);
        }

        log_debug("Commit de la transaction pour mise à jour");
        $conn->commit();
        log_debug("Séance mise à jour avec succès");
        return ['success' => true, 'message' => 'Séance mise à jour avec succès'];

    } catch (Exception $e) {
        log_debug("Erreur détectée, rollback de la transaction: " . $e->getMessage());
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Supprimer une séance - Version corrigée
function deleteSeance($conn, $id)
{
    log_debug("Début de la fonction deleteSeance avec ID: $id");
    $id = (int) $id;

    // Commencer une transaction
    log_debug("Début de la transaction pour suppression");
    $conn->begin_transaction();

    try {
        // Récupérer l'ID de la séance associée
        $sql_get_seance = "SELECT id_seance FROM emploi_du_temps WHERE id = $id";
        log_debug("Récupération de la séance: $sql_get_seance");
        $result_get_seance = $conn->query($sql_get_seance);

        if (!$result_get_seance || $result_get_seance->num_rows === 0) {
            throw new Exception("Séance introuvable dans l'emploi du temps");
        }

        $row_get_seance = $result_get_seance->fetch_assoc();
        $id_seance = $row_get_seance['id_seance'];
        log_debug("Séance trouvée avec ID: $id_seance");

        // Supprimer la séance de l'emploi du temps
        $sql = "DELETE FROM emploi_du_temps WHERE id = $id";
        log_debug("Suppression de l'emploi du temps: $sql");

        if (!$conn->query($sql)) {
            throw new Exception("Erreur lors de la suppression de la séance de l'emploi du temps: " . $conn->error);
        }

        // Vérifier si la séance est encore référencée dans l'emploi du temps
        $sql_check_refs = "SELECT COUNT(*) AS count FROM emploi_du_temps WHERE id_seance = $id_seance";
        log_debug("Vérification des références: $sql_check_refs");
        $result_check_refs = $conn->query($sql_check_refs);

        if (!$result_check_refs) {
            throw new Exception("Erreur lors de la vérification des références: " . $conn->error);
        }

        $row_check_refs = $result_check_refs->fetch_assoc();

        if ($row_check_refs['count'] == 0) {
            // Supprimer la séance de la table seance si elle n'est plus référencée
            $sql_delete_seance = "DELETE FROM seance WHERE id = $id_seance";
            log_debug("Suppression de la séance: $sql_delete_seance");

            if (!$conn->query($sql_delete_seance)) {
                throw new Exception("Erreur lors de la suppression de la séance: " . $conn->error);
            }

            log_debug("Séance supprimée avec succès");
        } else {
            log_debug("La séance est encore référencée ailleurs, pas de suppression");
        }

        log_debug("Commit de la transaction pour suppression");
        $conn->commit();
        return ['success' => true, 'message' => 'Séance supprimée avec succès'];

    } catch (Exception $e) {
        log_debug("Erreur détectée, rollback de la transaction: " . $e->getMessage());
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Point d'entrée principal de l'API
$conn = connect();
header('Content-Type: application/json');

// Journaliser les requêtes entrantes
log_debug("Nouvelle requête - Méthode: " . $_SERVER['REQUEST_METHOD'] . ", URI: " . $_SERVER['REQUEST_URI']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_debug("Données POST reçues: " . json_encode($_POST));
}

// Traitement des différentes requêtes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Chargement des données initiales
    log_debug("Traitement d'une requête GET");
    $selectsData = getSelectsData($conn);

    // Si un paramètre spécifique est demandé, ne renvoyer que ces données
    if (isset($_GET['data']) && $_GET['data'] === 'emploi') {
        log_debug("Retour des données d'emploi du temps uniquement");
        echo json_encode(getEmploiDuTemps($conn));
    } else {
        // Sinon, envoyer toutes les données y compris l'emploi du temps
        log_debug("Retour de toutes les données");
        $emploiDuTemps = getEmploiDuTemps($conn);
        echo json_encode(array_merge($selectsData, ['emploi_du_temps' => $emploiDuTemps]));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_debug("Traitement d'une requête POST");

    // Vérifier s'il s'agit d'une demande d'ajout, de mise à jour ou de suppression
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Si c'est une action de suppression
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            log_debug("Action de suppression demandée pour l'ID: " . $_POST['id']);
            $result = deleteSeance($conn, $_POST['id']);
            echo json_encode($result);
        } else {
            // Sinon c'est une mise à jour
            log_debug("Action de mise à jour demandée pour l'ID: " . $_POST['id']);
            $result = updateSeance($conn, $_POST);
            echo json_encode($result);
        }
    } else {
        // C'est un ajout
        log_debug("Action d'ajout demandée");
        $result = addSeance($conn, $_POST);
        echo json_encode($result);
    }
} else {
    log_debug("Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

$conn->close();
log_debug("Connexion fermée\n");