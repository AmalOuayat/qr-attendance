<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Présence | Gestion des présences</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Palette selon l'image fournie */
            --blue: #99CDD8;
            --light-mint: #9AEAE3;
            --cream: #FDE8D3;
            --salmon: #F3C3B2;
            --sage: #CFD6CA;
            --slate: #657166;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Helvetica Neue', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--blue) 0%, var(--sage) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .header {
            width: 100%;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            width: 50px;
            height: 50px;
            background-color: white;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo i {
            font-size: 24px;
            color: var(--slate);
        }

        .brand {
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            width: 100%;
            max-width: 1200px;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        h1 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .feature-card {
            background-color: white;
            border-radius: 16px;
            padding: 1.5rem;
            width: 300px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            background: linear-gradient(135deg, var(--blue) 0%, var(--light-mint) 100%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .feature-icon i {
            color: white;
            font-size: 24px;
        }

        .feature-title {
            color: var(--slate);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-desc {
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .login-container {
            margin-top: 2rem;
            text-align: center;
        }

        .login-btn {
            background-color: var(--slate);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background-color: #5a6559;
        }

        .footer {
            width: 100%;
            padding: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            .features {
                flex-direction: column;
                align-items: center;
            }

            .feature-card {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="brand">QR Présence</div>
        </div>
    </div>

    <div class="main-content">
        <div class="hero-section">
            <h1>Gestion des présences simplifiée</h1>
            <p class="subtitle">Optimisez le suivi des présences grâce à notre système de scan QR Code rapide, fiable et
                sécurisé.</p>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="feature-title">Gain de temps</h3>
                <p class="feature-desc">Enregistrez les présences en quelques secondes avec un simple scan de QR code.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Suivi en temps réel</h3>
                <p class="feature-desc">Consultez les statistiques de présence et générez des rapports détaillés.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="feature-title">Sécurisé</h3>
                <p class="feature-desc">Système sécurisé contre la fraude avec codes uniques et horodatés.</p>
            </div>
        </div>

        <div class="login-container">
            <a href="login.php" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Se connecter
            </a>
        </div>
    </div>

    <div class="footer">
        &copy; 2025 QR Présence | Tous droits réservés
    </div>
</body>

</html>