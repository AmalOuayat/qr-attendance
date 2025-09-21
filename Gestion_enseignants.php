<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Formateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Styles modernes pour l'interface avec nouvelle palette de couleurs */
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f9f7;
            /* Fond légèrement teinté */
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

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            background-color: white;
            padding: 1rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .search-box {
            display: flex;
            align-items: center;
            background-color: var(--light-bg);
            border-radius: 4px;
            padding: 0.5rem 1rem;
            width: 300px;
        }

        .search-box input {
            border: none;
            background: transparent;
            margin-left: 8px;
            width: 100%;
            outline: none;
            color: var(--text-color);
        }

        .action-icons {
            display: flex;
            gap: 15px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-secondary);
        }

        .btn-action:hover {
            background-color: var(--light-bg);
            transform: translateY(-2px);
        }

        .btn-add {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-add:hover {
            background-color: var(--secondary-color);
        }

        .btn-edit {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-edit:hover {
            background-color: var(--secondary-color);
        }

        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-delete:hover {
            background-color: var(--secondary-color);
        }

        .btn-refresh {
            background-color: var(--light-bg);
            color: var(--text-secondary);
        }

        .btn-refresh:hover {
            background-color: #e2e6ea;
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
            color: var(--text-color);
            white-space: nowrap;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tr.selected {
            background-color: rgba(153, 205, 216, 0.2) !important;
            /* Bleu clair transparent */
            border-left: 4px solid var(--primary-color);
        }

        /* Style pour le modal déplaçable */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(101, 113, 102, 0.5);
            /* Gris foncé avec transparence */
            z-index: 1000;
            overflow: auto;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 0;
            border-radius: var(--radius);
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 25px rgba(101, 113, 102, 0.2);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            flex-shrink: 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .close-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(153, 205, 216, 0.2);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .error-message {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            background-color: var(--light-bg);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-bottom-left-radius: var(--radius);
            border-bottom-right-radius: var(--radius);
            flex-shrink: 0;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: var(--light-bg);
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background-color: var(--border-color);
        }

        /* Badges pour les mots de passe */
        .password-badge {
            background-color: var(--light-bg);
            color: var(--text-color);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .action-bar {
                flex-direction: column;
                gap: 10px;
            }

            .search-box {
                width: 100%;
            }

            th,
            td {
                padding: 0.75rem 0.5rem;
            }

            .modal-content {
                width: 95%;
            }
        }

        /* Pour cacher certaines colonnes sur les petits écrans */
        @media (max-width: 576px) {
            .hide-on-mobile {
                display: none;
            }
        }

        /* Classe pour désactiver les transitions pendant le déplacement du modal */
        .dragging {
            transition: none !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gestion des Formateurs</h1>

        <div class="action-bar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Rechercher un formateur..." id="searchInput">
            </div>
            <div class="action-icons">
                <button class="btn-action btn-add" title="Ajouter un formateur" onclick="ouvrirModal()">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button class="btn-action btn-edit" title="Modifier un formateur" onclick="ouvrirModalModification()">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-delete" title="Supprimer un formateur" onclick="supprimerFormateur()">
                    <i class="bi bi-trash"></i>
                </button>
                <button class="btn-action btn-refresh" title="Actualiser la liste" onclick="chargerFormateurs()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="table-container">
            <table id="formateursTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th class="hide-on-mobile">Téléphone</th>
                        <th>Mot de passe</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les formateurs seront ajoutés ici par JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal déplaçable pour ajouter/modifier un formateur -->
    <div id="modal" class="modal">
        <div class="modal-content" id="draggableModal">
            <div class="modal-header" id="modalHeader">
                <h2 id="modalTitle">Ajouter un Formateur</h2>
                <button class="close-btn" onclick="fermerModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formateurForm">
                    <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone :</label>
                        <input type="text" id="telephone" name="telephone" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="motdepasse">Mot de passe :</label>
                        <div class="password-container">
                            <input type="password" id="motdepasse" name="motdepasse" class="form-control" required
                                minlength="6">
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="passwordError" class="error-message">Le mot de passe doit contenir au moins 6
                            caractères.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fermerModal()">Annuler</button>
                <button type="button" class="btn btn-primary" id="submitButton"
                    onclick="soumettreFormulaire()">Enregistrer</button>
            </div>
        </div>
    </div>

    <script>
        let selectedRow = null; // Ligne sélectionnée
        let isEditing = false; // Mode édition ou ajout
        let isDragging = false;
        let offsetX, offsetY;

        // Rendre le modal déplaçable
        const modalHeader = document.getElementById('modalHeader');
        const draggableModal = document.getElementById('draggableModal');
        const modalBody = document.querySelector('.modal-body');

        modalHeader.addEventListener('mousedown', function (e) {
            isDragging = true;
            offsetX = e.clientX - draggableModal.getBoundingClientRect().left;
            offsetY = e.clientY - draggableModal.getBoundingClientRect().top;

            // Ajouter une classe pour désactiver les transitions pendant le drag
            draggableModal.classList.add('dragging');
        });

        document.addEventListener('mousemove', function (e) {
            if (isDragging) {
                // Calculer les nouvelles coordonnées
                let newLeft = e.clientX - offsetX;
                let newTop = e.clientY - offsetY;

                // Appliquer les nouvelles coordonnées
                draggableModal.style.left = newLeft + 'px';
                draggableModal.style.top = newTop + 'px';
                draggableModal.style.transform = 'none'; // Annuler le transform initial
            }
        });

        document.addEventListener('mouseup', function () {
            if (isDragging) {
                isDragging = false;
                // Retirer la classe de dragging
                draggableModal.classList.remove('dragging');
            }
        });

        // S'assurer que le modal reste dans les limites de l'écran
        function keepModalInView() {
            const modalRect = draggableModal.getBoundingClientRect();
            const windowWidth = window.innerWidth;
            const windowHeight = window.innerHeight;

            if (modalRect.right > windowWidth) {
                draggableModal.style.left = (windowWidth - modalRect.width) + 'px';
            }
            if (modalRect.bottom > windowHeight) {
                draggableModal.style.top = (windowHeight - modalRect.height) + 'px';
            }
            if (modalRect.left < 0) {
                draggableModal.style.left = '0px';
            }
            if (modalRect.top < 0) {
                draggableModal.style.top = '0px';
            }
        }

        // Fonction pour basculer la visibilité du mot de passe
        function togglePassword() {
            const passwordInput = document.getElementById('motdepasse');
            const toggleBtn = document.querySelector('.toggle-password i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.classList.remove('bi-eye');
                toggleBtn.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleBtn.classList.remove('bi-eye-slash');
                toggleBtn.classList.add('bi-eye');
            }
        }

        // Ouvrir le modal
        function ouvrirModal() {
            document.getElementById("formateurForm").reset();
            document.getElementById("modal").style.display = "block";
            document.getElementById("modalTitle").textContent = "Ajouter un Formateur";
            document.getElementById("submitButton").textContent = "Enregistrer";
            isEditing = false;

            // Réinitialiser la position du modal
            resetModalPosition();
        }

        // Réinitialiser la position du modal
        function resetModalPosition() {
            const modal = document.getElementById('draggableModal');
            modal.style.left = '50%';
            modal.style.top = '50%';
            modal.style.transform = 'translate(-50%, -50%)';
        }

        // Ouvrir le modal en mode édition
        function ouvrirModalModification() {
            if (!selectedRow) {
                alert("Veuillez sélectionner un formateur à modifier.");
                return;
            }

            // Récupérer les données de la ligne sélectionnée
            const cells = selectedRow.cells;
            const id = cells[0].textContent;
            const nom = cells[1].textContent;
            const prenom = cells[2].textContent;
            const email = cells[3].textContent;
            const telephone = cells[4].textContent;

            // Remplir le modal avec les données
            document.getElementById("nom").value = nom;
            document.getElementById("prenom").value = prenom;
            document.getElementById("email").value = email;
            document.getElementById("telephone").value = telephone;
            document.getElementById("motdepasse").value = ""; // Réinitialiser le mot de passe

            // Changer le titre et le texte du bouton du modal
            document.getElementById("modalTitle").textContent = "Modifier un Formateur";
            document.getElementById("submitButton").textContent = "Mettre à jour";

            // Ouvrir le modal
            document.getElementById("modal").style.display = "block";
            isEditing = true;

            // Réinitialiser la position du modal
            resetModalPosition();
        }

        // Fermer le modal
        function fermerModal() {
            document.getElementById("modal").style.display = "none";
            document.getElementById("formateurForm").reset();
        }

        // Soumettre le formulaire
        function soumettreFormulaire() {
            const form = document.getElementById("formateurForm");

            // Vérifier la validité du formulaire
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const nom = document.getElementById("nom").value;
            const prenom = document.getElementById("prenom").value;
            const email = document.getElementById("email").value;
            const telephone = document.getElementById("telephone").value;
            const motdepasse = document.getElementById("motdepasse").value;

            // Validation du mot de passe (uniquement pour l'ajout)
            if (!isEditing && motdepasse.length < 6) {
                document.getElementById("passwordError").style.display = "block";
                return;
            } else {
                document.getElementById("passwordError").style.display = "none";
            }

            // Construction des données à envoyer
            const data = JSON.stringify({
                nom: nom,
                prenom: prenom,
                email: email,
                tel: telephone,
                password: motdepasse
            });

            // Déterminer l'URL et la méthode en fonction du mode (ajout ou modification)
            const url = isEditing
                ? `back-end/enseignant_api.php?action=updateFormateur&id=${selectedRow.cells[0].textContent}`
                : 'back-end/enseignant_api.php?action=addFormateur';
            const method = isEditing ? 'PUT' : 'POST';

            // Envoi des données au back-end via fetch
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: data
            })
                .then(response => response.text())
                .then(responseText => {
                    console.log("Réponse brute du serveur:", responseText);
                    try {
                        const data = JSON.parse(responseText);
                        if (data.success) {
                            if (isEditing) {
                                // Mettre à jour la ligne dans le tableau
                                selectedRow.cells[1].textContent = nom;
                                selectedRow.cells[2].textContent = prenom;
                                selectedRow.cells[3].textContent = email;
                                selectedRow.cells[4].textContent = telephone;
                            } else {
                                // Ajouter une nouvelle ligne à la table
                                const newRow = document.createElement('tr');
                                newRow.innerHTML = `
                                    <td>${data.id}</td>
                                    <td>${nom}</td>
                                    <td>${prenom}</td>
                                    <td>${email}</td>
                                    <td class="hide-on-mobile">${telephone}</td>
                                    <td><span class="password-badge"><i class="bi bi-shield-lock"></i> Masqué</span></td>
                                `;
                                document.querySelector("#formateursTable tbody").appendChild(newRow);
                            }
                            fermerModal();
                        } else {
                            alert("Erreur : " + data.message);
                        }
                    } catch (e) {
                        console.error("Erreur lors du parsing de la réponse JSON:", e);
                        alert("Erreur de traitement de la réponse du serveur.");
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert("Erreur de connexion ou de traitement : " + error.message);
                });
        }

        // Sélectionner une ligne dans le tableau
        document.querySelector("#formateursTable tbody").addEventListener("click", function (event) {
            const row = event.target.closest("tr");
            if (row) {
                // Désélectionner la ligne précédente
                if (selectedRow) {
                    selectedRow.classList.remove("selected");
                }
                // Sélectionner la nouvelle ligne
                selectedRow = row;
                selectedRow.classList.add("selected");
            }
        });

        // Fonction pour supprimer un formateur
        function supprimerFormateur() {
            if (!selectedRow) {
                alert("Veuillez sélectionner un formateur à supprimer.");
                return;
            }

            // Récupérer l'ID du formateur sélectionné
            const id = selectedRow.cells[0].textContent;

            // Demander une confirmation avant de supprimer
            if (confirm("Êtes-vous sûr de vouloir supprimer ce formateur ?")) {
                // Envoyer une requête de suppression au back-end
                fetch(`back-end/enseignant_api.php?action=deleteFormateur&id=${id}`, {
                    method: 'DELETE'
                })
                    .then(response => response.text())
                    .then(responseText => {
                        console.log("Réponse brute du serveur:", responseText);
                        try {
                            const data = JSON.parse(responseText);
                            if (data.success) {
                                // Supprimer la ligne du tableau
                                selectedRow.remove();
                                selectedRow = null; // Réinitialiser la ligne sélectionnée
                            } else {
                                alert("Erreur lors de la suppression : " + data.message);
                            }
                        } catch (e) {
                            console.error("Erreur lors du parsing de la réponse JSON:", e);
                            alert("Erreur de traitement de la réponse du serveur.");
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert("Erreur de connexion ou de traitement : " + error.message);
                    });
            }
        }

        // Fonction pour charger les formateurs depuis la base de données
        function chargerFormateurs() {
            fetch('back-end/enseignant_api.php?action=getFormateurs')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Effacer le contenu actuel du tableau
                        const tbody = document.querySelector("#formateursTable tbody");
                        tbody.innerHTML = '';

                        // Ajouter les formateurs à la table
                        data.formateurs.forEach(formateur => {
                            const newRow = document.createElement('tr');
                            newRow.innerHTML = `
                                <td>${formateur.id}</td>
                                <td>${formateur.nom}</td>
                                <td>${formateur.prenom}</td>
                                <td>${formateur.email}</td>
                                <td class="hide-on-mobile">${formateur.tel}</td>
                                <td><span class="password-badge"><i class="bi bi-shield-lock"></i> Masqué</span></td>
                            `;
                            tbody.appendChild(newRow);
                        });
                    } else {
                        alert('Erreur de récupération des formateurs : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert("Erreur lors de la récupération des formateurs : " + error.message);
                });
        }

        // Fonction de recherche
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll("#formateursTable tbody tr");

            rows.forEach(row => {
                let found = false;
                const cells = row.getElementsByTagName('td');

                for (let i = 0; i < cells.length; i++) {
                    const cellText = cells[i].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            });
        });

        // Vérifier périodiquement que le modal reste dans les limites de l'écran
        window.addEventListener('resize', keepModalInView);

        // Charger les formateurs lorsque la page est chargée
        window.onload = chargerFormateurs;
    </script>
</body>

</html>