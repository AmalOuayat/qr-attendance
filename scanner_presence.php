<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code - Gestion Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .scan-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }

        #video {
            width: 100%;
            background: #000;
            margin: 20px 0;
            border-radius: 8px;
        }

        #result {
            margin-top: 20px;
            min-height: 60px;
        }

        .seance-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #4e73df;
        }

        .btn-scan {
            margin: 5px;
            padding: 10px 20px;
            font-weight: 500;
        }

        #imagePreview {
            max-width: 100%;
            max-height: 300px;
            margin-top: 15px;
            display: none;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }

        .preview-container {
            text-align: center;
            margin: 15px 0;
        }

        .btn-import {
            position: relative;
            overflow: hidden;
        }

        .btn-group-scan {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        #gpsStatus {
            transition: all 0.3s ease;
        }

        #gpsStatus.gps-ok {
            background-color: #d1e7dd;
            border-color: #badbcc;
        }

        #gpsStatus.gps-error {
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        .status-present {
            background-color: #d1e7dd;
            border-color: #badbcc;
        }

        .status-late {
            background-color: #fff3cd;
            border-color: #ffecb5;
        }

        .status-absent {
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        @media (max-width: 576px) {
            .scan-container {
                margin: 15px;
                padding: 15px;
            }

            .btn-group-scan {
                flex-direction: column;
            }

            .btn-scan {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container scan-container">
        <h2 class="text-center mb-4">
            <i class="bi bi-qr-code-scan"></i> Validation de présence
        </h2>

        <div class="btn-group-scan">
            <button id="startButton" class="btn btn-primary btn-scan">
                <i class="bi bi-camera"></i> Scanner avec la caméra
            </button>
            <button id="importButton" class="btn btn-secondary btn-scan btn-import">
                <i class="bi bi-upload"></i> Importer une photo
                <input type="file" id="fileInput" accept="image/*"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
            </button>
            <button id="stopButton" class="btn btn-danger btn-scan" disabled>
                <i class="bi bi-stop-circle"></i> Arrêter
            </button>
        </div>

        <video id="video" playsinline></video>

        <div class="preview-container">
            <img id="imagePreview" alt="Aperçu de l'image importée">
        </div>

        <div id="result" class="text-center"></div>

        <div id="gpsStatus" class="alert alert-info mt-3">
            <i class="bi bi-geo-alt"></i> <span id="gpsStatusText">La géolocalisation est obligatoire pour enregistrer
                votre présence.</span>
            <div id="gpsAccuracy" class="small mt-1"></div>
        </div>

        <div id="seanceDetails" class="seance-info" style="display: none;">
            <h4><i class="bi bi-info-circle"></i> Détails de la séance</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong><i class="bi bi-book"></i> Module:</strong> <span id="moduleName"></span></p>
                    <p><strong><i class="bi bi-person"></i> Formateur:</strong> <span id="formateurName"></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong><i class="bi bi-geo-alt"></i> Salle:</strong> <span id="salleName"></span></p>
                    <p><strong><i class="bi bi-calendar"></i> Date:</strong> <span id="seanceDate"></span></p>
                </div>
            </div>
            <p class="text-center"><strong><i class="bi bi-clock"></i> Horaire:</strong> <span id="seanceTime"></span>
            </p>
            <p class="text-center"><strong><i class="bi bi-info-circle"></i> Statut:</strong> <span id="seanceStatus"
                    class="badge bg-primary">Non déterminé</span></p>

            <div class="d-grid gap-2 mt-3">
                <button id="confirmBtn" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Confirmer la présence
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        // Éléments DOM
        const video = document.getElementById("video");
        const startButton = document.getElementById("startButton");
        const stopButton = document.getElementById("stopButton");
        const importButton = document.getElementById("importButton");
        const fileInput = document.getElementById("fileInput");
        const resultDiv = document.getElementById("result");
        const seanceDetails = document.getElementById("seanceDetails");
        const confirmBtn = document.getElementById("confirmBtn");
        const imagePreview = document.getElementById("imagePreview");
        const gpsStatus = document.getElementById("gpsStatus");
        const gpsStatusText = document.getElementById("gpsStatusText");
        const gpsAccuracy = document.getElementById("gpsAccuracy");
        const seanceStatusBadge = document.getElementById("seanceStatus");

        // Variables d'état
        let scanning = false;
        let stream = null;
        let currentPosition = null;

        // Initialisation
        document.addEventListener("DOMContentLoaded", () => {
            // Vérifier la compatibilité de l'API MediaDevices
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Votre navigateur ne supporte pas l'accès à la caméra.
                        Veuillez utiliser la fonction d'import d'image.
                    </div>
                `;
                startButton.disabled = true;
            }

            // Vérifier la géolocalisation au chargement
            checkGeolocationSupport();
        });

        // Vérifier le support de la géolocalisation
        function checkGeolocationSupport() {
            if (!navigator.geolocation) {
                gpsStatus.classList.add("gps-error");
                gpsStatusText.textContent = "Votre navigateur ne supporte pas la géolocalisation";
                return false;
            }
            return true;
        }

        // Obtenir la position actuelle
        async function getCurrentPosition() {
            return new Promise((resolve, reject) => {
                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentPosition = position;
                        updateGpsStatus(position);
                        resolve(position);
                    },
                    (error) => {
                        handleGeolocationError(error);
                        reject(error);
                    },
                    options
                );
            });
        }

        // Mettre à jour l'affichage du statut GPS
        function updateGpsStatus(position) {
            const accuracy = Math.round(position.coords.accuracy);
            gpsAccuracy.textContent = `Précision: ${accuracy}m`;

            if (accuracy <= 50) {
                gpsStatus.classList.remove("gps-error");
                gpsStatus.classList.add("gps-ok");
                gpsStatusText.textContent = "Localisation validée";
            } else {
                gpsStatus.classList.remove("gps-ok");
                gpsStatus.classList.add("gps-error");
                gpsStatusText.textContent = "Précision GPS insuffisante";
            }
        }

        // Gérer les erreurs de géolocalisation
        function handleGeolocationError(error) {
            let errorMsg = "Erreur de géolocalisation: ";
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg += "Vous devez autoriser la géolocalisation";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg += "Position indisponible";
                    break;
                case error.TIMEOUT:
                    errorMsg += "Délai dépassé";
                    break;
                default:
                    errorMsg += "Erreur inconnue";
            }

            gpsStatus.classList.remove("gps-ok");
            gpsStatus.classList.add("gps-error");
            gpsStatusText.textContent = errorMsg;
            gpsAccuracy.textContent = "";
        }

        // Démarrer le scan caméra
        startButton.addEventListener("click", async () => {
            try {
                // Vérifier la géolocalisation
                await getCurrentPosition();

                // Arrêter le scan précédent si actif
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                // Réinitialiser l'interface
                resetInterface();
                imagePreview.style.display = "none";

                // Démarrer la caméra
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment",
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });

                video.srcObject = stream;
                video.play();
                scanning = true;
                startButton.disabled = true;
                stopButton.disabled = false;
                importButton.disabled = true;
                resultDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-search"></i> Recherche de QR code en cours...
                    </div>
                `;
                seanceDetails.style.display = "none";

                // Démarrer la détection
                requestAnimationFrame(scanQR);
            } catch (err) {
                console.error("Erreur caméra:", err);
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-camera-video-off"></i> Erreur: ${err.message || "Accès à la caméra refusé"}
                    </div>
                `;
                startButton.disabled = false;
                stopButton.disabled = true;
                importButton.disabled = false;
            }
        });

        // Arrêter le scan
        stopButton.addEventListener("click", stopScan);

        // Gérer la sélection de fichier
        fileInput.addEventListener("change", async (e) => {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];

                // Vérifier que c'est bien une image
                if (!file.type.match('image.*')) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-file-earmark-image"></i> Veuillez sélectionner une image valide (JPEG, PNG)
                        </div>
                    `;
                    return;
                }

                // Vérifier la taille du fichier (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-octagon"></i> L'image est trop volumineuse (max 5MB)
                        </div>
                    `;
                    return;
                }

                // Vérifier la géolocalisation
                try {
                    await getCurrentPosition();
                } catch (err) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-geo-alt"></i> ${err.message}
                        </div>
                    `;
                    return;
                }

                const reader = new FileReader();

                reader.onload = (event) => {
                    // Afficher l'aperçu
                    imagePreview.src = event.target.result;
                    imagePreview.style.display = "block";

                    // Arrêter la caméra si active
                    if (stream) {
                        stopScan();
                    }

                    // Analyser l'image
                    resultDiv.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-search"></i> Analyse de l'image en cours...
                        </div>
                    `;
                    analyzeImageForQR(event.target.result);
                };

                reader.onerror = () => {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-file-earmark-break"></i> Erreur lors de la lecture du fichier
                        </div>
                    `;
                };

                reader.readAsDataURL(file);
            }
        });

        // Fonction pour analyser le flux vidéo
        function scanQR() {
            if (!scanning) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert"
                });

                if (code) {
                    try {
                        const qrData = JSON.parse(code.data);
                        displaySeanceInfo(qrData);
                    } catch (e) {
                        resultDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-qr-code"></i> QR code invalide ou non reconnu
                            </div>
                        `;
                        // Continuer à scanner
                        requestAnimationFrame(scanQR);
                    }
                } else {
                    requestAnimationFrame(scanQR);
                }
            } else {
                requestAnimationFrame(scanQR);
            }
        }

        // Fonction pour analyser une image statique
        function analyzeImageForQR(imageSrc) {
            const img = new Image();

            img.onload = function () {
                const canvas = document.createElement("canvas");
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert"
                });

                if (code) {
                    try {
                        const qrData = JSON.parse(code.data);
                        displaySeanceInfo(qrData);
                    } catch (e) {
                        resultDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-qr-code"></i> QR code invalide ou non reconnu
                            </div>
                        `;
                    }
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-qr-code-scan"></i> Aucun QR code détecté dans l'image
                        </div>
                    `;
                }
            };

            img.onerror = () => {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-image-alt"></i> Erreur lors du chargement de l'image
                    </div>
                `;
            };

            img.src = imageSrc;
        }

        // Afficher les infos de la séance
        function displaySeanceInfo(qrData) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> QR code reconnu avec succès!
                </div>
            `;

            // Remplir les informations
            document.getElementById("moduleName").textContent = qrData.module || "Non spécifié";
            document.getElementById("formateurName").textContent = qrData.formateur || "Non spécifié";
            document.getElementById("salleName").textContent = qrData.salle || "Non spécifié";

            // Formater la date (avec fuseau horaire Maroc)
            const dateObj = new Date(qrData.date);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'Africa/Casablanca'
            };
            document.getElementById("seanceDate").textContent = dateObj.toLocaleDateString('fr-FR', options);
            document.getElementById("seanceTime").textContent = qrData.creneau || "Non spécifié";

            // Calculer le statut prévisionnel (avec heure Maroc)
            const now = new Date();
            const dateDebut = new Date(qrData.date);
            const dateFin = new Date(dateDebut.getTime() + 2 * 60 * 60 * 1000); // +2h
            const limitePresent = new Date(dateDebut.getTime() + 15 * 60 * 1000); // +15min

            // Debug: Afficher les heures dans la console
            console.log("Heure actuelle Maroc:", now.toLocaleString('fr-FR', { timeZone: 'Africa/Casablanca' }));
            console.log("Début séance:", dateDebut.toLocaleString('fr-FR', { timeZone: 'Africa/Casablanca' }));
            console.log("Limite présent:", limitePresent.toLocaleString('fr-FR', { timeZone: 'Africa/Casablanca' }));
            console.log("Fin séance:", dateFin.toLocaleString('fr-FR', { timeZone: 'Africa/Casablanca' }));

            if (now < dateDebut) {
                seanceStatusBadge.textContent = "À venir";
                seanceStatusBadge.className = "badge bg-secondary";
            } else if (now <= limitePresent) {
                seanceStatusBadge.textContent = "Présent";
                seanceStatusBadge.className = "badge bg-success";
            } else if (now <= dateFin) {
                seanceStatusBadge.textContent = "En retard";
                seanceStatusBadge.className = "badge bg-warning text-dark";
            } else {
                seanceStatusBadge.textContent = "Absent";
                seanceStatusBadge.className = "badge bg-danger";
            }

            // Stocker les données pour la validation
            seanceDetails.style.display = "block";
            seanceDetails.dataset.qrData = JSON.stringify(qrData);

            // Arrêter le scan si actif
            if (stream) {
                stopScan();
            }
        }

        // Confirmer la présence
        confirmBtn.addEventListener("click", async () => {
            const qrData = seanceDetails.dataset.qrData;

            if (!qrData) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-octagon"></i> Erreur: données de séance manquantes
                    </div>
                `;
                return;
            }

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Validation en cours...
            `;

            try {
                const position = await getCurrentPosition();
                const qrDataObj = JSON.parse(qrData);

                // Ajouter les données de géolocalisation
                qrDataObj.latitude = position.coords.latitude;
                qrDataObj.longitude = position.coords.longitude;
                qrDataObj.accuracy = position.coords.accuracy;

                const response = await fetch('back-end/validate_presence.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(qrDataObj)
                });

                const data = await response.json();

                // Déterminer la classe CSS en fonction du statut
                let alertClass, iconClass;
                switch (data.status) {
                    case 'present':
                        alertClass = 'alert-success status-present';
                        iconClass = 'bi-check-circle-fill';
                        break;
                    case 'late':
                        alertClass = 'alert-warning status-late';
                        iconClass = 'bi-exclamation-triangle-fill';
                        break;
                    case 'absent':
                        alertClass = 'alert-danger status-absent';
                        iconClass = 'bi-x-circle-fill';
                        break;
                    default:
                        alertClass = 'alert-info';
                        iconClass = 'bi-info-circle-fill';
                }

                resultDiv.innerHTML = `
                    <div class="alert ${alertClass}">
                        <i class="bi ${iconClass}"></i> ${data.message} (Statut: ${data.status})
                        ${data.presence_id ? `<br>ID: ${data.presence_id}` : ''}
                        ${data.distance ? `<br>Distance: ${Math.round(data.distance)}m` : ''}
                    </div>
                `;

                seanceDetails.style.display = "none";
                setTimeout(resetInterface, 3000);

            } catch (err) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-octagon-fill"></i> Erreur: ${err.message || "Problème de connexion"}
                    </div>
                `;
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `
                    <i class="bi bi-check-circle"></i> Confirmer la présence
                `;
            }
        });

        // Réinitialiser l'interface
        function resetInterface() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            scanning = false;
            startButton.disabled = false;
            stopButton.disabled = true;
            importButton.disabled = false;
            imagePreview.style.display = "none";
            imagePreview.src = "";
            fileInput.value = "";
            seanceDetails.style.display = "none";
            video.srcObject = null;
        }

        // Arrêter le scan
        function stopScan() {
            resetInterface();
            resultDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-pause-circle"></i> Scan arrêté
                </div>
            `;
        }
    </script>
</body>

</html>