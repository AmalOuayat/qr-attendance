<?php
include('back-end/api.php');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Groupes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: white;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        h1 {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 1.8rem;
        }

        .card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #88b9c3;
            /* Bleu clair plus foncé */
        }

        .btn-outline {
            border: 1px solid var(--secondary-color);
            color: var(--secondary-color);
            background: transparent;
        }

        .btn-outline:hover {
            background-color: rgba(101, 113, 102, 0.05);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: var(--secondary-color);
        }

        .btn-danger:hover {
            background-color: #e0b2a2;
            /* Rose saumon plus foncé */
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: var(--radius);
            overflow: hidden;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr {
            transition: all 0.2s ease;
        }

        tbody tr:hover {
            background-color: rgba(154, 235, 227, 0.1);
            /* Légère teinte de vert d'eau */
        }

        tr.selected {
            background-color: rgba(154, 235, 227, 0.2);
            /* Teinte plus prononcée de vert d'eau */
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.7;
            color: var(--text-secondary);
        }

        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(101, 113, 102, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(3px);
        }

        .modal {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            width: 450px;
            max-width: 90%;
            animation: slideIn 0.3s ease;
            border: 1px solid var(--border-color);
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-color);
            color: white;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.8rem;
            background-color: rgba(253, 232, 211, 0.3);
            /* Légère teinte de beige clair */
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-color);
        }

        input[type="text"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: border 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(154, 235, 227, 0.2);
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transform: translateY(150%);
            animation: slideUp 0.3s forwards, slideDown 0.3s 3s forwards;
            z-index: 2000;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            to {
                transform: translateY(150%);
            }
        }

        .toast.success {
            border-left: 4px solid var(--success-color);
        }

        .toast.error {
            border-left: 4px solid var(--danger-color);
        }

        .toast i {
            font-size: 1.2rem;
        }

        .toast.success i {
            color: var(--success-color);
        }

        .toast.error i {
            color: var(--danger-color);
        }

        #loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1500;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(153, 205, 216, 0.2);
            border-left-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }

            th,
            td {
                padding: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Gestion des Groupes</h1>
        </div>

        <div class="card">
            <div class="action-buttons">
                <button id="btnAdd" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Ajouter
                </button>
                <button id="btnEdit" class="btn btn-outline" disabled>
                    <i class="bi bi-pencil"></i> Modifier
                </button>
                <button id="btnDelete" class="btn btn-danger" disabled>
                    <i class="bi bi-trash"></i> Supprimer
                </button>
            </div>

            <div id="tableContainer">
                <table id="groupesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom du groupe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les groupes seront chargés dynamiquement ici -->
                    </tbody>
                </table>
                <div id="emptyState" class="empty-state" style="display: none;">
                    <i class="bi bi-people"></i>
                    <h3>Aucun groupe trouvé</h3>
                    <p>Cliquez sur le bouton "Ajouter" pour créer votre premier groupe.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier un groupe -->
    <div id="modalBackdrop" class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle" class="modal-title">Ajouter un Groupe</h2>
                <button class="modal-close" onclick="fermerModal()">&times;</button>
            </div>
            <form id="groupeForm">
                <div class="modal-body">
                    <input type="hidden" id="groupeId" name="groupeId">
                    <div class="form-group">
                        <label for="nomGroupe">Nom du groupe</label>
                        <input type="text" id="nomGroupe" name="nomGroupe" placeholder="Entrez le nom du groupe"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="fermerModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast notifications -->
    <div id="toast" class="toast">
        <i class="bi"></i>
        <span id="toastMessage"></span>
    </div>

    <!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
    </div>

    <script>
        let selectedRow = null;
        const btnEdit = document.getElementById('btnEdit');
        const btnDelete = document.getElementById('btnDelete');
        const modalBackdrop = document.getElementById('modalBackdrop');
        const toast = document.getElementById('toast');
        const loader = document.getElementById('loader');
        const emptyState = document.getElementById('emptyState');

        // Afficher le chargeur
        function showLoader() {
            loader.style.display = 'flex';
        }

        // Masquer le chargeur
        function hideLoader() {
            loader.style.display = 'none';
        }

        // Afficher une notification toast
        function showToast(message, type = 'success') {
            const toastElement = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const icon = toastElement.querySelector('i');

            toastElement.className = 'toast ' + type;
            icon.className = type === 'success' ? 'bi bi-check-circle' : 'bi bi-exclamation-circle';
            toastMessage.textContent = message;

            // Reset animation
            toastElement.style.animation = 'none';
            void toastElement.offsetWidth; // Trigger reflow
            toastElement.style.animation = 'slideUp 0.3s forwards, slideDown 0.3s 3s forwards';
        }

        // Ouvrir le modal pour ajouter un groupe
        function ouvrirModal() {
            document.getElementById("modalTitle").textContent = "Ajouter un Groupe";
            document.getElementById("groupeId").value = "";
            document.getElementById("nomGroupe").value = "";
            modalBackdrop.style.display = "flex";
            document.getElementById("nomGroupe").focus();
        }

        // Ouvrir le modal pour modifier un groupe
        function ouvrirModalModification() {
            if (selectedRow) {
                document.getElementById("modalTitle").textContent = "Modifier le Groupe";
                document.getElementById("groupeId").value = selectedRow.cells[0].textContent;
                document.getElementById("nomGroupe").value = selectedRow.cells[1].textContent;
                modalBackdrop.style.display = "flex";
                document.getElementById("nomGroupe").focus();
            }
        }

        // Fermer le modal
        function fermerModal() {
            modalBackdrop.style.display = "none";
            document.getElementById("groupeForm").reset();
        }

        // Charger les groupes depuis l'API
        function chargerGroupes() {
            showLoader();

            fetch('back-end/groupe_api.php?action=load')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector("#groupesTable tbody");
                    tbody.innerHTML = "";

                    if (data.length > 0) {
                        data.forEach(groupe => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${groupe.id}</td>
                                <td>${groupe.nom}</td>
                            `;
                            row.addEventListener('click', function () {
                                selectRow(this);
                            });
                            tbody.appendChild(row);
                        });
                        emptyState.style.display = 'none';
                    } else {
                        emptyState.style.display = 'flex';
                    }

                    // Réinitialiser la sélection
                    selectedRow = null;
                    updateButtonStates();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast("Erreur lors du chargement des groupes", "error");
                })
                .finally(() => {
                    hideLoader();
                });
        }

        // Sélectionner une ligne
        function selectRow(row) {
            if (selectedRow) {
                selectedRow.classList.remove('selected');
            }

            if (selectedRow === row) {
                selectedRow = null;
            } else {
                selectedRow = row;
                selectedRow.classList.add('selected');
            }

            updateButtonStates();
        }

        // Mettre à jour l'état des boutons d'action
        function updateButtonStates() {
            btnEdit.disabled = !selectedRow;
            btnDelete.disabled = !selectedRow;
        }

        // Gérer la soumission du formulaire
        document.getElementById("groupeForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const idGroupe = document.getElementById("groupeId").value;
            const nomGroupe = document.getElementById("nomGroupe").value.trim();

            if (!nomGroupe) {
                showToast("Le nom du groupe ne peut pas être vide", "error");
                return;
            }

            const action = idGroupe ? "update" : "add";
            const payload = { id: idGroupe, nom: nomGroupe };

            showLoader();

            fetch(`back-end/groupe_api.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chargerGroupes();
                        fermerModal();
                        showToast(data.message || (action === "add" ? "Groupe ajouté avec succès" : "Groupe mis à jour avec succès"));
                    } else {
                        showToast(data.message || "Une erreur est survenue", "error");
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast("Erreur lors de l'enregistrement", "error");
                })
                .finally(() => {
                    hideLoader();
                });
        });

        // Supprimer un groupe
        function supprimerGroupe() {
            if (!selectedRow) {
                showToast("Veuillez sélectionner un groupe à supprimer", "error");
                return;
            }

            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer ce groupe ?");
            if (!confirmation) {
                return;
            }

            const id = selectedRow.cells[0].textContent;

            showLoader();

            fetch('back-end/groupe_api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chargerGroupes();
                        showToast(data.message || "Groupe supprimé avec succès");
                    } else {
                        showToast(data.message || "Erreur lors de la suppression", "error");
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast("Erreur lors de la suppression", "error");
                })
                .finally(() => {
                    hideLoader();
                });
        }

        // Event listeners for buttons
        document.getElementById('btnAdd').addEventListener('click', ouvrirModal);
        document.getElementById('btnEdit').addEventListener('click', ouvrirModalModification);
        document.getElementById('btnDelete').addEventListener('click', supprimerGroupe);

        // Fermer le modal en cliquant à l'extérieur
        modalBackdrop.addEventListener('click', function (event) {
            if (event.target === modalBackdrop) {
                fermerModal();
            }
        });

        // Charger les groupes au chargement de la page
        document.addEventListener("DOMContentLoaded", chargerGroupes);
    </script>
</body>

</html>