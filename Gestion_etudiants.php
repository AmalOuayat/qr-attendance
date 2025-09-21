<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants</title>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f7;
            color: var(--text-color);
            line-height: 1.6;
            padding-bottom: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
            padding: 0 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
            color: white;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .group-selector {
            flex: 1;
            min-width: 250px;
            max-width: 400px;
        }

        .group-selector select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            background-color: white;
            font-size: 1rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            color: var(--text-color);
        }

        .group-selector select:focus {
            border-color: var(--accent-color);
            outline: none;
        }

        .group-selector label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        .btn-icon {
            font-size: 1.1rem;
        }

        .card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid rgba(153, 205, 216, 0.2);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th {
            background-color: var(--light-bg);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            color: var(--secondary-color);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(153, 205, 216, 0.2);
            vertical-align: middle;
            color: var(--text-color);
        }

        tbody tr:hover {
            background-color: rgba(153, 205, 216, 0.05);
        }

        .action-cell {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .edit-icon {
            background-color: rgba(253, 232, 211, 0.3);
            color: var(--secondary-color);
        }

        .edit-icon:hover {
            background-color: var(--warning-color);
            color: var(--secondary-color);
        }

        .delete-icon {
            background-color: rgba(243, 195, 178, 0.3);
            color: var(--secondary-color);
        }

        .delete-icon:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(101, 113, 102, 0.5);
            z-index: 1000;
            overflow-y: auto;
            padding: 2rem 1rem;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: 0 4px 20px rgba(101, 113, 102, 0.2);
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            animation: modal-fade 0.3s ease;
            border: 1px solid var(--border-color);
        }

        @keyframes modal-fade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: var(--transition);
            color: var(--text-color);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(154, 235, 227, 0.2);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-secondary {
            background-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: #535d54;
        }

        .btn-submit {
            background-color: var(--success-color);
            color: var(--secondary-color);
        }

        .btn-submit:hover {
            background-color: #7bcbc4;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .group-selector {
                max-width: none;
            }

            .action-buttons {
                justify-content: flex-start;
            }

            .modal-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {

            td,
            th {
                padding: 0.75rem 0.5rem;
            }

            .action-cell {
                flex-direction: column;
                gap: 0.5rem;
                align-items: center;
            }
        }

        /* Status message */
        .status-message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--radius);
            display: none;
        }

        .status-success {
            background-color: rgba(154, 235, 227, 0.2);
            border: 1px solid var(--success-color);
            color: var(--secondary-color);
        }

        .status-error {
            background-color: rgba(243, 195, 178, 0.2);
            border: 1px solid var(--danger-color);
            color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <header>
        <h1>Gestion des Étudiants</h1>
    </header>

    <div class="container">
        <div id="statusMessage" class="status-message"></div>

        <div class="controls">
            <div class="group-selector">
                <label for="groupeSelect">Choisir un groupe :</label>
                <select id="groupeSelect" onchange="chargerEtudiants()">
                    <option value="">Sélectionnez un groupe</option>
                </select>
            </div>
            <div class="action-buttons">
                <button class="btn" onclick="ouvrirModalAjout()">
                    <i class="bi bi-plus-circle btn-icon"></i>
                    Ajouter un étudiant
                </button>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table id="etudiantsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>CNE</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les étudiants seront ajoutés ici par JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un étudiant -->
    <div id="modalAjout" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajouter un étudiant</h2>
            </div>
            <form id="ajoutForm">
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cne">CNE :</label>
                    <input type="text" id="cne" name="cne" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="tel">Téléphone :</label>
                    <input type="text" id="tel" name="tel" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="groupe">Groupe :</label>
                    <select id="groupe" name="groupe" class="form-control" required>
                        <option value="">Sélectionnez un groupe</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="fermerModalAjout()">Annuler</button>
                    <button type="submit" class="btn btn-submit">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal pour modifier un étudiant -->
    <div id="modalModification" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier un étudiant</h2>
            </div>
            <form id="modificationForm">
                <input type="hidden" id="modificationId" name="id">
                <div class="form-group">
                    <label for="modificationNom">Nom :</label>
                    <input type="text" id="modificationNom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modificationPrenom">Prénom :</label>
                    <input type="text" id="modificationPrenom" name="prenom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modificationCne">CNE :</label>
                    <input type="text" id="modificationCne" name="cne" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modificationEmail">Email :</label>
                    <input type="email" id="modificationEmail" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modificationTel">Téléphone :</label>
                    <input type="text" id="modificationTel" name="tel" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modificationGroupe">Groupe :</label>
                    <select id="modificationGroupe" name="groupe" class="form-control" required>
                        <option value="">Sélectionnez un groupe</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="fermerModalModification()">Annuler</button>
                    <button type="submit" class="btn btn-submit">Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Afficher un message de statut
        function afficherMessage(type, message) {
            const statusMessage = document.getElementById("statusMessage");
            statusMessage.textContent = message;
            statusMessage.style.display = "block";

            if (type === "success") {
                statusMessage.className = "status-message status-success";
            } else {
                statusMessage.className = "status-message status-error";
            }

            // Cacher le message après 5 secondes
            setTimeout(() => {
                statusMessage.style.display = "none";
            }, 5000);
        }

        // Charger les groupes depuis la base de données
        function chargerGroupes() {
            fetch('back-end/etudiants_api.php?action=getGroupes')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById("groupeSelect");
                        const selectModal = document.getElementById("groupe");
                        const selectModificationModal = document.getElementById("modificationGroupe");
                        select.innerHTML = '<option value="">Sélectionnez un groupe</option>';
                        selectModal.innerHTML = '<option value="">Sélectionnez un groupe</option>';
                        selectModificationModal.innerHTML = '<option value="">Sélectionnez un groupe</option>';
                        data.groupes.forEach(groupe => {
                            const option = document.createElement("option");
                            option.value = groupe.id;
                            option.textContent = groupe.nom;
                            select.appendChild(option);
                            selectModal.appendChild(option.cloneNode(true));
                            selectModificationModal.appendChild(option.cloneNode(true));
                        });
                    } else {
                        afficherMessage("error", 'Erreur de récupération des groupes : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    afficherMessage("error", "Erreur lors de la récupération des groupes : " + error.message);
                });
        }

        // Charger les étudiants d'un groupe
        function chargerEtudiants() {
            const groupeId = document.getElementById("groupeSelect").value;
            if (!groupeId) {
                const tbody = document.querySelector("#etudiantsTable tbody");
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Veuillez sélectionner un groupe</td></tr>';
                return;
            }

            fetch(`back-end/etudiants_api.php?action=getEtudiants&groupeId=${groupeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.querySelector("#etudiantsTable tbody");
                        tbody.innerHTML = '';

                        if (data.etudiants.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Aucun étudiant trouvé dans ce groupe</td></tr>';
                            return;
                        }

                        data.etudiants.forEach(etudiant => {
                            const newRow = document.createElement("tr");
                            newRow.dataset.id = etudiant.id;

                            newRow.innerHTML = `
                                <td>${etudiant.id}</td>
                                <td>${etudiant.nom}</td>
                                <td>${etudiant.prenom}</td>
                                <td>${etudiant.cne}</td>
                                <td>${etudiant.email}</td>
                                <td>${etudiant.tel}</td>
                                <td class="action-cell">
                                    <span class="action-icon edit-icon" title="Modifier l'étudiant" onclick="ouvrirModalModification(${etudiant.id})">
                                        <i class="bi bi-pencil"></i>
                                    </span>
                                    <span class="action-icon delete-icon" title="Supprimer l'étudiant" onclick="supprimerEtudiant(${etudiant.id})">
                                        <i class="bi bi-trash"></i>
                                    </span>
                                </td>
                            `;
                            tbody.appendChild(newRow);
                        });
                    } else {
                        afficherMessage("error", 'Erreur de récupération des étudiants : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    afficherMessage("error", "Erreur lors de la récupération des étudiants : " + error.message);
                });
        }

        // Ouvrir le modal pour ajouter un étudiant
        function ouvrirModalAjout() {
            document.getElementById("ajoutForm").reset();
            document.getElementById("modalAjout").style.display = "block";
        }

        // Fermer le modal
        function fermerModalAjout() {
            document.getElementById("modalAjout").style.display = "none";
        }

        // Ouvrir le modal pour modifier un étudiant
        function ouvrirModalModification(etudiantId) {
            fetch(`back-end/etudiants_api.php?action=getEtudiant&id=${etudiantId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const etudiant = data.etudiant;
                        document.getElementById("modificationId").value = etudiant.id;
                        document.getElementById("modificationNom").value = etudiant.nom;
                        document.getElementById("modificationPrenom").value = etudiant.prenom;
                        document.getElementById("modificationCne").value = etudiant.cne;
                        document.getElementById("modificationEmail").value = etudiant.email;
                        document.getElementById("modificationTel").value = etudiant.tel;
                        document.getElementById("modificationGroupe").value = etudiant.id_groupe;
                        document.getElementById("modalModification").style.display = "block";
                    } else {
                        afficherMessage("error", 'Erreur de récupération des informations de l\'étudiant : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    afficherMessage("error", "Erreur lors de la récupération des informations de l'étudiant : " + error.message);
                });
        }

        // Fermer le modal de modification
        function fermerModalModification() {
            document.getElementById("modalModification").style.display = "none";
        }

        // Ajouter un étudiant
        document.getElementById("ajoutForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = {
                nom: document.getElementById("nom").value,
                prenom: document.getElementById("prenom").value,
                cne: document.getElementById("cne").value,
                email: document.getElementById("email").value,
                tel: document.getElementById("tel").value,
                groupeId: document.getElementById("groupe").value
            };

            fetch('back-end/etudiants_api.php?action=ajouterEtudiant', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chargerEtudiants();
                        fermerModalAjout();
                        afficherMessage("success", "Étudiant ajouté avec succès");
                    } else {
                        afficherMessage("error", "Erreur lors de l'ajout : " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    afficherMessage("error", "Erreur de connexion ou de traitement : " + error.message);
                });
        });

        // Modifier un étudiant
        document.getElementById("modificationForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = {
                id: document.getElementById("modificationId").value,
                nom: document.getElementById("modificationNom").value,
                prenom: document.getElementById("modificationPrenom").value,
                cne: document.getElementById("modificationCne").value,
                email: document.getElementById("modificationEmail").value,
                tel: document.getElementById("modificationTel").value,
                groupeId: document.getElementById("modificationGroupe").value
            };

            fetch('back-end/etudiants_api.php?action=modifierEtudiant', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chargerEtudiants();
                        fermerModalModification();
                        afficherMessage("success", "Étudiant modifié avec succès");
                    } else {
                        afficherMessage("error", "Erreur lors de la modification : " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    afficherMessage("error", "Erreur de connexion ou de traitement : " + error.message);
                });
        });

        // Supprimer un étudiant
        function supprimerEtudiant(etudiantId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?")) {
                fetch(`back-end/etudiants_api.php?action=supprimerEtudiant&id=${etudiantId}`, {
                    method: 'DELETE'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chargerEtudiants();
                            afficherMessage("success", "Étudiant supprimé avec succès");
                        } else {
                            afficherMessage("error", "Erreur lors de la suppression : " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        afficherMessage("error", "Erreur de connexion ou de traitement : " + error.message);
                    });
            }
        }

        // Fermer les modals si on clique en dehors
        window.onclick = function (event) {
            const modalAjout = document.getElementById("modalAjout");
            const modalModification = document.getElementById("modalModification");

            if (event.target == modalAjout) {
                modalAjout.style.display = "none";
            }

            if (event.target == modalModification) {
                modalModification.style.display = "none";
            }
        }

        // Charger les groupes lorsque la page est chargée
        window.onload = chargerGroupes;
    </script>
</body>

</html>