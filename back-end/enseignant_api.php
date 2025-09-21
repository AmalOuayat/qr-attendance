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
    // Si l'action est 'getFormateurs'
    if ($_GET['action'] == 'getFormateurs') {
        try {
            // Préparer la requête pour récupérer tous les formateurs
            $stmt = $conn->prepare("SELECT * FROM formateur");
            $stmt->execute();

            // Vérifier si des formateurs existent
            $formateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($formateurs) > 0) {
                // Retourner la liste des formateurs sous forme de JSON
                echo json_encode(['success' => true, 'formateurs' => $formateurs]);
            } else {
                // Si aucun formateur n'est trouvé
                echo json_encode(['success' => false, 'message' => 'Aucun formateur trouvé']);
            }
        } catch (PDOException $e) {
            // En cas d'erreur de requête SQL
            echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
        }
    }

    // Si l'action est 'addFormateur'
    if ($_GET['action'] == 'addFormateur') {
        // Récupérer les données envoyées en POST
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['nom'], $data['prenom'], $data['email'], $data['tel'], $data['password'])) {
            try {
                // Hasher le mot de passe avant de l'insérer
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

                // Préparer la requête pour insérer un nouveau formateur
                $stmt = $conn->prepare("INSERT INTO formateur (nom, prenom, email, tel, password) VALUES (:nom, :prenom, :email, :tel, :password)");
                $stmt->bindParam(':nom', $data['nom']);
                $stmt->bindParam(':prenom', $data['prenom']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':tel', $data['tel']);
                $stmt->bindParam(':password', $hashedPassword); // Utiliser la variable hashée

                $stmt->execute();

                // Récupérer l'ID du formateur ajouté
                $formateurId = $conn->lastInsertId();

                // Répondre avec les données du formateur ajouté
                echo json_encode(['success' => true, 'id' => $formateurId]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur de la base de données : ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        }
    }

    // Si l'action est 'updateFormateur'
    if ($_GET['action'] == 'updateFormateur') {
        // Récupérer l'ID du formateur à modifier
        $id = $_GET['id'];

        // Récupérer les données envoyées en PUT
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['nom'], $data['prenom'], $data['email'], $data['tel'])) {
            try {
                // Préparer la requête pour mettre à jour le formateur
                $stmt = $conn->prepare("UPDATE formateur SET nom = :nom, prenom = :prenom, email = :email, tel = :tel WHERE id = :id");
                $stmt->bindParam(':nom', $data['nom']);
                $stmt->bindParam(':prenom', $data['prenom']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':tel', $data['tel']);
                $stmt->bindParam(':id', $id);

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

    // Si l'action est 'deleteFormateur'
    if ($_GET['action'] == 'deleteFormateur') {
        // Récupérer l'ID du formateur à supprimer
        $id = $_GET['id'];

        try {
            // Préparer la requête pour supprimer le formateur
            $stmt = $conn->prepare("DELETE FROM formateur WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Vérifier si la suppression a réussi
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucun formateur trouvé avec cet ID.']);
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