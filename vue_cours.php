<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Séances de Formation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #99CDD8;
            /* Bleu clair */
            --secondary-color: #657166;
            /* Gris foncé */
            --accent-color: #9AEBE3;
            /* Vert d'eau */
            --light-bg: #FDE8D3;
            /* Beige clair */
            --border-color: #F3C3B2;
            /* Rose saumon */
            --text-color: #657166;
            /* Gris foncé pour texte */
            --text-secondary: #99CDD8;
            /* Bleu clair pour texte secondaire */
            --success-color: #9AEBE3;
            /* Vert d'eau pour succès */
            --danger-color: #F3C3B2;
            /* Rose saumon pour danger */
            --warning-color: #FDE8D3;
            /* Beige clair pour avertissement */
            --shadow: 0 4px 6px rgba(101, 113, 102, 0.15);
            /* Ombre basée sur le gris */
            --radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #ffffff;
            color: var(--text-color);
            line-height: 1.6;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }

        header h1 {
            font-size: 1.8rem;
            color: var(--secondary-color);
        }

        #user-info {
            color: var(--secondary-color);
            font-weight: 500;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }

        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .card-header {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            padding: 1rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        input,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(153, 205, 216, 0.2);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--secondary-color);
        }

        .btn-primary:hover {
            background-color: var(--text-secondary);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success-color);
            color: var(--secondary-color);
        }

        .btn-success:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #e9afa0;
            transform: translateY(-2px);
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--light-bg);
            font-weight: 600;
            color: var(--secondary-color);
        }

        tr:hover {
            background-color: rgba(153, 205, 216, 0.05);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            border: none;
            background: none;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: var(--radius);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .action-btn.edit {
            color: var(--warning-color);
        }

        .action-btn.delete {
            color: var(--danger-color);
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .hidden {
            display: none;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: rgba(154, 235, 227, 0.1);
            border: 1px solid var(--success-color);
            color: var(--secondary-color);
        }

        .alert-danger {
            background-color: rgba(243, 195, 178, 0.1);
            border: 1px solid var(--danger-color);
            color: var(--secondary-color);
        }

        .loading {
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(101, 113, 102, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .day-schedule {
            margin-bottom: 2rem;
        }

        .day-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .seance-card {
            background-color: white;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .seance-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(101, 113, 102, 0.2);
        }

        .seance-time {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .seance-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 0.5rem;
        }

        .seance-info {
            margin-right: 1rem;
            margin-bottom: 0.5rem;
        }

        .seance-info span {
            font-weight: 500;
            color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <header>
        <h1><i class="fas fa-calendar-alt"></i> Mes Séances de Formation</h1>
        <div id="user-info">
            <i class="fas fa-user-circle"></i> <span id="formateur-nom">Formateur</span>
        </div>
    </header>

    <main>
        <div id="alerts"></div>

        <!-- Planning des séances -->
        <div class="card">
            <div class="card-header">
                Mon planning hebdomadaire
            </div>
            <div class="card-body">
                <div id="planning-container">
                    <div class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Constantes
        const ID_FORMATEUR = 1; // ID du formateur connecté

        // Sélecteurs DOM
        const planningContainer = document.getElementById('planning-container');
        const alertsContainer = document.getElementById('alerts');
        const formateurNom = document.getElementById('formateur-nom');

        // État de l'application
        let formateur = { id: ID_FORMATEUR, nom: "Formateur" };
        let jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];

        // Fonction pour montrer une alerte
        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;

            alertsContainer.appendChild(alertDiv);

            // Supprimer l'alerte après 3 secondes
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Fonction pour formater l'heure
        function formatTime(timeStr) {
            return timeStr.substring(0, 5);
        }

        // Fonction pour formater la date
        function formatDate(dateStr) {
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            return new Date(dateStr).toLocaleDateString('fr-FR', options);
        }

        // Fonction pour charger le formateur
        async function loadFormateur() {
            try {
                const response = await fetch(`back-end/cours_api.php?action=load_formateur&id=${ID_FORMATEUR}`);
                const data = await response.json();

                if (data) {
                    formateur = data;
                    formateurNom.textContent = formateur.nom;
                }
            } catch (error) {
                console.error('Erreur lors du chargement du formateur:', error);
            }
        }

        // Fonction pour charger les séances du formateur
        async function loadSeances() {
            try {
                planningContainer.innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                    </div>
                `;

                const response = await fetch(`back-end/cours_api.php?action=load_seances&id_formateur=${ID_FORMATEUR}`);
                const seances = await response.json();

                // Organiser les séances par jour
                const seancesByDay = {};

                seances.forEach(seance => {
                    const dateObj = new Date(seance.date);
                    const jour = dateObj.toISOString().split('T')[0]; // Format YYYY-MM-DD

                    if (!seancesByDay[jour]) {
                        seancesByDay[jour] = [];
                    }

                    seancesByDay[jour].push(seance);
                });

                // Trier les jours
                const sortedDays = Object.keys(seancesByDay).sort();

                // Générer le HTML du planning
                if (sortedDays.length === 0) {
                    planningContainer.innerHTML = `
                        <div style="text-align: center; padding: 2rem; color: var(--text-color);">
                            Aucune séance planifiée
                        </div>
                    `;
                    return;
                }

                let planningHTML = '';

                sortedDays.forEach(jour => {
                    const dateObj = new Date(jour);
                    const jourSemaine = jours[dateObj.getDay()];
                    const dateFormatee = formatDate(jour);

                    planningHTML += `
                        <div class="day-schedule">
                            <div class="day-header">
                                ${dateFormatee}
                            </div>
                    `;

                    // Trier les séances par heure de début
                    seancesByDay[jour].sort((a, b) => a.heure_debut.localeCompare(b.heure_debut));

                    seancesByDay[jour].forEach(seance => {
                        planningHTML += `
                            <div class="seance-card">
                                <div class="seance-time">
                                    ${formatTime(seance.heure_debut)} - ${formatTime(seance.heure_fin)}
                                </div>
                                <div class="seance-details">
                                    <div class="seance-info">
                                        <span>Module:</span> ${seance.module_nom}
                                    </div>
                                    <div class="seance-info">
                                        <span>Groupe:</span> ${seance.groupe_nom}
                                    </div>
                                    <div class="seance-info">
                                        <span>Salle:</span> ${seance.salle_nom}
                                    </div>
                                    <div class="action-buttons">
                                        <button class="action-btn delete" data-id="${seance.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    planningHTML += `</div>`;
                });

                planningContainer.innerHTML = planningHTML;

                // Ajouter les écouteurs d'événements aux boutons de suppression
                document.querySelectorAll('.action-btn.delete').forEach(btn => {
                    btn.addEventListener('click', deleteSeance);
                });
            } catch (error) {
                console.error('Erreur lors du chargement des séances:', error);
                showAlert('Erreur lors du chargement des séances', 'danger');
                planningContainer.innerHTML = `
                    <div style="text-align: center; color: var(--danger-color); padding: 2rem;">
                        Erreur lors du chargement des séances
                    </div>
                `;
            }
        }

        // Fonction pour supprimer une séance
        function deleteSeance(e) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')) {
                const id = e.currentTarget.dataset.id;

                fetch('back-end/cours_api.php?action=delete_seance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Séance supprimée avec succès');
                            loadSeances();
                        } else {
                            showAlert(data.message || 'Erreur lors de la suppression de la séance', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression de la séance:', error);
                        showAlert('Erreur lors de la suppression de la séance', 'danger');
                    });
            }
        }

        // Initialiser l'application
        document.addEventListener('DOMContentLoaded', () => {
            loadFormateur();
            loadSeances();
        });
    </script>
</body>

</html>