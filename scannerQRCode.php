<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Vos styles existants... */
        .scan-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .scan-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="my-4">Scanner QR Code</h1>
        <div class="card shadow">
            <div class="card-body">
                <div id="qr-reader" style="width:100%"></div>
                <div id="qr-reader-results"></div>
                <button class="btn btn-primary mt-3" onclick="reprendreScan()">
                    <i class="bi bi-arrow-repeat"></i> Reprendre le Scan
                </button>
                <!-- Zone pour afficher les résultats -->
                <div id="scan-result" class="mt-3" style="display:none;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
    <script>
        let html5QrcodeScanner;

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Scanned: ${decodedText}`);

            // Afficher le résultat temporairement
            document.getElementById('qr-reader-results').innerText = `Code scanné: ${decodedText}`;

            // Envoyer les données au serveur
            fetch('back-end/validateQRCode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    qrData: decodedText,
                    etudiant_id: <?php echo $etudiant_id; ?>
                }),
            })
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('scan-result');
                    resultDiv.style.display = 'block';

                    if (data.success) {
                        resultDiv.className = 'scan-success';
                        resultDiv.innerHTML = `
                        <h4><i class="bi bi-check-circle-fill"></i> Présence enregistrée</h4>
                        <p>Module: ${data.module || 'Non spécifié'}</p>
                    `;
                    } else {
                        resultDiv.className = 'scan-error';
                        resultDiv.innerHTML = `
                        <h4><i class="bi bi-exclamation-triangle-fill"></i> Erreur</h4>
                        <p>${data.message || 'Erreur inconnue'}</p>
                    `;
                    }

                    // Arrêter le scanner après un scan réussi
                    html5QrcodeScanner.clear();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    const resultDiv = document.getElementById('scan-result');
                    resultDiv.style.display = 'block';
                    resultDiv.className = 'scan-error';
                    resultDiv.innerHTML = `
                    <h4><i class="bi bi-exclamation-triangle-fill"></i> Erreur de connexion</h4>
                    <p>Impossible de se connecter au serveur</p>
                `;
                });
        }

        function reprendreScan() {
            document.getElementById('qr-reader-results').innerText = '';
            document.getElementById('scan-result').style.display = 'none';
            html5QrcodeScanner.render(onScanSuccess);
        }

        // Initialiser le scanner
        document.addEventListener('DOMContentLoaded', () => {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { fps: 10, qrbox: 250 });
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
</body>

</html>