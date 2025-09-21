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
    <title>Historique des Présences</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        /* Styles modernes avec la nouvelle palette de couleurs */
        :root {
            --primary-color: #99CDD8;
            --secondary-color: #657166;
            --accent-color: #9AEBE3;
            --light-bg: #FDE8D3;
            --border-color: #F3C3B2;
            --text-color: #657166;
            --text-secondary: #99CDD8;
            --success-color: #9AEBE3;
            --danger-color: #F3C3B2;
            --warning-color: #FDE8D3;
            --shadow: 0 4px 6px rgba(101, 113, 102, 0.15);
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem;
        }

        header {
            margin-bottom: 2rem;
        }

        h1 {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
        }

        h1::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -8px;
            width: 60px;
            height: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        .table-container {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(153, 205, 216, 0.2);
            transition: var(--transition);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            padding: 1.2rem 1.5rem;
            text-align: left;
        }

        th {
            background-color: rgba(153, 205, 216, 0.1);
            font-weight: 500;
            color: var(--secondary-color);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--primary-color);
        }

        tr:not(:last-child) td {
            border-bottom: 1px solid rgba(153, 205, 216, 0.1);
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background-color: rgba(153, 205, 216, 0.05);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .badge i {
            margin-right: 5px;
            font-size: 0.8rem;
        }

        .badge-success {
            background-color: rgba(154, 235, 227, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .badge-warning {
            background-color: rgba(253, 232, 211, 0.3);
            color: #D4A76A;
            border: 1px solid var(--warning-color);
        }

        .badge-danger {
            background-color: rgba(243, 195, 178, 0.2);
            color: #E07A5F;
            border: 1px solid var(--danger-color);
        }

        /* Animation pour les badges */
        .badge:hover {
            transform: translateY(-2px);
        }

        /* Affichage responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            th,
            td {
                padding: 1rem 0.75rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            .badge {
                padding: 0.3rem 0.6rem;
            }
        }

        /* Pour mobiles */
        @media (max-width: 576px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: var(--radius);
                padding: 0.5rem;
            }

            td {
                position: relative;
                padding-left: 40%;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
                min-height: 50px;
                border: none !important;
            }

            td:before {
                position: absolute;
                top: 0.75rem;
                left: 0.75rem;
                width: 35%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 500;
                color: var(--secondary-color);
            }

            td:nth-of-type(1):before {
                content: "Module";
            }

            td:nth-of-type(2):before {
                content: "Date";
            }

            td:nth-of-type(3):before {
                content: "Statut";
            }
        }

        /* Animation lors du chargement des données */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Effet de focus pour améliorer l'accessibilité */
        *:focus {
            outline: 3px solid rgba(153, 205, 216, 0.5);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Historique des Présences</h1>
        </header>

        <div class="table-container">
            <table id="presenceTable">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les présences seront chargées ici par JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Fonction pour charger l'historique des présences
        function chargerHistoriquePresences() {
            fetch(`back-end/Presences_etudiants_api.php?action=getHistoriquePresences&etudiant_id=<?php echo $etudiant_id; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.querySelector("#presenceTable tbody");
                        tbody.innerHTML = '';

                        data.presences.forEach((presence, index) => {
                            const newRow = document.createElement('tr');
                            newRow.classList.add('fade-in');
                            newRow.style.animationDelay = `${index * 0.05}s`;

                            // Définir les icônes selon le statut
                            let icon, statusText;
                            if (presence.status === 'present') {
                                icon = 'bi-check-circle-fill';
                                statusText = 'Présent';
                            } else if (presence.status === 'late') {
                                icon = 'bi-exclamation-triangle-fill';
                                statusText = 'En retard';
                            } else {
                                icon = 'bi-x-circle-fill';
                                statusText = 'Absent';
                            }

                            // Formater la date pour un affichage plus agréable
                            const date = new Date(presence.date_time);
                            const formattedDate = date.toLocaleDateString('fr-FR', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            newRow.innerHTML = `
                                <td>${presence.nom_module}</td>
                                <td>${formattedDate}</td>
                                <td>
                                    <span class="badge ${presence.status === 'present' ? 'badge-success' : presence.status === 'late' ? 'badge-warning' : 'badge-danger'}">
                                        <i class="bi ${icon}"></i> ${statusText}
                                    </span>
                                </td>
                            `;
                            tbody.appendChild(newRow);
                        });
                    } else {
                        const tbody = document.querySelector("#presenceTable tbody");
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 2rem;">
                                    <i class="bi bi-info-circle" style="font-size: 2rem; color: var(--primary-color);"></i>
                                    <p style="margin-top: 0.5rem;">${data.message || 'Aucune présence trouvée'}</p>
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    const tbody = document.querySelector("#presenceTable tbody");
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem;">
                                <i class="bi bi-exclamation-circle" style="font-size: 2rem; color: var(--danger-color);"></i>
                                <p style="margin-top: 0.5rem;">Erreur lors de la récupération des présences</p>
                            </td>
                        </tr>
                    `;
                });
        }

        // Charger l'historique des présences lorsque la page est chargée
        window.onload = chargerHistoriquePresences;
    </script>
</body>

</html>