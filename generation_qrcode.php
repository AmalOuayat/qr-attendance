<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération de QR Codes pour les Séances</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
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
            background-color: #f8f9fa;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .qr-container {
            margin-top: 30px;
        }

        .card {
            border-radius: var(--radius);
            border: none;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-top-left-radius: var(--radius) !important;
            border-top-right-radius: var(--radius) !important;
            border-bottom: none;
        }

        .seance-card {
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
            background-color: white;
        }

        .qr-code-container {
            text-align: center;
            padding: 15px;
            margin-top: 10px;
            background-color: var(--light-bg);
            border-radius: var(--radius);
        }

        #datePickerContainer {
            max-width: 300px;
        }

        .loading-spinner {
            display: none;
            justify-content: center;
            margin: 20px 0;
        }

        .spinner-border {
            color: var(--primary-color) !important;
        }

        .no-seances-message {
            margin: 20px 0;
            text-align: center;
            font-style: italic;
            color: var(--text-secondary);
            padding: 20px;
            background-color: var(--light-bg);
            border-radius: var(--radius);
        }

        .qr-code-image {
            max-width: 200px;
            max-height: 200px;
            margin: 0 auto;
            border: 4px solid white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .error-message {
            margin: 20px 0;
            text-align: center;
            color: var(--danger-color);
            font-weight: bold;
            padding: 15px;
            background-color: rgba(243, 195, 178, 0.2);
            border-radius: var(--radius);
            border-left: 4px solid var(--danger-color);
        }

        .download-btn {
            margin-top: 15px;
            background-color: var(--secondary-color);
            border: none;
            border-radius: var(--radius);
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background-color: var(--text-color);
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--text-secondary);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border: none;
            color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #556055;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success-color);
            border: none;
            color: var(--secondary-color);
            font-weight: bold;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #88d4cd;
            transform: translateY(-2px);
        }

        .form-control {
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            padding: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(153, 205, 216, 0.25);
            border-color: var(--primary-color);
        }

        .form-label {
            color: var(--secondary-color);
            font-weight: 600;
        }

        /* Styles pour Flatpickr */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected.prevMonthDay,
        .flatpickr-day.startRange.prevMonthDay,
        .flatpickr-day.endRange.prevMonthDay,
        .flatpickr-day.selected.nextMonthDay,
        .flatpickr-day.startRange.nextMonthDay,
        .flatpickr-day.endRange.nextMonthDay {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .flatpickr-day.selected.startRange+.endRange:not(:nth-child(7n+1)),
        .flatpickr-day.startRange.startRange+.endRange:not(:nth-child(7n+1)),
        .flatpickr-day.endRange.startRange+.endRange:not(:nth-child(7n+1)) {
            box-shadow: -10px 0 0 var(--primary-color);
        }

        .flatpickr-day.week.selected {
            box-shadow: -5px 0 0 var(--primary-color), 5px 0 0 var(--primary-color);
        }

        /* Améliorations pour les informations de séance */
        .seance-info {
            padding: 10px;
            background-color: rgba(153, 205, 216, 0.1);
            border-radius: var(--radius);
            margin-bottom: 15px;
        }

        .seance-info p {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .seance-info p i {
            margin-right: 8px;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .seance-title {
            color: white;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .seance-card {
                page-break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .container-fluid {
                width: 100%;
                padding: 0;
            }

            .qr-code-container {
                background-color: transparent;
            }

            .qr-code-image {
                border: 1px solid #ddd;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid qr-container">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="seance-title">Génération de QR Codes pour les Séances</h6>
                <a href="gestion_emploi.php" class="btn btn-secondary no-print">
                    <i class="fas fa-calendar"></i> Retour à l'emploi du temps
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4 no-print">
                    <div class="col-md-4" id="datePickerContainer">
                        <label for="datePicker" class="form-label">Sélectionner une date</label>
                        <input type="text" class="form-control" id="datePicker" placeholder="JJ/MM/AAAA">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="rechercherBtn" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher les séances du jour
                        </button>
                    </div>
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <button id="printBtn" class="btn btn-success">
                            <i class="fas fa-print"></i> Imprimer tous les QR Codes
                        </button>
                    </div>
                </div>

                <div class="loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>

                <div id="errorMessage" class="error-message" style="display: none;"></div>

                <div id="noSeancesMessage" class="no-seances-message" style="display: none;">
                    <i class="fas fa-info-circle mr-2"></i> Aucune séance trouvée pour cette date.
                </div>

                <div id="seancesContainer" class="row">
                    <!-- Les séances seront injectées ici par JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, et autres bibliothèques -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/fr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // URL de l'API - Assurez-vous que ce chemin est correct
            const apiUrl = 'back-end/qrcode_api.php';

            // Éléments DOM
            const errorMessageEl = document.getElementById('errorMessage');
            const loadingSpinnerEl = document.querySelector('.loading-spinner');
            const noSeancesMessageEl = document.getElementById('noSeancesMessage');
            const seancesContainerEl = document.getElementById('seancesContainer');

            // Initialiser Flatpickr (le sélecteur de date)
            const datePicker = flatpickr("#datePicker", {
                dateFormat: "Y-m-d",
                locale: "fr",
                defaultDate: new Date(),
                maxDate: new Date().fp_incr(30), // Permet de sélectionner jusqu'à 30 jours dans le futur
                minDate: new Date().fp_incr(-30), // Permet de sélectionner jusqu'à 30 jours dans le passé
            });

            // Initialisation
            init();

            // Fonction d'initialisation
            function init() {
                // Ajouter les écouteurs d'événements
                document.getElementById('rechercherBtn').addEventListener('click', rechercherSeances);
                document.getElementById('printBtn').addEventListener('click', imprimerQRCodes);

                // Charger les séances pour la date du jour par défaut
                rechercherSeances();
            }

            // Fonction pour rechercher les séances d'un jour
            function rechercherSeances() {
                const date = document.getElementById('datePicker').value;

                if (!date) {
                    afficherErreur('Veuillez sélectionner une date valide.');
                    return;
                }

                // Réinitialiser l'interface
                resetUI();

                // Afficher le spinner de chargement
                loadingSpinnerEl.style.display = 'flex';

                // Appeler l'API pour récupérer les séances du jour
                fetch(`${apiUrl}?date=${date}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Cacher le spinner
                        loadingSpinnerEl.style.display = 'none';

                        // Vérifier que les données sont bien formatées
                        if (!data || typeof data !== 'object') {
                            throw new Error('Format de réponse invalide');
                        }

                        if (data.success && data.seances && data.seances.length > 0) {
                            // Afficher les séances
                            afficherSeances(data.seances);
                        } else {
                            // Afficher un message si aucune séance n'est trouvée
                            noSeancesMessageEl.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération des séances:', error);
                        loadingSpinnerEl.style.display = 'none';
                        afficherErreur('Erreur lors de la récupération des séances. Veuillez vérifier que l\'API est accessible.');
                    });
            }

            // Réinitialiser l'interface utilisateur
            function resetUI() {
                errorMessageEl.style.display = 'none';
                errorMessageEl.textContent = '';
                noSeancesMessageEl.style.display = 'none';
                seancesContainerEl.innerHTML = '';
            }

            // Afficher un message d'erreur
            function afficherErreur(message) {
                errorMessageEl.textContent = message;
                errorMessageEl.style.display = 'block';
                loadingSpinnerEl.style.display = 'none';
            }

            // Fonction pour afficher les séances
            function afficherSeances(seances) {
                seances.forEach(seance => {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-md-6 col-lg-4';

                    const card = document.createElement('div');
                    card.className = 'card seance-card';
                    card.dataset.id = seance.id;

                    const cardHeader = document.createElement('div');
                    cardHeader.className = 'card-header py-2';
                    cardHeader.innerHTML = `<h6 class="seance-title">${seance.module_nom}</h6>`;

                    const cardBody = document.createElement('div');
                    cardBody.className = 'card-body';

                    try {
                        // Formatage de la date
                        const dateObj = new Date(seance.date_reelle);
                        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                        const formattedDate = dateObj.toLocaleDateString('fr-FR', options);

                        // Informations sur la séance avec des icônes
                        const infoDiv = document.createElement('div');
                        infoDiv.className = 'seance-info';
                        infoDiv.innerHTML = `
                            <p><i class="fas fa-calendar-day"></i> <strong>Date:</strong> ${formattedDate}</p>
                            <p><i class="fas fa-clock"></i> <strong>Horaire:</strong> ${seance.creneau}</p>
                            <p><i class="fas fa-user-tie"></i> <strong>Formateur:</strong> ${seance.formateur_nom} ${seance.formateur_prenom}</p>
                            <p><i class="fas fa-users"></i> <strong>Groupe:</strong> ${seance.groupe_nom}</p>
                            <p><i class="fas fa-door-open"></i> <strong>Salle:</strong> ${seance.salle_nom}</p>
                        `;

                        // Conteneur pour le QR code
                        const qrCodeContainer = document.createElement('div');
                        qrCodeContainer.className = 'qr-code-container';
                        qrCodeContainer.id = `qr-code-${seance.id_seance}`;

                        // Conteneur pour le bouton de téléchargement
                        const downloadContainer = document.createElement('div');
                        downloadContainer.className = 'text-center no-print';
                        downloadContainer.innerHTML = `
                            <button class="btn btn-primary download-btn" data-id="${seance.id_seance}">
                                <i class="fas fa-download"></i> Télécharger le QR Code
                            </button>
                        `;

                        cardBody.appendChild(infoDiv);
                        cardBody.appendChild(qrCodeContainer);
                        cardBody.appendChild(downloadContainer);

                        card.appendChild(cardHeader);
                        card.appendChild(cardBody);

                        colDiv.appendChild(card);
                        seancesContainerEl.appendChild(colDiv);

                        // Générer le QR code
                        if (seance.qr_data) {
                            genererQRCode(seance.qr_data, `qr-code-${seance.id_seance}`, seance.module_nom);
                        } else {
                            console.error('Données QR manquantes pour la séance:', seance);
                        }
                    } catch (err) {
                        console.error('Erreur lors de l\'affichage de la séance:', err, seance);
                    }
                });

                // Ajouter les écouteurs d'événements pour les boutons de téléchargement
                document.querySelectorAll('.download-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const seanceId = this.getAttribute('data-id');
                        telechargerQRCode(seanceId);
                    });
                });
            }

            // Fonction pour générer un QR code
            function genererQRCode(data, containerId, moduleName) {
                const qrContainer = document.getElementById(containerId);
                if (!qrContainer) {
                    console.error(`Conteneur QR non trouvé: ${containerId}`);
                    return;
                }

                qrContainer.innerHTML = ''; // Vider le conteneur

                try {
                    // Créer le QR code
                    new QRCode(qrContainer, {
                        text: data,
                        width: 200,
                        height: 200,
                        colorDark: "#657166", // Utiliser la couleur du texte pour le QR code
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });

                    // Ajouter la classe à l'image générée
                    setTimeout(() => {
                        const imgElement = qrContainer.querySelector('img');
                        if (imgElement) {
                            imgElement.classList.add('qr-code-image');
                            // Ajouter un attribut data-module pour le téléchargement
                            imgElement.setAttribute('data-module', moduleName);
                        }
                    }, 100);
                } catch (err) {
                    console.error('Erreur lors de la génération du QR code:', err);
                    qrContainer.innerHTML = '<p class="text-danger">Erreur lors de la génération du QR code</p>';
                }
            }

            // Fonction pour télécharger un QR code
            function telechargerQRCode(seanceId) {
                const qrContainer = document.getElementById(`qr-code-${seanceId}`);
                if (!qrContainer) {
                    console.error(`Conteneur QR non trouvé pour la séance: ${seanceId}`);
                    return;
                }

                const imgElement = qrContainer.querySelector('img');
                if (!imgElement) {
                    console.error('Image QR code non trouvée');
                    return;
                }

                // Récupérer le nom du module pour le nom du fichier
                const moduleName = imgElement.getAttribute('data-module') || `seance_${seanceId}`;
                const fileName = `QRCode_${moduleName.replace(/\s+/g, '_')}_${seanceId}.png`;

                // Créer un lien temporaire pour le téléchargement
                const link = document.createElement('a');
                link.href = imgElement.src;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Fonction pour imprimer tous les QR codes
            function imprimerQRCodes() {
                const seancesContainer = document.getElementById('seancesContainer');

                if (seancesContainer.children.length === 0) {
                    alert('Aucun QR code à imprimer. Veuillez d\'abord rechercher des séances.');
                    return;
                }

                window.print();
            }
        });
    </script>
</body>

</html>