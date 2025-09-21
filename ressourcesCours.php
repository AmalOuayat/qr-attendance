<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer l'ID de l'étudiant connecté
$etudiant_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressources de Cours</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Styles modernes pour l'interface */
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-color: #212529;
            --text-secondary: #6c757d;
            --success-color: #38b000;
            --danger-color: #d90429;
            --warning-color: #ffb700;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
        }

        .resources-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .resource-card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .resource-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .resource-card .content {
            padding: 1rem;
        }

        .resource-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .resource-card p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .resource-card a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .resource-card a:hover {
            background-color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .resources-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Ressources de Cours</h1>

        <div class="resources-container" id="resourcesContainer">
            <!-- Les ressources seront chargées ici par JavaScript -->
        </div>
    </div>

    <script>
        // Fonction pour charger les ressources de cours
        function chargerRessources() {
            fetch(`back-end/ressources_api.php?action=getRessources&etudiant_id=<?php echo $etudiant_id; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('resourcesContainer');
                        container.innerHTML = '';

                        data.ressources.forEach(ressource => {
                            const resourceCard = document.createElement('div');
                            resourceCard.classList.add('resource-card');

                            resourceCard.innerHTML = `
                                <img src="${ressource.image_url}" alt="${ressource.titre}">
                                <div class="content">
                                    <h3>${ressource.titre}</h3>
                                    <p>${ressource.description}</p>
                                    <a href="${ressource.fichier_url}" target="_blank">Télécharger</a>
                                </div>
                            `;
                            container.appendChild(resourceCard);
                        });
                    } else {
                        alert('Erreur de récupération des ressources : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert("Erreur lors de la récupération des ressources : " + error.message);
                });
        }

        // Charger les ressources lorsque la page est chargée
        window.onload = chargerRessources;
    </script>
</body>

</html>