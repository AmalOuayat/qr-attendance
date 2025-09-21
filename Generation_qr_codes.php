<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération QR Code - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Palette de couleurs */
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

        /* Styles généraux */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            padding: 2rem;
            background-color: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        h2 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }

        .text-muted {
            color: var(--secondary-color) !important;
            opacity: 0.8;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(101, 113, 102, 0.2);
        }

        .card-header {
            background-color: var(--accent-color) !important;
            color: var(--secondary-color) !important;
            font-weight: 600;
            border-bottom: none;
            padding: 1rem;
        }

        .card-body {
            padding: 1.5rem;
            background-color: #fff;
        }

        /* Formulaires */
        .form-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-select,
        .form-control {
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            padding: 0.7rem 1rem;
            color: var(--text-color);
            background-color: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(153, 205, 216, 0.25);
        }

        /* Boutons */
        .btn {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: var(--radius);
            transition: all 0.3s ease;
            border: none;
            box-shadow: var(--shadow);
        }

        .btn-success {
            background-color: var(--success-color);
            color: var(--secondary-color);
        }

        .btn-success:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(154, 235, 227, 0.4);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #88b9c4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(153, 205, 216, 0.4);
        }

        /* QR Code iframe */
        #qr {
            border: 2px solid var(--border-color);
            border-radius: var(--radius);
            background-color: var(--light-bg);
            padding: 0.5rem;
            transition: border-color 0.3s ease;
        }

        #qr:hover {
            border-color: var(--primary-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .card {
                margin-bottom: 1.5rem;
            }

            #qr {
                height: 280px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .card,
        #qr,
        .btn {
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Amélioration de l'accessibilité */
        .btn:focus,
        .form-select:focus {
            outline: none;
        }

        /* Personnalisation des alertes SweetAlert */
        .swal2-popup {
            border-radius: var(--radius);
            padding: 2rem;
        }

        .swal2-title {
            color: var(--secondary-color);
        }

        .swal2-confirm {
            background-color: var(--primary-color) !important;
        }

        /* Style pour le téléchargement */
        #btnDownload {
            display: block;
            margin: 1rem auto;
            min-width: 200px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h2>Génération des QR Codes par Module</h2>
                <p class="text-muted">Générez et gérez les QR codes pour l'enregistrement des présences</p>
            </div>
        </div>
        <form action="qr.php" target="qr">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            Générer QR Code
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="selectModule" class="form-label">Sélectionnez un module</label>
                                <select id="selectModule" name="idm" class="form-select">
                                    <option value="">Choisir un module</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="selectSeance" class="form-label">Sélectionnez une séance</label>
                                <select id="selectSeance" name="ids" class="form-select">
                                    <option value="">Choisir une séance</option>
                                </select>
                            </div>
                            <button id="btnGenerate" class="btn btn-success">Générer QR Code</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <iframe src="qr.php" name="qr" id="qr" frameborder="0" width="100%" style="height: 340px;"></iframe>
                </div>
                <button type="button" id="btnDownload" class="btn btn-primary mt-3">Télécharger QR Code</button>
            </div>
        </form>
        <!-- Colonne pour afficher le QR Code généré -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // API base URL - À adapter selon votre configuration
            const API_URL = 'back-end/qr_api.php';
            const QR_GENERATOR_URL = 'qr.php';

            document.addEventListener('DOMContentLoaded', function () {
                // Charger les modules au chargement de la page
                loadModules();
                loadQRCodes();

                // Écouteurs d'événements
                document.getElementById('btnGenerate').addEventListener('click', generateQRCode);
                document.getElementById('btnDownload').addEventListener('click', downloadQRCode);

                // Charger les séances lorsqu'un module est sélectionné
                document.getElementById('selectModule').addEventListener('change', function () {
                    loadSeances(this.value);
                });

            });

            function loadModules() {
                fetch(`${API_URL}?action=getModules`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const select = document.getElementById('selectModule');
                            select.innerHTML = '<option value="">Choisir un module</option>';

                            data.modules.forEach(module => {
                                const option = document.createElement('option');
                                option.value = module.id;
                                option.textContent = module.nom;
                                select.appendChild(option);
                            });
                        } else {
                            showError('Erreur lors du chargement des modules', data.message);
                        }
                    })
                    .catch(error => showError('Erreur réseau', error.message));
            }

            function loadSeances(moduleId) {
                if (!moduleId) {
                    document.getElementById('selectSeance').innerHTML = '<option value="">Choisir une séance</option>';
                    return;
                }

                fetch(`${API_URL}?action=getSeances&moduleId=${moduleId}`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.getElementById('selectSeance');
                        select.innerHTML = '<option value="">Choisir une séance</option>';

                        if (data.status === 'success' && data.seances.length > 0) {
                            data.seances.forEach(seance => {
                                const option = document.createElement('option');
                                option.value = seance.id;

                                const dateDebut = new Date(seance.date_debut);
                                const dateFin = new Date(seance.date_fin);
                                const dateStr = dateDebut.toLocaleDateString('fr-FR');
                                const timeStr = `${dateDebut.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })} - ${dateFin.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;

                                option.textContent = `${dateStr} ${timeStr} - ${seance.status}`;
                                select.appendChild(option);
                            });
                        } else {
                            select.innerHTML += '<option value="" disabled>Aucune séance disponible</option>';
                        }
                    })
                    .catch(error => showError('Erreur réseau', error.message));
            }

            function loadQRCodes() {
            }

            function generateQRCode() {
                const seanceId = document.getElementById('selectSeance').value;

                if (!seanceId) {
                    showError('Erreur', 'Veuillez sélectionner une séance');
                    return;
                }

                fetch(`${QR_GENERATOR_URL}?id=${seanceId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur de génération');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = URL.createObjectURL(blob);

                        // Récupérer les infos de la séance pour l'affichage
                        const module = document.getElementById('selectModule').options[document.getElementById('selectModule').selectedIndex].text;
                        const seance = document.getElementById('selectSeance').options[document.getElementById('selectSeance').selectedIndex].text;

                        loadQRCodes();
                    })

            }

            function downloadQRCode() {
                const qrFrame = document.getElementById('qr');
                const qrDoc = qrFrame.contentDocument || qrFrame.contentWindow.document;
                const qrImg = qrDoc.querySelector('img'); // Récupérer l'image du QR Code

                if (!qrImg) {
                    showError('Erreur', 'Veuillez générer un QR Code avant de le télécharger');
                    return;
                }

                const link = document.createElement('a');
                link.href = qrImg.src;
                link.download = 'QR_Code.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function showError(title, message) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        </script>
</body>

</html>