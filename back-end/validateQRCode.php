<?php
// validate_presence.php - API pour gérer le scan des QR codes et enregistrer les présences

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_presence_qr');

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']));
}

// Définir le fuseau horaire du Maroc
date_default_timezone_set('Africa/Casablanca');

header('Content-Type: application/json');

// Fonction pour calculer la distance entre deux points GPS
function distanceBetweenPoints($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371000; // Rayon de la Terre en mètres

    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

    return $angle * $earthRadius;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données JSON brutes
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Données QR manquantes ou invalides']);
        exit;
    }

    try {
        // Récupérer l'ID étudiant (par défaut 1 pour le développement)
        $etudiant_id = 1; // À remplacer par votre logique d'authentification

        // Récupérer les infos essentielles
        $seance_id = $data['seance_id'] ?? null;
        $module = $data['module'] ?? null;
        $dateStr = $data['date'] ?? null;
        $creneau = $data['creneau'] ?? null;

        if (!$seance_id) {
            throw new Exception('ID de séance manquant dans le QR code');
        }

        // Récupérer les infos complètes de la séance
        $stmt = $conn->prepare("SELECT s.*, c.id_module, s.status as seance_status 
                               FROM seance s 
                               JOIN cours c ON s.id_cours = c.id 
                               WHERE s.id = ?");
        $stmt->bind_param("i", $seance_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('Séance non trouvée');
        }

        $seance = $result->fetch_assoc();
        $module_id = $seance['id_module'];

        // Vérifier le statut de la séance
        if ($seance['seance_status'] === 'Annulée') {
            throw new Exception('Cette séance a été annulée, vous ne pouvez pas marquer votre présence');
        }

        // Déterminer le statut de présence (avec fuseau horaire Maroc)
        $now = new DateTime('now', new DateTimeZone('Africa/Casablanca'));
        $date_seance = new DateTime($seance['date_debut'], new DateTimeZone('Africa/Casablanca'));
        $date_seance_fin = new DateTime($seance['date_fin'], new DateTimeZone('Africa/Casablanca'));

        // Debug: Enregistrer les heures pour vérification
        error_log("Heure actuelle Rabat: " . $now->format('Y-m-d H:i:s'));
        error_log("Début séance: " . $date_seance->format('Y-m-d H:i:s'));
        error_log("Fin séance: " . $date_seance_fin->format('Y-m-d H:i:s'));

        // Initialiser le statut par défaut
        $status = 'absent';
        $gps_required = true;

        // Vérifier si la séance est en cours
        if ($now >= $date_seance && $now <= $date_seance_fin) {
            // Séance en cours
            $debut_limite = clone $date_seance;
            $debut_limite->add(new DateInterval('PT15M')); // 15 minutes de tolérance

            // Vérifier la géolocalisation
            if (empty($data['latitude']) || empty($data['longitude'])) {
                throw new Exception('La géolocalisation est requise pour enregistrer votre présence');
            }

            // Récupération des coordonnées de la salle
            $stmtSalle = $conn->prepare("SELECT latitude, longitude FROM salle WHERE id = ?");
            $stmtSalle->bind_param("i", $seance['id_salle']);
            $stmtSalle->execute();
            $resultSalle = $stmtSalle->get_result();
            $salle = $resultSalle->fetch_assoc();

            if (!$salle || is_null($salle['latitude'])) {
                throw new Exception('Configuration manquante: coordonnées GPS de la salle non définies');
            }

            // Calcul de la distance
            $distance = distanceBetweenPoints(
                $data['latitude'],
                $data['longitude'],
                $salle['latitude'],
                $salle['longitude']
            );

            // Vérification de la distance (50m max)
            $distanceSeuil = 50;
            if ($distance <= $distanceSeuil) {
                // Déterminer si présent ou en retard
                $status = ($now <= $debut_limite) ? 'present' : 'late';
            } else {
                throw new Exception('Vous devez être dans la salle pour scanner (distance: ' . round($distance, 2) . 'm)');
            }
        } elseif ($now > $date_seance_fin) {
            // Séance terminée
            $gps_required = false;
            $status = 'absent';
        } else {
            // Séance pas encore commencée
            throw new Exception('La séance n\'a pas encore commencé');
        }

        // Enregistrer la présence
        if ($gps_required) {
            $stmtInsert = $conn->prepare("INSERT INTO presence (id_etudiant, id_seance, date_time, status, id_module, latitude, longitude) 
                                         VALUES (?, ?, NOW(), ?, ?, ?, ?)");
            $stmtInsert->bind_param("iisiidd", $etudiant_id, $seance_id, $status, $module_id, $data['latitude'], $data['longitude']);
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO presence (id_etudiant, id_seance, date_time, status, id_module) 
                                         VALUES (?, ?, NOW(), ?, ?)");
            $stmtInsert->bind_param("iisi", $etudiant_id, $seance_id, $status, $module_id);
        }

        if ($stmtInsert->execute()) {
            $presence_id = $conn->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Statut de présence enregistré',
                'status' => $status,
                'presence_id' => $presence_id,
                'distance' => isset($distance) ? $distance : null
            ]);
        } else {
            throw new Exception('Erreur lors de l\'enregistrement: ' . $conn->error);
        }

    } catch (Exception $e) {
        error_log("Erreur: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}