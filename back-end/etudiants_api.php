<?php
include('api.php'); // Inclure votre fichier de connexion à la base de données

// Connexion à la base de données
$conn = connect();
header('Content-Type: application/json');

// Activer les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier l'existence de l'action
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Si l'action est 'getGroupes'
    if ($action == 'getGroupes') {
        try {
            // Préparer la requête pour récupérer tous les groupes
            $stmt = $conn->prepare("SELECT * FROM groupe");
            $stmt->execute();

            // Vérifier si des groupes existent
            $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($groupes) > 0) {
                // Retourner la liste des groupes sous forme de JSON
                echo json_encode(['success' => true, 'groupes' => $groupes]);
            } else {
                // Si aucun groupe n'est trouvé
                echo json_encode(['success' => false, 'message' => 'Aucun groupe trouvé']);
            }
        } catch (PDOException $e) {
            // En cas d'erreur de requête SQL
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }

    // Si l'action est 'getEtudiants'
    elseif ($action == 'getEtudiants') {
        $groupeId = $_GET['groupeId'];

        try {
            // Préparer la requête pour récupérer les étudiants du groupe
            $stmt = $conn->prepare("SELECT * FROM etudiant WHERE id_groupe = :groupeId");
            $stmt->bindParam(':groupeId', $groupeId);
            $stmt->execute();

            // Vérifier si des étudiants existent
            $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($etudiants) > 0) {
                // Retourner la liste des étudiants sous forme de JSON
                echo json_encode(['success' => true, 'etudiants' => $etudiants]);
            } else {
                // Si aucun étudiant n'est trouvé
                echo json_encode(['success' => false, 'message' => 'Aucun étudiant trouvé']);
            }
        } catch (PDOException $e) {
            // En cas d'erreur de requête SQL
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }

    // Si l'action est 'getEtudiant'
    elseif ($action == 'getEtudiant') {
        $id = $_GET['id'];

        try {
            // Préparer la requête pour récupérer les informations de l'étudiant
            $stmt = $conn->prepare("SELECT * FROM etudiant WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Vérifier si l'étudiant existe
            $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($etudiant) {
                // Retourner les informations de l'étudiant sous forme de JSON
                echo json_encode(['success' => true, 'etudiant' => $etudiant]);
            } else {
                // Si l'étudiant n'est pas trouvé
                echo json_encode(['success' => false, 'message' => 'Étudiant non trouvé']);
            }
        } catch (PDOException $e) {
            // En cas d'erreur de requête SQL
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }

    // Si l'action est 'ajouterEtudiant'
    elseif ($action == 'ajouterEtudiant') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['nom']) && isset($data['prenom']) && isset($data['cne']) && isset($data['email']) && isset($data['tel']) && isset($data['groupeId'])) {
            try {
                // Préparer la requête pour ajouter un étudiant
                $stmt = $conn->prepare("INSERT INTO etudiant (nom, prenom, cne, email, tel, id_groupe) VALUES (:nom, :prenom, :cne, :email, :tel, :groupeId)");
                $stmt->bindParam(':nom', $data['nom']);
                $stmt->bindParam(':prenom', $data['prenom']);
                $stmt->bindParam(':cne', $data['cne']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':tel', $data['tel']);
                $stmt->bindParam(':groupeId', $data['groupeId']);
                $stmt->execute();

                // Répondre avec un succès
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        }
    }

    // Si l'action est 'modifierEtudiant'
    elseif ($action == 'modifierEtudiant') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['id']) && isset($data['nom']) && isset($data['prenom']) && isset($data['cne']) && isset($data['email']) && isset($data['tel']) && isset($data['groupeId'])) {
            try {
                // Préparer la requête pour modifier un étudiant
                $stmt = $conn->prepare("UPDATE etudiant SET nom = :nom, prenom = :prenom, cne = :cne, email = :email, tel = :tel, id_groupe = :groupeId WHERE id = :id");
                $stmt->bindParam(':id', $data['id']);
                $stmt->bindParam(':nom', $data['nom']);
                $stmt->bindParam(':prenom', $data['prenom']);
                $stmt->bindParam(':cne', $data['cne']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':tel', $data['tel']);
                $stmt->bindParam(':groupeId', $data['groupeId']);
                $stmt->execute();

                // Répondre avec un succès
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        }
    }

    // Si l'action est 'supprimerEtudiant'
    elseif ($action == 'supprimerEtudiant') {
        $id = $_GET['id'];

        try {
            // Préparer la requête pour supprimer un étudiant
            $stmt = $conn->prepare("DELETE FROM etudiant WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Répondre avec un succès
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }

    // Si l'action n'est pas reconnue
    else {
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} else {
    // Si l'action n'est pas définie dans l'URL
    echo json_encode(['success' => false, 'message' => 'Aucune action spécifiée']);
}
?>