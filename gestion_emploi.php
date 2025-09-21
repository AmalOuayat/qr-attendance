<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Emplois du Temps</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        }

        body {
            background-color: #f9f9f9;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .schedule-container {
            margin-top: 30px;
        }

        .card {
            border-radius: var(--radius);
            border: none;
            box-shadow: var(--shadow);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-top-left-radius: var(--radius) !important;
            border-top-right-radius: var(--radius) !important;
            padding: 1rem;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--secondary-color);
            font-weight: 600;
            border-radius: var(--radius);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #8AD8D0;
            border-color: #8AD8D0;
            color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            border-radius: var(--radius);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #586158;
            border-color: #586158;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--radius);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-outline-danger {
            color: var(--danger-color);
            border-color: var(--danger-color);
            border-radius: var(--radius);
        }

        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            color: white;
            border-color: var(--danger-color);
        }

        .timetable {
            overflow-x: auto;
            border-radius: var(--radius);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-bordered {
            border: 1px solid rgba(153, 205, 216, 0.2);
        }

        .table th {
            min-width: 150px;
            background-color: var(--light-bg);
            color: var(--secondary-color);
            font-weight: 600;
            border-color: rgba(153, 205, 216, 0.3);
            padding: 1rem;
            text-align: center;
        }

        .table td {
            border-color: rgba(153, 205, 216, 0.3);
            padding: 0.75rem;
            vertical-align: top;
        }

        .table td:first-child {
            background-color: rgba(153, 205, 216, 0.1);
            font-weight: 600;
            color: var(--secondary-color);
        }

        .seance-card {
            margin-bottom: 5px;
            border-left: 4px solid var(--accent-color);
            border-radius: var(--radius);
            transition: all 0.3s ease;
        }

        .seance-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .seance-card .card-title {
            color: var(--secondary-color);
            font-weight: 600;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
        }

        .modal-content {
            border-radius: var(--radius);
            border: none;
            box-shadow: var(--shadow);
        }

        .form-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-select,
        .form-control {
            border-radius: var(--radius);
            border-color: rgba(153, 205, 216, 0.5);
            color: var(--secondary-color);
            padding: 0.6rem 1rem;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(154, 235, 227, 0.25);
        }

        .btn-close {
            color: white;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 1.5rem 1.5rem;
        }

        /* Customization for week days */
        .table th:nth-child(2) {
            /* Monday */
            background-color: rgba(153, 205, 216, 0.2);
        }

        .table th:nth-child(3) {
            /* Tuesday */
            background-color: rgba(153, 205, 216, 0.3);
        }

        .table th:nth-child(4) {
            /* Wednesday */
            background-color: rgba(153, 205, 216, 0.4);
        }

        .table th:nth-child(5) {
            /* Thursday */
            background-color: rgba(153, 205, 216, 0.5);
        }

        .table th:nth-child(6) {
            /* Friday */
            background-color: rgba(153, 205, 216, 0.6);
        }

        .table th:nth-child(7) {
            /* Saturday */
            background-color: rgba(153, 205, 216, 0.7);
        }
    </style>
</head>

<body>

    <div class="container-fluid schedule-container">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Gestion des Emplois du Temps</h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSeanceModal">
                    <i class="fas fa-plus"></i> Ajouter une séance
                </button>
            </div>
            <div class="card-body">
                <div class="timetable">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Horaire</th>
                                <th>Lundi</th>
                                <th>Mardi</th>
                                <th>Mercredi</th>
                                <th>Jeudi</th>
                                <th>Vendredi</th>
                                <th>Samedi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>8h30-11h00</strong></td>
                                <td id="Lundi-8h30-11h00">
                                    <!-- Les séances seront injectées ici par JavaScript -->
                                </td>
                                <td id="Mardi-8h30-11h00"></td>
                                <td id="Mercredi-8h30-11h00"></td>
                                <td id="Jeudi-8h30-11h00"></td>
                                <td id="Vendredi-8h30-11h00"></td>
                                <td id="Samedi-8h30-11h00"></td>
                            </tr>
                            <tr>
                                <td><strong>11h00-13h30</strong></td>
                                <td id="Lundi-11h00-13h30"></td>
                                <td id="Mardi-11h00-13h30"></td>
                                <td id="Mercredi-11h00-13h30"></td>
                                <td id="Jeudi-11h00-13h30"></td>
                                <td id="Vendredi-11h00-13h30"></td>
                                <td id="Samedi-11h00-13h30"></td>
                            </tr>
                            <tr>
                                <td><strong>13h30-16h00</strong></td>
                                <td id="Lundi-13h30-16h00"></td>
                                <td id="Mardi-13h30-16h00"></td>
                                <td id="Mercredi-13h30-16h00"></td>
                                <td id="Jeudi-13h30-16h00"></td>
                                <td id="Vendredi-13h30-16h00"></td>
                                <td id="Samedi-13h30-16h00"></td>
                            </tr>
                            <tr>
                                <td><strong>16h00-18h30</strong></td>
                                <td id="Lundi-16h00-18h30"></td>
                                <td id="Mardi-16h00-18h30"></td>
                                <td id="Mercredi-16h00-18h30"></td>
                                <td id="Jeudi-16h00-18h30"></td>
                                <td id="Vendredi-16h00-18h30"></td>
                                <td id="Samedi-16h00-18h30"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter une séance -->
    <div class="modal fade" id="addSeanceModal" tabindex="-1" aria-labelledby="addSeanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSeanceModalLabel">Ajouter une séance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="seanceForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="jour" class="form-label">Jour de la semaine</label>
                                <select class="form-select" id="jour" name="jour" required>
                                    <option value="">Sélectionner un jour</option>
                                    <option value="Lundi">Lundi</option>
                                    <option value="Mardi">Mardi</option>
                                    <option value="Mercredi">Mercredi</option>
                                    <option value="Jeudi">Jeudi</option>
                                    <option value="Vendredi">Vendredi</option>
                                    <option value="Samedi">Samedi</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="creneau" class="form-label">Créneau horaire</label>
                                <select class="form-select" id="creneau" name="creneau" required>
                                    <option value="">Sélectionner un créneau</option>
                                    <option value="8h30-11h00">8h30-11h00</option>
                                    <option value="11h00-13h30">11h00-13h30</option>
                                    <option value="13h30-16h00">13h30-16h00</option>
                                    <option value="16h00-18h30">16h00-18h30</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="module" class="form-label">Module</label>
                                <select class="form-select" id="module" name="id_module" required>
                                    <option value="">Sélectionner un module</option>
                                    <!-- Les modules seront injectés ici par JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="groupe" class="form-label">Groupe</label>
                                <select class="form-select" id="groupe" name="id_groupe" required>
                                    <option value="">Sélectionner un groupe</option>
                                    <!-- Les groupes seront injectés ici par JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="formateur" class="form-label">Formateur</label>
                                <select class="form-select" id="formateur" name="id_formateur" required>
                                    <option value="">Sélectionner un formateur</option>
                                    <!-- Les formateurs seront injectés ici par JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="salle" class="form-label">Salle</label>
                                <select class="form-select" id="salle" name="id_salle" required>
                                    <option value="">Sélectionner une salle</option>
                                    <!-- Les salles seront injectées ici par JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="frequence" class="form-label">Fréquence</label>
                                <select class="form-select" id="frequence" name="frequence" required>
                                    <option value="chaque_semaine">Chaque semaine</option>
                                    <option value="paire">Semaine paire</option>
                                    <option value="impaire">Semaine impaire</option>
                                </select>
                            </div>
                            <input type="hidden" id="seanceId" name="id">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="saveSeance">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edition d'une séance -->
    <div class="modal fade" id="editSeanceModal" tabindex="-1" aria-labelledby="editSeanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSeanceModalLabel">Modifier une séance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSeanceForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_jour" class="form-label">Jour de la semaine</label>
                                <select class="form-select" id="edit_jour" name="jour" required>
                                    <option value="Lundi">Lundi</option>
                                    <option value="Mardi">Mardi</option>
                                    <option value="Mercredi">Mercredi</option>
                                    <option value="Jeudi">Jeudi</option>
                                    <option value="Vendredi">Vendredi</option>
                                    <option value="Samedi">Samedi</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_creneau" class="form-label">Créneau horaire</label>
                                <select class="form-select" id="edit_creneau" name="creneau" required>
                                    <option value="8h30-11h00">8h30-11h00</option>
                                    <option value="11h00-13h30">11h00-13h30</option>
                                    <option value="13h30-16h00">13h30-16h00</option>
                                    <option value="16h00-18h30">16h00-18h30</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_module" class="form-label">Module</label>
                                <select class="form-select" id="edit_module" name="id_module" required>
                                    <!-- Les modules seront injectés ici par JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_groupe" class="form-label">Groupe</label>
                                <select class="form-select" id="edit_groupe" name="id_groupe" required>
                                    <!-- Les groupes seront injectés ici par JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_formateur" class="form-label">Formateur</label>
                                <select class="form-select" id="edit_formateur" name="id_formateur" required>
                                    <!-- Les formateurs seront injectés ici par JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_salle" class="form-label">Salle</label>
                                <select class="form-select" id="edit_salle" name="id_salle" required>
                                    <!-- Les salles seront injectées ici par JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_frequence" class="form-label">Fréquence</label>
                                <select class="form-select" id="edit_frequence" name="frequence" required>
                                    <option value="chaque_semaine">Chaque semaine</option>
                                    <option value="paire">Semaine paire</option>
                                    <option value="impaire">Semaine impaire</option>
                                </select>
                            </div>
                            <input type="hidden" id="editSeanceId" name="id">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="updateSeance">Mettre à jour</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS et Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Script personnalisé -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Variables globales
            let modules = [];
            let formateurs = [];
            let groupes = [];
            let salles = [];
            let emploiDuTemps = [];
            let editingSeanceId = null;

            // URL de l'API
            const apiUrl = 'back-end/emploi_api.php';

            // Initialisation
            init();

            // Fonction d'initialisation
            function init() {
                // Charger les données initiales
                loadInitialData();

                // Ajouter les écouteurs d'événements
                document.getElementById('saveSeance').addEventListener('click', saveSeance);

                // Ajouter un écouteur pour la soumission du formulaire pour éviter le comportement par défaut
                document.getElementById('seanceForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                });

                document.getElementById('editSeanceForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                });
            }

            // Fonction pour charger les données initiales
            function loadInitialData() {
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        // Stocker les données
                        modules = data.modules || [];
                        formateurs = data.formateurs || [];
                        groupes = data.groupes || [];
                        salles = data.salles || [];
                        emploiDuTemps = data.emploi_du_temps || [];

                        // Remplir les listes déroulantes
                        populateSelectOptions('module', modules);
                        populateSelectOptions('formateur', formateurs, true);
                        populateSelectOptions('groupe', groupes);
                        populateSelectOptions('salle', salles);

                        // Remplir aussi les listes du formulaire d'édition
                        populateSelectOptions('edit_module', modules);
                        populateSelectOptions('edit_formateur', formateurs, true);
                        populateSelectOptions('edit_groupe', groupes);
                        populateSelectOptions('edit_salle', salles);

                        // Afficher l'emploi du temps
                        displayEmploiDuTemps();
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des données:', error);
                        alert('Erreur lors du chargement des données. Veuillez réessayer.');
                    });
            }

            // Fonction pour recharger uniquement l'emploi du temps
            function reloadEmploiDuTemps() {
                fetch(apiUrl + '?data=emploi')
                    .then(response => response.json())
                    .then(data => {
                        emploiDuTemps = data;
                        displayEmploiDuTemps();
                    })
                    .catch(error => {
                        console.error('Erreur lors du rechargement de l\'emploi du temps:', error);
                    });
            }

            // Fonction pour remplir les options des listes déroulantes
            function populateSelectOptions(selectId, data, isFormateur = false) {
                const select = document.getElementById(selectId);

                // Vider la liste déroulante en conservant la première option
                const firstOption = select.options[0];
                select.innerHTML = '';
                if (firstOption) {
                    select.appendChild(firstOption);
                }

                // Ajouter les nouvelles options
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;

                    // Si c'est un formateur, afficher le nom et le prénom
                    if (isFormateur) {
                        option.textContent = `${item.nom} ${item.prenom}`;
                    } else {
                        option.textContent = item.nom;
                    }

                    select.appendChild(option);
                });
            }

            // Fonction pour afficher l'emploi du temps
            function displayEmploiDuTemps() {
                // Vider tous les créneaux de l'emploi du temps
                const jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                const creneaux = ['8h30-11h00', '11h00-13h30', '13h30-16h00', '16h00-18h30'];

                jours.forEach(jour => {
                    creneaux.forEach(creneau => {
                        const cellId = `${jour}-${creneau}`;
                        document.getElementById(cellId).innerHTML = '';
                    });
                });

                // Remplir l'emploi du temps avec les séances
                emploiDuTemps.forEach(seance => {
                    const cellId = `${seance.jour}-${seance.creneau}`;
                    const cellElement = document.getElementById(cellId);

                    if (cellElement) {
                        const seanceCard = createSeanceCard(seance);
                        cellElement.appendChild(seanceCard);
                    }
                });
            }

            // Fonction pour créer une carte de séance
            function createSeanceCard(seance) {
                const card = document.createElement('div');
                card.className = 'card seance-card';
                card.dataset.id = seance.id;

                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-2';

                const moduleName = document.createElement('h6');
                moduleName.className = 'card-title mb-1';
                moduleName.textContent = seance.module_nom;

                const groupName = document.createElement('p');
                groupName.className = 'card-text small mb-1';
                groupName.innerHTML = `<strong>Groupe:</strong> ${seance.groupe_nom}`;

                const formateurName = document.createElement('p');
                formateurName.className = 'card-text small mb-1';
                formateurName.innerHTML = `<strong>Formateur:</strong> ${seance.formateur_nom} ${seance.formateur_prenom}`;

                const salleName = document.createElement('p');
                salleName.className = 'card-text small mb-1';
                salleName.innerHTML = `<strong>Salle:</strong> ${seance.salle_nom}`;

                const actionDiv = document.createElement('div');
                actionDiv.className = 'd-flex justify-content-end';

                const editButton = document.createElement('button');
                editButton.className = 'btn btn-sm btn-outline-primary me-1';
                editButton.innerHTML = '<i class="fas fa-edit"></i>';
                editButton.addEventListener('click', function () {
                    openEditModal(seance);
                });

                const deleteButton = document.createElement('button');
                deleteButton.className = 'btn btn-sm btn-outline-danger';
                deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                deleteButton.addEventListener('click', function () {
                    deleteSeance(seance.id);
                });

                actionDiv.appendChild(editButton);
                actionDiv.appendChild(deleteButton);

                cardBody.appendChild(moduleName);
                cardBody.appendChild(groupName);
                cardBody.appendChild(formateurName);
                cardBody.appendChild(salleName);
                cardBody.appendChild(actionDiv);

                card.appendChild(cardBody);

                return card;
            }

            // Fonction pour ouvrir la modal d'édition d'une séance
            function openEditModal(seance) {
                editingSeanceId = seance.id;

                // Remplir les champs du formulaire avec les données de la séance
                document.getElementById('edit_jour').value = seance.jour;
                document.getElementById('edit_creneau').value = seance.creneau;
                document.getElementById('edit_module').value = seance.id_module;
                document.getElementById('edit_groupe').value = seance.id_groupe;
                document.getElementById('edit_formateur').value = seance.id_formateur;
                document.getElementById('edit_salle').value = seance.id_salle;

                // Ajouter un écouteur d'événement pour le bouton de sauvegarde
                const saveButton = document.querySelector('#editSeanceModal .modal-footer .btn-primary');
                saveButton.onclick = function () {
                    updateSeance();
                };

                // Ouvrir la modal
                const editModal = new bootstrap.Modal(document.getElementById('editSeanceModal'));
                editModal.show();
            }

            // Fonction pour sauvegarder une nouvelle séance
            function saveSeance() {
                const formData = new FormData(document.getElementById('seanceForm'));

                // Vérifier que tous les champs sont remplis
                if (!validateForm(formData)) {
                    alert('Veuillez remplir tous les champs obligatoires.');
                    return;
                }

                // Envoyer les données au serveur
                fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fermer la modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addSeanceModal'));
                            modal.hide();

                            // Réinitialiser le formulaire
                            document.getElementById('seanceForm').reset();

                            // Recharger l'emploi du temps
                            reloadEmploiDuTemps();

                            // Afficher un message de succès
                            alert('Séance ajoutée avec succès!');
                        } else {
                            // Afficher un message d'erreur
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'ajout de la séance:', error);
                        alert('Erreur lors de l\'ajout de la séance. Veuillez réessayer.');
                    });
            }

            // Fonction pour mettre à jour une séance existante
            function updateSeance() {
                const formData = new FormData(document.getElementById('editSeanceForm'));
                formData.append('id', editingSeanceId);

                // Vérifier que tous les champs sont remplis
                if (!validateForm(formData)) {
                    alert('Veuillez remplir tous les champs obligatoires.');
                    return;
                }

                // Envoyer les données au serveur
                fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fermer la modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editSeanceModal'));
                            modal.hide();

                            // Recharger l'emploi du temps
                            reloadEmploiDuTemps();

                            // Afficher un message de succès
                            alert('Séance mise à jour avec succès!');
                        } else {
                            // Afficher un message d'erreur
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la mise à jour de la séance:', error);
                        alert('Erreur lors de la mise à jour de la séance. Veuillez réessayer.');
                    });
            }

            // Fonction pour supprimer une séance
            function deleteSeance(id) {
                // Demander confirmation
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette séance?')) {
                    return;
                }

                // Créer un FormData pour l'envoi
                const formData = new FormData();
                formData.append('id', id);
                formData.append('action', 'delete');

                // Envoyer la demande au serveur
                fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recharger l'emploi du temps
                            reloadEmploiDuTemps();

                            // Afficher un message de succès
                            alert('Séance supprimée avec succès!');
                        } else {
                            // Afficher un message d'erreur
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression de la séance:', error);
                        alert('Erreur lors de la suppression de la séance. Veuillez réessayer.');
                    });
            }

            // Fonction pour valider le formulaire
            function validateForm(formData) {
                // Vérifier que tous les champs requis sont remplis
                const requiredFields = ['jour', 'creneau', 'id_module', 'id_groupe', 'id_formateur', 'id_salle'];

                for (const field of requiredFields) {
                    if (!formData.get(field) || formData.get(field).trim() === '') {
                        return false;
                    }
                }

                return true;
            }
        });
    </script>
</body>

</html>