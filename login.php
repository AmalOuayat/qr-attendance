<?php
session_start();

// Configuration de la base de données
$host = "localhost";
$dbname = "gestion_presence_qr"; // Nom de la BD basé sur votre dump SQL
$username = "root";
$password = "";

// Message d'erreur
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Connexion à la base de données
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Vérifier d'abord si c'est un admin
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            // L'utilisateur est un admin
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'admin';

            header("Location: adminSpace.php");
            exit();
        } else {
            // Vérifier si c'est un formateur
            $stmt = $conn->prepare("SELECT * FROM formateur WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password === $user['password']) {
                // L'utilisateur est un formateur
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = 'formateur';

                header("Location: formateurSpace.php");
                exit();
            } else {
                // Vérifier si c'est un étudiant
                $stmt = $conn->prepare("SELECT * FROM etudiant WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $password === $user['password']) {
                    // L'utilisateur est un étudiant
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nom'] = $user['nom'];
                    $_SESSION['prenom'] = $user['prenom'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['cne'] = $user['cne'];
                    $_SESSION['id_groupe'] = $user['id_groupe'];
                    $_SESSION['role'] = 'etudiant';

                    header("Location: etudiantSpace.php");
                    exit();
                } else {
                    // Authentification échouée
                    $error_message = "Email ou mot de passe incorrect.";
                }
            }
        }
    } catch (PDOException $e) {
        $error_message = "Erreur de connexion: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Connexion</title>
    <style>
        /* Styles pour les messages d'erreur */
        .error-message {
            color: #ff3860;
            background-color: rgba(255, 56, 96, 0.1);
            border-left: 4px solid #ff3860;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Connexion</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" id="loginForm">
            <label for="email">Email :</label>
            <div class="input-container">
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
            </div>
            <br>
            <label for="password">Mot de passe :</label>
            <div class="input-container">
                <i class="bi bi-lock"></i>
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
            </div>
            <br>
            <button type="submit">Se connecter</button>
        </form>
        <a href="forgot-password.php" class="forgot-password">Mot de passe oublié ?</a>
    </div>
</body>

</html>