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
    <title>Cours Suivis</title>
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

        .table-container {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--light-bg);
            font-weight: 500;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: var(--success-color);
            color: white;
        }

        .badge-warning {
            background-color: var(--warning-color);
            color: white;
        }

        .badge-danger {
            background-color: var(--danger-color);
            color: white;
        }

        @media (max-width: 768px) {

            th,
            td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Cours Suivis</h1>

        <div class="table-container">
            <table id="coursTable">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Formateur</th>
                        <th>Salle</th>
                        <th>Date et Heure</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les cours seront chargés ici par JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Fonction pour charger les cours de l'étudiant
        function chargerCours() {
            fetch(`back-end/coursSuivis_api.php?action=getCoursEtudiant&etudiant_id=<?php echo $etudiant_id; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.querySelector("#coursTable tbody");
                        tbody.innerHTML = '';

                        data.cours.forEach(cours => {
                            const newRow = document.createElement('tr');
                            newRow.innerHTML = `
                                <td>${cours.nom_module}</td>
                                <td>${cours.nom_formateur} ${cours.prenom_formateur}</td>
                                <td>${cours.nom_salle}</td>
                                <td>${cours.date_debut}</td>
                                <td><span class="badge ${cours.status === 'Planifiée' ? 'badge-success' : cours.status === 'En cour' ? 'badge-warning' : 'badge-danger'}">${cours.status}</span></td>
                            `;
                            tbody.appendChild(newRow);
                        });
                    } else {
                        alert('Erreur de récupération des cours : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert("Erreur lors de la récupération des cours : " + error.message);
                });
        }

        // Charger les cours lorsque la page est chargée
        window.onload = chargerCours;
    </script>
</body>

</html>