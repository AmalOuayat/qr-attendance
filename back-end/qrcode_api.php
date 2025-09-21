<?php
/**
 * API de génération de QR Codes pour les séances
 * Ce fichier gère la récupération des séances en fonction de la date
 * et la génération des QR codes correspondants
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_presence_qr');

// Connexion à la base de données
function connect()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

// Fonction pour déterminer si une semaine est paire ou impaire
function estSemainePaire($date)
{
    // Trouver le numéro de la semaine dans l'année
    $numSemaine = date('W', strtotime($date));
    return $numSemaine % 2 === 0;
}

// Fonction pour récupérer les séances d'un jour spécifique
function getSeancesDuJour($conn, $date)
{
    // Déterminer le jour de la semaine en français
    $joursSemaine = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        0 => 'Dimanche'
    ];

    $jourSemaine = $joursSemaine[date('w', strtotime($date))];

    // Déterminer si la semaine est paire ou impaire
    $semainePaire = estSemainePaire($date);

    // Construction de la requête SQL pour récupérer les séances du jour
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
            WHERE 
                e.jour = '$jourSemaine'";

    $result = $conn->query($sql);

    // Vérifier si la requête a réussi
    if (!$result) {
        error_log("Erreur SQL: " . $conn->error);
        return [];
    }

    $seances = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Dans ce cas, on suppose que toutes les séances sont de type "chaque_semaine"
            // car la colonne frequence n'existe pas dans la table
            $inclureSeance = true;

            if ($inclureSeance) {
                // Ajouter la date réelle et formater les informations pour le QR code
                $row['date_reelle'] = $date;

                // Créer la chaîne pour le QR code
                $qrData = [
                    'seance_id' => $row['id_seance'],
                    'module' => $row['module_nom'],
                    'formateur' => $row['formateur_nom'] . ' ' . $row['formateur_prenom'],
                    'salle' => $row['salle_nom'],
                    'groupe' => $row['groupe_nom'],
                    'date' => $date,
                    'creneau' => $row['creneau']
                ];

                $row['qr_data'] = json_encode($qrData);
                $seances[] = $row;
            }
        }
    }

    return $seances;
}

// Point d'entrée de l'API
$conn = connect();
header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Traiter la requête
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Si une date est fournie, récupérer les séances pour cette date
    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $date = $_GET['date'];
        try {
            $seances = getSeancesDuJour($conn, $date);
            echo json_encode(['success' => true, 'seances' => $seances]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune date fournie']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

$conn->close();





