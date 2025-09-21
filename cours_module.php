<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Modules</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: white;
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
        }

        h1 {
            color: var(--secondary-color);
            font-size: 2.2rem;
            font-weight: 600;
        }

        h2 {
            color: var(--text-secondary);
            font-size: 1.6rem;
            margin: 25px 0 15px 0;
            font-weight: 500;
        }

        .card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 25px;
            margin-bottom: 25px;
            border-top: 4px solid var(--primary-color);
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-control {
            flex: 1;
            min-width: 200px;
        }

        input,
        select,
        button {
            width: 100%;
            padding: 12px 15px;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            font-size: 1rem;
            transition: all 0.2s ease-in-out;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(153, 205, 216, 0.2);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(153, 205, 216, 0.05);
        }

        .empty-message {
            text-align: center;
            padding: 20px;
            color: var(--text-color);
            font-style: italic;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-active {
            background-color: rgba(154, 235, 227, 0.15);
            color: var(--success-color);
        }

        @media (max-width: 768px) {
            .form-control {
                min-width: 100%;
            }

            th,
            td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-graduation-cap"></i> Gestion des Modules</h1>
        </header>

        <div class="card">
            <h2><i class="fas fa-plus-circle"></i> Ajouter un Module</h2>
            <form id="moduleForm">
                <div class="form-group">
                    <div class="form-control">
                        <input type="text" id="nomModule" placeholder="Nom du module" required>
                    </div>
                    <div class="form-control">
                        <input type="number" id="heure" placeholder="Masse horaire (heures)" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-control">
                        <select id="formateur" required>
                            <option value="">Sélectionnez un formateur</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <select id="filiere" required onchange="loadBranches()">
                            <option value="">Sélectionnez une filière</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <select id="branche" required>
                            <option value="">Sélectionnez une branche</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-control">
                        <select id="groupe" required>
                            <option value="">Sélectionnez un groupe</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <select id="salle" required>
                            <option value="">Sélectionnez une salle</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <button type="submit"><i class="fas fa-save"></i> Ajouter Module</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Liste des Modules</h2>
            <table id="moduleTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Heures</th>
                        <th>Formateur</th>
                        <th>Filière</th>
                        <th>Branche</th>
                        <th>Groupe</th>
                        <th>Salle</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les modules seront ajoutés ici dynamiquement -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Charger les formateurs, filières, branches, salles et groupes au démarrage de la page
        document.addEventListener('DOMContentLoaded', function () {
            loadFormateurs();
            loadFilieres();
            loadSalles();
            loadGroupes();
            loadModules();
        });

        // Ajouter un module
        document.getElementById('moduleForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const moduleData = {
                nom: document.getElementById('nomModule').value,
                heure: document.getElementById('heure').value,
                id_formateur: document.getElementById('formateur').value,
                id_filiere: document.getElementById('filiere').value,
                id_branche: document.getElementById('branche').value,
                id_groupe: document.getElementById('groupe').value,
                id_salle: document.getElementById('salle').value
            };

            fetch('back-end/cours_module_api.php?action=add_module', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(moduleData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Module ajouté avec succès !');
                        document.getElementById('moduleForm').reset();
                        loadModules(); // Recharger la liste des modules
                    } else {
                        alert('Erreur : ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données :', error);
                });
        });

        // Récupérer les formateurs depuis la base de données
        function loadFormateurs() {
            fetch('back-end/cours_module_api.php?action=load_formateurs')
                .then(response => response.json())
                .then(data => {
                    const formateurSelect = document.getElementById('formateur');
                    formateurSelect.innerHTML = '<option value="">Sélectionnez un formateur</option>';
                    data.forEach(formateur => {
                        const option = document.createElement('option');
                        option.value = formateur.id;
                        option.textContent = formateur.nom + ' ' + formateur.prenom;
                        formateurSelect.appendChild(option);
                    });
                });
        }

        // Récupérer les filières depuis la base de données
        function loadFilieres() {
            fetch('back-end/cours_module_api.php?action=load_filieres')
                .then(response => response.json())
                .then(data => {
                    const filiereSelect = document.getElementById('filiere');
                    filiereSelect.innerHTML = '<option value="">Sélectionnez une filière</option>';
                    data.forEach(filiere => {
                        const option = document.createElement('option');
                        option.value = filiere.id;
                        option.textContent = filiere.nom;
                        filiereSelect.appendChild(option);
                    });
                });
        }

        // Récupérer les branches en fonction de la filière sélectionnée
        function loadBranches() {
            const id_filiere = document.getElementById('filiere').value;
            if (!id_filiere) return;

            fetch(`back-end/cours_module_api.php?action=load_branches&id_filiere=${id_filiere}`)
                .then(response => response.json())
                .then(data => {
                    const brancheSelect = document.getElementById('branche');
                    brancheSelect.innerHTML = '<option value="">Sélectionnez une branche</option>';
                    data.forEach(branche => {
                        const option = document.createElement('option');
                        option.value = branche.id;
                        option.textContent = branche.nom;
                        brancheSelect.appendChild(option);
                    });
                });
        }

        // Récupérer les salles depuis la base de données
        function loadSalles() {
            fetch('back-end/cours_module_api.php?action=load_salles')
                .then(response => response.json())
                .then(data => {
                    const salleSelect = document.getElementById('salle');
                    salleSelect.innerHTML = '<option value="">Sélectionnez une salle</option>';
                    data.forEach(salle => {
                        const option = document.createElement('option');
                        option.value = salle.id;
                        option.textContent = salle.nom;
                        salleSelect.appendChild(option);
                    });
                });
        }

        // Récupérer les groupes depuis la base de données
        function loadGroupes() {
            fetch('back-end/cours_module_api.php?action=load_groupes')
                .then(response => response.json())
                .then(data => {
                    const groupeSelect = document.getElementById('groupe');
                    groupeSelect.innerHTML = '<option value="">Sélectionnez un groupe</option>';
                    data.forEach(groupe => {
                        const option = document.createElement('option');
                        option.value = groupe.id;
                        option.textContent = groupe.nom;
                        groupeSelect.appendChild(option);
                    });
                });
        }

        // Récupérer et afficher les modules dans un tableau
        function loadModules() {
            fetch('back-end/cours_module_api.php?action=load_modules')
                .then(response => response.json())
                .then(data => {
                    const moduleTableBody = document.querySelector('#moduleTable tbody');
                    moduleTableBody.innerHTML = ''; // Vider le tableau avant de le remplir

                    if (data.length === 0) {
                        // Afficher un message si aucun module n'est trouvé
                        const emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = `<td colspan="7" class="empty-message">Aucun module trouvé. Veuillez en ajouter un.</td>`;
                        moduleTableBody.appendChild(emptyRow);
                    } else {
                        data.forEach(module => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${module.nom}</td>
                                <td>${module.heure} h</td>
                                <td>${module.formateur_nom}</td>
                                <td>${module.filiere_nom}</td>
                                <td>${module.branche_nom}</td>
                                <td>${module.groupe_nom}</td>
                                <td>${module.salle_nom}</td>
                            `;
                            moduleTableBody.appendChild(row);
                        });
                    }
                });
        }
    </script>
</body>

</html>