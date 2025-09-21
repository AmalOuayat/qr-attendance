<?php

function connect()
{
    try {
        // Connexion à la base de données MySQL avec PDO
        $pdo = new PDO("mysql:host=localhost;dbname=gestion_presence_qr", "root", ""); // Adapter les infos
        // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Retourner l'objet PDO
        return $pdo;
    } catch (PDOException $e) {
        // En cas d'erreur de connexion, afficher un message d'erreur et arrêter l'exécution
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
?>