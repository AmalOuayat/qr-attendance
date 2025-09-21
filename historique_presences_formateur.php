<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Présences - Formateur</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
            --radius: 8px;
            --transition: all 0.3s ease;
        }

        body {
            background-color: #fff;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: var(--shadow);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
        }

        .navbar-dark .navbar-toggler-icon {
            background-color: white;
        }

        .card {
            margin-bottom: 20px;
            border-radius: var(--radius);
            border: none;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(101, 113, 102, 0.2);
        }

        .card-header {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-top-left-radius: var(--radius) !important;
            border-top-right-radius: var(--radius) !important;
            font-weight: 500;
            padding: 12px 15px;
        }

        .stats-card {
            transition: var(--transition);
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .presence-present {
            color: var(--success-color);
            border-left: 3px solid var(--success-color);
        }

        .presence-absent {
            color: var(--danger-color);
            border-left: 3px solid var(--danger-color);
        }

        .presence-late {
            color: var(--warning-color);
            border-left: 3px solid var(--warning-color);
        }

        .history-item {
            border-left: 4px solid var(--border-color);
            padding-left: 15px;
            margin-bottom: 10px;
            background-color: rgba(253, 232, 211, 0.1);
            padding: 12px;
            border-radius: 0 var(--radius) var(--radius) 0;
        }

        .history-item.present {
            border-left-color: var(--success-color);
        }

        .history-item.absent {
            border-left-color: var(--danger-color);
        }

        .history-item.late {
            border-left-color: var(--warning-color);
        }

        .stats-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .loading {
            display: flex;
            justify-content: center;
            padding: 20px;
            color: var(--primary-color);
        }

        #studentsList {
            max-height: 500px;
            overflow-y: auto;
            border-radius: var(--radius);
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid rgba(153, 205, 216, 0.2);
            transition: var(--transition);
        }

        .list-group-item:hover {
            background-color: rgba(153, 205, 216, 0.1);
        }

        .list-group-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-select {
            border-radius: var(--radius);
            border-color: var(--border-color);
            padding: 10px;
            color: var(--text-color);
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(153, 205, 216, 0.25);
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-warning {
            color: var(--secondary-color) !important;
        }

        .text-muted {
            color: var(--secondary-color) !important;
            opacity: 0.7;
        }

        .progress {
            background-color: rgba(153, 205, 216, 0.1);
            border-radius: var(--radius);
            height: 10px;
        }

        .progress-bar.bg-success {
            background-color: var(--success-color) !important;
        }

        .progress-bar.bg-warning {
            background-color: var(--warning-color) !important;
        }

        .progress-bar.bg-danger {
            background-color: var(--danger-color) !important;
        }

        .border-bottom,
        .border-top {
            border-color: rgba(153, 205, 216, 0.3) !important;
        }

        .table {
            color: var(--text-color);
        }

        .table th {
            color: var(--text-secondary);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(153, 205, 216, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(153, 205, 216, 0.1);
        }

        h5,
        h6 {
            color: var(--secondary-color);
            font-weight: 600;
        }

        .spinner-border {
            color: var(--primary-color);
        }

        /* Cartes statistiques */
        .card.stats-card {
            border-radius: var(--radius);
            overflow: hidden;
        }

        .card.stats-card .card-body {
            background-color: white;
        }

        .card.stats-card.presence-present .progress-bar {
            background-color: var(--success-color);
        }

        .card.stats-card.presence-absent .progress-bar {
            background-color: var(--danger-color);
        }

        .card.stats-card.presence-late .progress-bar {
            background-color: var(--warning-color);
        }

        .card.stats-card i {
            margin-bottom: 10px;
        }

        .card.stats-card h3 {
            font-weight: bold;
            color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-calendar-check me-2"></i>
                Suivi des Présences
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Sélection du groupe -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i> Groupes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="loadingGroupes" class="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <select id="groupeSelect" class="form-select mb-3" style="display: none;">
                            <option value="">Sélectionnez un groupe</option>
                        </select>

                        <div id="groupeInfo" class="mt-3" style="display: none;">
                            <h6 class="border-bottom pb-2">Statistiques du groupe</h6>
                            <div id="loadingGroupStats" class="loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                            <div id="groupeStatsContent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des étudiants -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-graduate me-2"></i> Étudiants
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="loadingEtudiants" class="loading" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <div id="studentsList" class="list-group">
                            <p class="text-muted text-center">Veuillez sélectionner un groupe</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails de l'étudiant -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>
                            <span id="studentName">Détails de présence</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="studentDetails">
                            <p class="text-muted text-center">Veuillez sélectionner un étudiant pour voir les détails
                            </p>
                        </div>

                        <div id="loadingDetails" class="loading" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>

                        <div id="studentStatsSection" style="display: none;">
                            <h5 class="border-bottom pb-2 mt-4">Statistiques globales</h5>
                            <div class="stats-container mb-3" id="globalStats"></div>

                            <h5 class="border-bottom pb-2 mt-4">Statistiques par module</h5>
                            <div id="moduleStats"></div>

                            <h5 class="border-bottom pb-2 mt-4">Historique des présences</h5>
                            <div id="presenceHistory"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // URLs de l'API
            const API_URL = 'back-end/presences_formateur_api.php';

            // Éléments DOM
            const groupeSelect = document.getElementById('groupeSelect');
            const studentsList = document.getElementById('studentsList');
            const studentName = document.getElementById('studentName');
            const studentDetails = document.getElementById('studentDetails');
            const studentStatsSection = document.getElementById('studentStatsSection');
            const globalStats = document.getElementById('globalStats');
            const moduleStats = document.getElementById('moduleStats');
            const presenceHistory = document.getElementById('presenceHistory');
            const groupeInfo = document.getElementById('groupeInfo');
            const groupeStatsContent = document.getElementById('groupeStatsContent');

            // Loaders
            const loadingGroupes = document.getElementById('loadingGroupes');
            const loadingEtudiants = document.getElementById('loadingEtudiants');
            const loadingDetails = document.getElementById('loadingDetails');
            const loadingGroupStats = document.getElementById('loadingGroupStats');

            // Charger les groupes au chargement de la page
            loadGroupes();

            // Event listeners
            groupeSelect.addEventListener('change', function () {
                const groupeId = this.value;
                if (groupeId) {
                    loadEtudiants(groupeId);
                    loadGroupeStats(groupeId);
                    groupeInfo.style.display = 'block';
                } else {
                    studentsList.innerHTML = '<p class="text-muted text-center">Veuillez sélectionner un groupe</p>';
                    groupeInfo.style.display = 'none';
                }

                // Réinitialiser la sélection d'étudiant
                resetStudentDetails();
            });

            // Fonction pour charger les groupes
            function loadGroupes() {
                loadingGroupes.style.display = 'flex';
                groupeSelect.style.display = 'none';

                fetch(`${API_URL}?action=getGroupes`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            populateGroupes(data.groupes);
                        } else {
                            showError('Impossible de charger les groupes: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des groupes:', error);
                        showError('Erreur de connexion au serveur');
                    })
                    .finally(() => {
                        loadingGroupes.style.display = 'none';
                        groupeSelect.style.display = 'block';
                    });
            }

            // Fonction pour charger les étudiants d'un groupe
            function loadEtudiants(groupeId) {
                loadingEtudiants.style.display = 'flex';
                studentsList.innerHTML = '';

                fetch(`${API_URL}?action=getEtudiants&groupeId=${groupeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            populateEtudiants(data.etudiants);
                        } else {
                            showError('Impossible de charger les étudiants: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des étudiants:', error);
                        showError('Erreur de connexion au serveur');
                    })
                    .finally(() => {
                        loadingEtudiants.style.display = 'none';
                    });
            }

            // Fonction pour charger les statistiques d'un groupe
            function loadGroupeStats(groupeId) {
                loadingGroupStats.style.display = 'flex';
                groupeStatsContent.innerHTML = '';

                fetch(`${API_URL}?action=getStatistiquesGroupe&groupeId=${groupeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayGroupStats(data.statistiques);
                        } else {
                            showError('Impossible de charger les statistiques du groupe: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des statistiques:', error);
                        showError('Erreur de connexion au serveur');
                    })
                    .finally(() => {
                        loadingGroupStats.style.display = 'none';
                    });
            }

            // Fonction pour charger les détails d'un étudiant
            function loadStudentDetails(etudiantId, nom, prenom) {
                studentName.textContent = `${prenom} ${nom}`;
                studentDetails.innerHTML = '';
                loadingDetails.style.display = 'flex';
                studentStatsSection.style.display = 'none';

                // Charger les statistiques de présence et l'historique
                Promise.all([
                    fetch(`${API_URL}?action=getStatistiquesPresence&etudiantId=${etudiantId}`).then(res => res.json()),
                    fetch(`${API_URL}?action=getHistoriquePresences&etudiantId=${etudiantId}`).then(res => res.json())
                ])
                    .then(([statsData, historyData]) => {
                        if (statsData.success && historyData.success) {
                            displayStudentStats(statsData.statistiques);
                            displayPresenceHistory(historyData.historique);
                            studentStatsSection.style.display = 'block';
                        } else {
                            let message = '';
                            if (!statsData.success) message += 'Erreur de statistiques: ' + statsData.message + '\n';
                            if (!historyData.success) message += 'Erreur d\'historique: ' + historyData.message;
                            showError(message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des détails:', error);
                        showError('Erreur de connexion au serveur');
                    })
                    .finally(() => {
                        loadingDetails.style.display = 'none';
                    });
            }

            // Fonction pour peupler le sélecteur de groupes
            function populateGroupes(groupes) {
                groupeSelect.innerHTML = '<option value="">Sélectionnez un groupe</option>';

                groupes.forEach(groupe => {
                    const option = document.createElement('option');
                    option.value = groupe.id;
                    option.textContent = groupe.nom;
                    groupeSelect.appendChild(option);
                });
            }

            // Fonction pour peupler la liste des étudiants
            function populateEtudiants(etudiants) {
                studentsList.innerHTML = '';

                if (etudiants.length === 0) {
                    studentsList.innerHTML = '<p class="text-muted text-center">Aucun étudiant dans ce groupe</p>';
                    return;
                }

                etudiants.forEach(etudiant => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user me-2"></i>
                                ${etudiant.prenom} ${etudiant.nom}
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    `;

                    item.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Désélectionner tous les étudiants
                        document.querySelectorAll('#studentsList a').forEach(el => {
                            el.classList.remove('active');
                        });

                        // Sélectionner cet étudiant
                        this.classList.add('active');

                        // Charger les détails
                        loadStudentDetails(etudiant.id, etudiant.nom, etudiant.prenom);
                    });

                    studentsList.appendChild(item);
                });
            }

            // Fonction pour afficher les statistiques du groupe
            function displayGroupStats(stats) {
                groupeStatsContent.innerHTML = '';

                if (stats.length === 0) {
                    groupeStatsContent.innerHTML = '<p class="text-muted">Aucune statistique disponible</p>';
                    return;
                }

                // Calcul des stats globales du groupe
                let totalPresents = 0;
                let totalAbsents = 0;
                let totalRetards = 0;
                let totalSeances = 0;

                stats.forEach(stat => {
                    totalPresents += parseInt(stat.presents || 0);
                    totalAbsents += parseInt(stat.absents || 0);
                    totalRetards += parseInt(stat.retards || 0);
                    totalSeances += parseInt(stat.total || 0);
                });

                // Afficher les stats globales
                const statsDiv = document.createElement('div');
                statsDiv.className = 'mb-3';
                statsDiv.innerHTML = `
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: ${totalSeances ? (totalPresents / totalSeances * 100) : 0}%" 
                             aria-valuenow="${totalPresents}" aria-valuemin="0" aria-valuemax="${totalSeances}">
                            ${totalPresents} présents
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: ${totalSeances ? (totalRetards / totalSeances * 100) : 0}%" 
                             aria-valuenow="${totalRetards}" aria-valuemin="0" aria-valuemax="${totalSeances}">
                            ${totalRetards} retards
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: ${totalSeances ? (totalAbsents / totalSeances * 100) : 0}%" 
                             aria-valuenow="${totalAbsents}" aria-valuemin="0" aria-valuemax="${totalSeances}">
                            ${totalAbsents} absents
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Total: ${totalSeances} ${totalSeances > 1 ? 'séances' : 'séance'}</span>
                        <span>Taux de présence: ${totalSeances ? Math.round((totalPresents / totalSeances) * 100) : 0}%</span>
                    </div>
                `;

                groupeStatsContent.appendChild(statsDiv);

                // Ajouter le top 3 des étudiants les plus absents
                const sortedStats = [...stats].sort((a, b) =>
                    (parseInt(b.absents || 0) / parseInt(b.total || 1)) -
                    (parseInt(a.absents || 0) / parseInt(a.total || 1))
                );

                const topAbsentTitle = document.createElement('h6');
                topAbsentTitle.className = 'mt-3 border-top pt-2';
                topAbsentTitle.textContent = 'Top absences';
                groupeStatsContent.appendChild(topAbsentTitle);

                const topAbsentsList = document.createElement('ul');
                topAbsentsList.className = 'list-unstyled small';

                for (let i = 0; i < Math.min(3, sortedStats.length); i++) {
                    const student = sortedStats[i];
                    if (parseInt(student.absents || 0) > 0) {
                        const item = document.createElement('li');
                        item.className = 'd-flex justify-content-between';
                        item.innerHTML = `
                            <span>${student.prenom} ${student.nom}</span>
                            <span class="text-danger">${student.absents} absence${student.absents > 1 ? 's' : ''}</span>
                        `;
                        topAbsentsList.appendChild(item);
                    }
                }

                if (topAbsentsList.children.length === 0) {
                    topAbsentsList.innerHTML = '<li class="text-muted">Aucune absence enregistrée</li>';
                }

                groupeStatsContent.appendChild(topAbsentsList);
            }

            // Fonction pour afficher les statistiques d'un étudiant
            function displayStudentStats(statistics) {
                const global = statistics.global;
                const modules = statistics.par_module;

                // Statistiques globales
                globalStats.innerHTML = '';

                if (!global || (!global.total || parseInt(global.total) === 0)) {
                    globalStats.innerHTML = '<p class="text-muted">Aucune statistique disponible</p>';
                } else {
                    // Calculer les pourcentages
                    const total = parseInt(global.total);
                    const presents = parseInt(global.presents || 0);
                    const absents = parseInt(global.absents || 0);
                    const retards = parseInt(global.retards || 0);

                    const presentPercent = Math.round((presents / total) * 100);
                    const absentPercent = Math.round((absents / total) * 100);
                    const retardPercent = Math.round((retards / total) * 100);

                    // Carte pour les présences
                    const presentCard = createStatCard('Présent', presents, total, presentPercent, 'presence-present', 'fa-check-circle');
                    globalStats.appendChild(presentCard);

                    // Carte pour les absences
                    const absentCard = createStatCard('Absent', absents, total, absentPercent, 'presence-absent', 'fa-times-circle');
                    globalStats.appendChild(absentCard);

                    // Carte pour les retards
                    const retardCard = createStatCard('En retard', retards, total, retardPercent, 'presence-late', 'fa-clock');
                    globalStats.appendChild(retardCard);
                }

                // Statistiques par module
                moduleStats.innerHTML = '';

                if (!modules || modules.length === 0) {
                    moduleStats.innerHTML = '<p class="text-muted">Aucune statistique par module disponible</p>';
                } else {
                    const table = document.createElement('table');
                    table.className = 'table table-striped table-hover';
                    table.innerHTML = `
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th class="text-center">Présences</th>
                                <th class="text-center">Retards</th>
                                <th class="text-center">Absences</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="moduleStatsBody"></tbody>
                    `;

                    const tbody = table.querySelector('#moduleStatsBody');

                    modules.forEach(module => {
                        const total = parseInt(module.total);
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${module.module_nom}</td>
                            <td class="text-center text-success">${module.presents} (${Math.round((module.presents / total) * 100)}%)</td>
                            <td class="text-center text-warning">${module.retards} (${Math.round((module.retards / total) * 100)}%)</td>
                            <td class="text-center text-danger">${module.absents} (${Math.round((module.absents / total) * 100)}%)</td>
                            <td class="text-center">${total}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    moduleStats.appendChild(table);
                }
            }

            // Fonction pour afficher l'historique des présences
            function displayPresenceHistory(history) {
                presenceHistory.innerHTML = '';

                if (!history || history.length === 0) {
                    presenceHistory.innerHTML = '<p class="text-muted">Aucun historique de présence disponible</p>';
                    return;
                }

                history.forEach(entry => {
                    const dateTime = new Date(entry.date_time);
                    const dateDebut = new Date(entry.date_debut);

                    let statusIcon, statusClass, statusText;

                    switch (entry.status) {
                        case 'present':
                            statusIcon = 'fa-check-circle';
                            statusClass = 'present';
                            statusText = 'Présent';
                            break;
                        case 'absent':
                            statusIcon = 'fa-times-circle';
                            statusClass = 'absent';
                            statusText = 'Absent';
                            break;
                        case 'late':
                            statusIcon = 'fa-clock';
                            statusClass = 'late';
                            statusText = 'En retard';
                            break;
                        default:
                            statusIcon = 'fa-question-circle';
                            statusClass = '';
                            statusText = 'Inconnu';
                    }

                    const historyItem = document.createElement('div');
                    historyItem.className = `history-item ${statusClass} mb-3`;
                    historyItem.innerHTML = `
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas ${statusIcon} me-2 ${entry.status === 'present' ? 'text-success' : entry.status === 'absent' ? 'text-danger' : 'text-warning'}"></i>
                            <strong>${statusText}</strong>
                            <span class="ms-auto text-muted small">
                                ${formatDate(dateTime)}
                            </span>
                        </div>
                        <div class="small">
                            <div><strong>Module:</strong> ${entry.module_nom}</div>
                            <div><strong>Date de séance:</strong> ${formatDate(dateDebut)}</div>
                            <div><strong>Salle:</strong> ${entry.salle_nom}</div>
                        </div>
                    `;

                    presenceHistory.appendChild(historyItem);
                });
            }

            // Fonction utilitaire pour créer une carte de statistique
            function createStatCard(title, value, total, percent, colorClass, icon) {
                const card = document.createElement('div');
                card.className = `card stats-card ${colorClass}`;
                card.style.width = '32%';

                card.innerHTML = `
                    <div class="card-body text-center p-3">
                        <div class="mb-2">
                            <i class="fas ${icon} fa-2x"></i>
                        </div>
                        <h3 class="mb-0">${value}</h3>
                        <div class="text-muted small">${title}</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: ${percent}%;" 
                                 aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-muted small mt-1">${percent}%</div>
                    </div>
                `;

                return card;
            }

            // Fonction pour réinitialiser les détails de l'étudiant
            function resetStudentDetails() {
                studentName.textContent = 'Détails de présence';
                studentDetails.innerHTML = '<p class="text-muted text-center">Veuillez sélectionner un étudiant pour voir les détails</p>';
                studentStatsSection.style.display = 'none';
            }

            // Fonction pour formater une date
            function formatDate(date) {
                const options = {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('fr-FR', options);
            }

            // Fonction pour afficher une erreur
            function showError(message) {
                console.error(message);
                // Ici on pourrait ajouter un toast ou une alerte
            }
        });
    </script>
</body>

</html>