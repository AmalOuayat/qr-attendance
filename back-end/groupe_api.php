<?php
include('api.php');

$conn = connect();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO `groupe`(`nom`) VALUES (:nom)");
        $stmt->execute(['nom' => $data['nom']]);
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } else if ($_GET['action'] == 'update') {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE `groupe` SET `nom` = :nom WHERE `id` = :id");
        $stmt->execute(['nom' => $data['nom'], 'id' => $data['id']]);
        echo json_encode(['success' => true, 'message' => 'Groupe mis à jour']);
    } else if ($_GET['action'] == 'delete') {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("DELETE FROM `groupe` WHERE `id` = :id");
        $stmt->execute(['id' => $data['id']]);
        echo json_encode(['success' => true, 'message' => 'Groupe supprimé']);
    } else if ($_GET['action'] == 'load') {
        $stmt = $conn->query("SELECT * FROM `groupe`");
        $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($groupes);
    }
}
?>