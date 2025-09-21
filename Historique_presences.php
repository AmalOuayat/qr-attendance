<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Présences</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin-top: 30px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #3498db;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px;
            font-weight: bold;
        }

        .form-control,
        .form-select {
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .table th {
            background-color: #f1f1f1;
        }

        .badge {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        .status-present {
            background-color: #2ecc71;
        }

        .status-absent {
            background-color: #e74c3c;
        }

        .status-retard {
            background-color: #f39c12;
        }

        .status-justifie {
            background-color: #9b59b6;
        }

        .seance-card {
            cursor: pointer;
            transition: all 0.3s;
        }

        .seance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .seance-active {
            border: 2px solid #3498db;
            transform: translateY(-5px);
        }

        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #7f8c8d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Gestion des Présences</h1>

        <div class="row">
            <!-- Filtres -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-filter me-2"></i> Filtres
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="groupeSelect" class="form-label">Groupe</label>
                            <select class="form-select" id="groupeSelect">
                                <option value="">Sélectionnez un groupe</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dateSelect" class="form-label">Date</label>
                            <input type="date" class="form-control" id="dateSelect" value="">
                        </div>
                        <button class="btn btn-primary w-100" id="searchBtn">
                            <i class="fas fa-search me-2"></i> Rechercher
                        </button>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="card mt-3" id="statsCard" style="display: none;">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i> Statistiques
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Présents:</span>
                            <span id="statsPresent" class="badge status-present">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Absents:</span>
                            <span id="statsAbsent" class="badge status-absent">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Retards:</span>
                            <span id="statsRetard" class="badge status-retard">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Justifiés:</span>
                            <span id="statsJustifie" class="badge status-justifie">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Séances -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-2"></i> Séances
                    </div>
                    <div class="card-body">
                        <div class="loader" id="seancesLoader">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement des séances...</p>
                        </div>
                        <div id="seancesContainer"></div>
                    </div>
                </div>

                <!-- Liste des étudiants -->
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fas fa-users me-2"></i> Liste des étudiants
                        <span id="seanceTitle" class="float-end"></span>
                    </div>
                    <div class="card-body">
                        <div class="loader" id="etudiantsLoader">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement des étudiants...</p>
                        </div>
                        <div id="etudiantsContainer">
                            <div class="no-data">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <h4>Sélectionnez un groupe et une date pour voir les séances</h4>
                                <p>Puis cliquez sur une séance pour afficher la liste des étudiants</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Variables globales
            let currentModuleId = null;

            // Définir la date du jour comme valeur par défaut
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById('dateSelect').value = formattedDate;

            // Charger les groupes au démarrage
            loadGroupes();

            // Event listeners
            document.getElementById('searchBtn').addEventListener('click', searchSeances);

            // Fonctions
            function loadGroupes() {
                fetch('back-end/presences_api.php?action=getGroupes')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const select = document.getElementById('groupeSelect');
                            data.groupes.forEach(groupe => {
                                const option = document.createElement('option');
                                option.value = groupe.id;
                                option.textContent = groupe.nom;
                                select.appendChild(option);
                            });
                        } else {
                            showError("Erreur lors du chargement des groupes: " + data.message);
                        }
                    })
                    .catch(error => {
                        showError("Erreur de connexion au serveur: " + error);
                    });
            }

            function searchSeances() {
                const groupeId = document.getElementById('groupeSelect').value;
                const date = document.getElementById('dateSelect').value;

                if (!groupeId) {
                    alert("Veuillez sélectionner un groupe");
                    return;
                }

                if (!date) {
                    alert("Veuillez sélectionner une date");
                    return;
                }

                // Afficher le loader
                document.getElementById('seancesLoader').style.display = 'block';
                document.getElementById('seancesContainer').innerHTML = '';
                document.getElementById('etudiantsContainer').innerHTML = '<div class="no-data"><i class="fas fa-info-circle fa-3x mb-3"></i><h4>Sélectionnez une séance pour afficher les étudiants</h4></div>';
                document.getElementById('seanceTitle').textContent = '';

                fetch(`back-end/presences_api.php?action=getSeances&groupeId=${groupeId}&date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        // Cacher le loader
                        document.getElementById('seancesLoader').style.display = 'none';

                        if (data.success) {
                            if (data.seances.length === 0) {
                                document.getElementById('seancesContainer').innerHTML = `
                                    <div class="no-data">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <h4>Aucune séance trouvée</h4>
                                        <p>Il n'y a pas de séances programmées pour ce groupe à cette date</p>
                                    </div>
                                `;
                            } else {
                                renderSeances(data.seances);
                            }
                        } else {
                            showError("Erreur lors du chargement des séances: " + data.message);
                        }
                    })
                    .catch(error => {
                        document.getElementById('seancesLoader').style.display = 'none';
                        showError("Erreur de connexion au serveur: " + error);
                    });
            }

            function renderSeances(seances) {
                const container = document.getElementById('seancesContainer');
                container.innerHTML = '';

                const row = document.createElement('div');
                row.className = 'row';

                seances.forEach(seance => {
                    // Formater les dates
                    const startTime = new Date(seance.date_debut).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    const endTime = new Date(seance.date_fin).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

                    // Déterminer la classe de statut
                    let statusClass = '';
                    let statusText = '';

                    switch (seance.status) {
                        case 'terminee':
                            statusClass = 'status-present';
                            statusText = 'Terminée';
                            break;
                        case 'en_cours':
                            statusClass = 'status-retard';
                            statusText = 'En cours';
                            break;
                        case 'annulee':
                            statusClass = 'status-absent';
                            statusText = 'Annulée';
                            break;
                        default:
                            statusClass = 'bg-secondary';
                            statusText = 'Planifiée';
                    }

                    const col = document.createElement('div');
                    col.className = 'col-md-6 mb-3';
                    col.innerHTML = `
                        <div class="card seance-card h-100" data-id="${seance.id}" data-module-id="${seance.module_id || '0'}">
                            <div class="card-body">
                                <h5 class="card-title">${seance.module_nom}</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge ${statusClass}">${statusText}</span>
                                    <span class="text-muted">${startTime} - ${endTime}</span>
                                </div>
                                <p class="card-text mb-1">
                                    <i class="fas fa-user-tie me-2"></i> ${seance.formateur_prenom} ${seance.formateur_nom}
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-door-open me-2"></i> ${seance.salle_nom}
                                </p>
                            </div>
                        </div>
                    `;

                    row.appendChild(col);

                    // Ajouter un écouteur d'événement pour charger les étudiants
                    col.querySelector('.seance-card').addEventListener('click', function () {
                        // Supprimer la classe active de toutes les séances
                        document.querySelectorAll('.seance-card').forEach(card => {
                            card.classList.remove('seance-active');
                        });

                        // Ajouter la classe active à la séance sélectionnée
                        this.classList.add('seance-active');

                        const seanceId = this.getAttribute('data-id');
                        const moduleId = this.getAttribute('data-module-id');
                        currentModuleId = moduleId;

                        // Mettre à jour le titre de la séance
                        document.getElementById('seanceTitle').textContent = seance.module_nom;

                        // Charger les étudiants
                        loadEtudiants(seanceId);
                    });
                });

                container.appendChild(row);
            }

            function loadEtudiants(seanceId) {
                const groupeId = document.getElementById('groupeSelect').value;

                // Afficher le loader
                document.getElementById('etudiantsLoader').style.display = 'block';
                document.getElementById('etudiantsContainer').innerHTML = '';

                fetch(`back-end/presences_api.php?action=getEtudiants&groupeId=${groupeId}&seanceId=${seanceId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Cacher le loader
                        document.getElementById('etudiantsLoader').style.display = 'none';

                        if (data.success) {
                            if (data.etudiants.length === 0) {
                                document.getElementById('etudiantsContainer').innerHTML = `
                                    <div class="no-data">
                                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                                        <h4>Aucun étudiant trouvé</h4>
                                        <p>Il n'y a pas d'étudiants dans ce groupe</p>
                                    </div>
                                `;
                            } else {
                                renderEtudiants(data.etudiants, seanceId);
                                updateStats(data.etudiants);
                            }
                        } else {
                            showError("Erreur lors du chargement des étudiants: " + data.message);
                        }
                    })
                    .catch(error => {
                        document.getElementById('etudiantsLoader').style.display = 'none';
                        showError("Erreur de connexion au serveur: " + error);
                    });
            }

            function renderEtudiants(etudiants, seanceId) {
                const container = document.getElementById('etudiantsContainer');
                container.innerHTML = '';

                const table = document.createElement('table');
                table.className = 'table table-striped table-hover';

                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>CNE</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                `;

                const tbody = table.querySelector('tbody');

                etudiants.forEach(etudiant => {
                    const tr = document.createElement('tr');

                    // Déterminer la classe et le texte du statut
                    let statusClass = '';
                    let statusText = '';

                    switch (etudiant.status) {
                        case 'present':
                            statusClass = 'status-present';
                            statusText = 'Présent';
                            break;
                        case 'absent':
                            statusClass = 'status-absent';
                            statusText = 'Absent';
                            break;
                        case 'retard':
                            statusClass = 'status-retard';
                            statusText = 'Retard';
                            break;
                        case 'justifie':
                            statusClass = 'status-justifie';
                            statusText = 'Justifié';
                            break;
                    }

                    tr.innerHTML = `
                        <td>${etudiant.cne}</td>
                        <td>${etudiant.nom}</td>
                        <td>${etudiant.prenom}</td>
                        <td>${etudiant.email}</td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Modifier
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item status-update" data-status="present" href="#"><i class="fas fa-check text-success me-2"></i> Présent</a></li>
                                    <li><a class="dropdown-item status-update" data-status="absent" href="#"><i class="fas fa-times text-danger me-2"></i> Absent</a></li>
                                    <li><a class="dropdown-item status-update" data-status="retard" href="#"><i class="fas fa-clock text-warning me-2"></i> Retard</a></li>
                                    <li><a class="dropdown-item status-update" data-status="justifie" href="#"><i class="fas fa-file-alt text-purple me-2"></i> Justifié</a></li>
                                </ul>
                            </div>
                        </td>
                    `;

                    tbody.appendChild(tr);

                    // Ajouter les écouteurs d'événements pour la mise à jour du statut
                    tr.querySelectorAll('.status-update').forEach(link => {
                        link.addEventListener('click', function (e) {
                            e.preventDefault();
                            const status = this.getAttribute('data-status');
                            updatePresence(etudiant.id, seanceId, status);
                        });
                    });
                });

                container.appendChild(table);

                // Afficher le card des statistiques
                document.getElementById('statsCard').style.display = 'block';
            }

            function updatePresence(etudiantId, seanceId, status) {
                // Créer les données du formulaire
                const formData = new FormData();
                formData.append('etudiantId', etudiantId);
                formData.append('seanceId', seanceId);
                formData.append('status', status);
                formData.append('moduleId', currentModuleId);

                fetch('back-end/presences_api.php?action=updatePresence', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recharger les étudiants pour mettre à jour l'affichage
                            loadEtudiants(seanceId);
                        } else {
                            showError("Erreur lors de la mise à jour du statut: " + data.message);
                        }
                    })
                    .catch(error => {
                        showError("Erreur de connexion au serveur: " + error);
                    });
            }

            function updateStats(etudiants) {
                let present = 0;
                let absent = 0;
                let retard = 0;
                let justifie = 0;

                etudiants.forEach(etudiant => {
                    switch (etudiant.status) {
                        case 'present':
                            present++;
                            break;
                        case 'absent':
                            absent++;
                            break;
                        case 'retard':
                            retard++;
                            break;
                        case 'justifie':
                            justifie++;
                            break;
                    }
                });

                document.getElementById('statsPresent').textContent = present;
                document.getElementById('statsAbsent').textContent = absent;
                document.getElementById('statsRetard').textContent = retard;
                document.getElementById('statsJustifie').textContent = justifie;
            }

            function showError(message) {
                alert(message);
            }
        });
    </script>
</body>

</html>