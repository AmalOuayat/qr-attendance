<?php
include('api.php'); // Assurez-vous que ce fichier contient la fonction `connect()` pour la connexion à la base de données

$conn = connect();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add_module') {
        // Ajouter un module
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO `module`(`nom`, `heure`, `id_formateur`, `id_salle`, `id_filiere`, `id_branche`, `id_groupe`) VALUES (:nom, :heure, :id_formateur, :id_salle, :id_filiere, :id_branche, :id_groupe)");
        $stmt->execute([
            'nom' => $data['nom'],
            'heure' => $data['heure'],
            'id_formateur' => $data['id_formateur'],
            'id_salle' => $data['id_salle'],
            'id_filiere' => $data['id_filiere'],
            'id_branche' => $data['id_branche'],
            'id_groupe' => $data['id_groupe']
        ]);
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } else if ($_GET['action'] == 'load_formateurs') {
        // Récupérer tous les formateurs
        $stmt = $conn->query("SELECT * FROM `formateur`");
        $formateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($formateurs);
    } else if ($_GET['action'] == 'load_filieres') {
        // Récupérer toutes les filières
        $stmt = $conn->query("SELECT * FROM `filiere`");
        $filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($filieres);
    } else if ($_GET['action'] == 'load_branches') {
        // Récupérer les branches en fonction de l'ID de la filière
        $id_filiere = $_GET['id_filiere'];
        $stmt = $conn->prepare("SELECT * FROM `branche` WHERE `id_filiere` = :id_filiere");
        $stmt->execute(['id_filiere' => $id_filiere]);
        $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($branches);
    } else if ($_GET['action'] == 'load_salles') {
        // Récupérer toutes les salles
        $stmt = $conn->query("SELECT * FROM `salle`");
        $salles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($salles);
    } else if ($_GET['action'] == 'load_groupes') {
        // Récupérer tous les groupes
        $stmt = $conn->query("SELECT * FROM `groupe`");
        $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($groupes);
    } else if ($_GET['action'] == 'load_modules') {
        // Récupérer tous les modules avec les noms des formateurs, filières, branches, salles et groupes
        $stmt = $conn->query("
            SELECT module.*, formateur.nom as formateur_nom, filiere.nom as filiere_nom, branche.nom as branche_nom, salle.nom as salle_nom, groupe.nom as groupe_nom
            FROM `module`
            JOIN `formateur` ON module.id_formateur = formateur.id
            JOIN `filiere` ON module.id_filiere = filiere.id
            JOIN `branche` ON module.id_branche = branche.id
            JOIN `salle` ON module.id_salle = salle.id
            JOIN `groupe` ON module.id_groupe = groupe.id
        ");
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($modules);
    }
}
?>