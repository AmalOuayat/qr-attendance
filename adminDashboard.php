<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Rediriger vers la page de connexion si non connecté
    header("Location: login.php");
    exit();
}

// Récupérer les informations utilisateur depuis la session
$nom = $_SESSION['nom'] ?? "Inconnu";
$prenom = $_SESSION['prenom'] ?? "";
$nom_complet = $prenom . " " . $nom;
$role = $_SESSION['role'] ?? "Inconnu";

// Vérifier si l'utilisateur a le bon rôle pour cette page
if ($role !== 'admin') {
    // Rediriger vers la page appropriée si mauvais rôle
    header("Location: " . $role . "Space.php");
    exit();
}

// Connexion à la base de données
$host = "localhost"; // ou votre hôte
$dbname = "gestion_presence_qr";
$username = "root"; // à adapter selon votre configuration
$password = ""; // à adapter selon votre configuration

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Récupérer les statistiques
// 1. Nombre total d'étudiants
$stmt = $pdo->query("SELECT COUNT(*) as total_etudiants FROM etudiant");
$totalEtudiants = $stmt->fetch(PDO::FETCH_ASSOC)['total_etudiants'];

// 2. Nombre total de formateurs
$stmt = $pdo->query("SELECT COUNT(*) as total_formateurs FROM formateur");
$totalFormateurs = $stmt->fetch(PDO::FETCH_ASSOC)['total_formateurs'];

// 3. Nombre total de modules
$stmt = $pdo->query("SELECT COUNT(*) as total_modules FROM module");
$totalModules = $stmt->fetch(PDO::FETCH_ASSOC)['total_modules'];

// 4. Nombre total de salles
$stmt = $pdo->query("SELECT COUNT(*) as total_salles FROM salle");
$totalSalles = $stmt->fetch(PDO::FETCH_ASSOC)['total_salles'];

// 5. Nombre total de séances
$stmt = $pdo->query("SELECT COUNT(*) as total_seances FROM seance");
$totalSeances = $stmt->fetch(PDO::FETCH_ASSOC)['total_seances'];

// 6. Récupérer les statistiques de présence
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM presence GROUP BY status");
$presenceStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
$presenceData = ['present' => 0, 'absent' => 0, 'late' => 0];
foreach ($presenceStats as $stat) {
    $presenceData[$stat['status']] = $stat['count'];
}

// 7. Récupérer les dernières séances
$stmt = $pdo->query("
    SELECT s.id, s.date_debut, s.status, f.nom as formateur_nom, f.prenom as formateur_prenom, 
           g.nom as groupe_nom, m.nom as module_nom, sl.nom as salle_nom
    FROM seance s
    JOIN formateur f ON s.id_formateur = f.id
    JOIN groupe g ON s.id_groupe = g.id
    JOIN cours c ON s.id_cours = c.id
    JOIN module m ON c.id_module = m.id
    JOIN salle sl ON s.id_salle = sl.id
    ORDER BY s.date_debut DESC
    LIMIT 5
");
$dernieresSeances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 8. Récupérer les dernières présences
$stmt = $pdo->query("
    SELECT p.id, p.date_time, p.status, e.nom as etudiant_nom, e.prenom as etudiant_prenom,
           m.nom as module_nom
    FROM presence p
    JOIN etudiant e ON p.id_etudiant = e.id
    JOIN module m ON p.id_module = m.id
    ORDER BY p.date_time DESC
    LIMIT 5
");
$dernieresPresences = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Gestion de Présence QR</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #99CDD8; /* Bleu clair */
            --secondary-color: #657166; /* Gris foncé */
            --accent-color: #9AEBE3; /* Vert d'eau */
            --light-bg: #FDE8D3; /* Beige clair */
            --border-color: #F3C3B2; /* Rose saumon */
            --text-color: #657166; /* Gris foncé pour texte */
            --text-secondary: #99CDD8; /* Bleu clair pour texte secondaire */
            --success-color: #9AEBE3; /* Vert d'eau pour succès */
            --danger-color: #F3C3B2; /* Rose saumon pour danger */
            --warning-color: #FDE8D3; /* Beige clair pour avertissement */
            --shadow: 0 4px 6px rgba(101, 113, 102, 0.15); /* Ombre basée sur le gris */
            --radius: 8px;
        }

        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
        }

        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 0;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: white;
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: var(--shadow);
        }

        .top-navbar {
            background-color: white;
            box-shadow: var(--shadow);
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: var(--radius);
            border-left: 4px solid var(--accent-color);
        }

        .stat-card {
            background-color: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
            border-top: 3px solid var(--border-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .stat-card .icon i {
            font-size: 24px;
            color: white;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        .stat-card .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .data-table {
            background-color: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--border-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background-color: var(--text-secondary);
            border-color: var(--text-secondary);
        }

        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--secondary-color);
            box-shadow: var(--shadow);
        }

        .btn-success:hover {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: var(--secondary-color);
        }

        .btn-warning {
            background-color: var(--light-bg);
            border-color: var(--light-bg);
            color: var(--secondary-color);
            box-shadow: var(--shadow);
        }

        .btn-warning:hover {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: var(--secondary-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
            box-shadow: var(--shadow);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(153, 205, 216, 0.1);
        }

        .badge-present, .bg-success {
            background-color: var(--success-color) !important;
            color: var(--secondary-color);
        }

        .badge-absent, .bg-danger {
            background-color: var(--danger-color) !important;
            color: white;
        }

        .badge-late, .bg-warning {
            background-color: var(--warning-color) !important;
            color: var(--secondary-color);
        }

        .bg-info {
            background-color: var(--light-bg) !important;
            color: var(--secondary-color);
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
            color: white;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-color);
        }

        .dropdown-menu {
            box-shadow: var(--shadow);
            border-radius: var(--radius);
        }

        h5 {
            color: var(--secondary-color);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .table th {
            color: var(--text-secondary);
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }
    </style>
</head>

<body>
    <!-- Header avec uniquement le nom de l'admin -->
    <header class="header">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo substr($prenom, 0, 1) . substr($nom, 0, 1); ?>
            </div>
            <div>
                <div class="fw-bold"><?php echo htmlspecialchars($nom_complet); ?></div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between">
            <h4 class="logo">Tableau de Bord Admin</h4>
            <div>
                <span class="text-muted">Aujourd'hui: <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 col-xl-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--primary-color);">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalEtudiants; ?></div>
                    <div class="stat-label">Étudiants</div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--success-color);">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalFormateurs; ?></div>
                    <div class="stat-label">Formateurs</div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--warning-color);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalModules; ?></div>
                    <div class="stat-label">Modules</div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--danger-color);">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalSalles; ?></div>
                    <div class="stat-label">Salles</div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--secondary-color);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalSeances; ?></div>
                    <div class="stat-label">Séances</div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--accent-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?php echo date('H:i'); ?></div>
                    <div class="stat-label">Heure actuelle</div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="row mb-4">
            <!-- Attendance Chart -->
            <div class="col-md-6 mb-4">
                <div class="data-table">
                    <h5 class="mb-4">Statistiques de Présence</h5>
                    <canvas id="attendanceChart" height="250"></canvas>
                </div>
            </div>

            <!-- Most Recent Sessions -->
            <div class="col-md-6 mb-4">
                <div class="data-table">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Séances Récentes</h5>
                        <a href="seances.php" class="btn btn-sm btn-primary">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Module</th>
                                    <th>Formateur</th>
                                    <th>Groupe</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dernieresSeances as $seance): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($seance['date_debut'])); ?></td>
                                        <td><?php echo htmlspecialchars($seance['module_nom']); ?></td>
                                        <td><?php echo htmlspecialchars($seance['formateur_prenom'] . ' ' . $seance['formateur_nom']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($seance['groupe_nom']); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($seance['status']) {
                                                case 'Planifiée':
                                                    $statusClass = 'bg-info';
                                                    break;
                                                case 'En cour':
                                                    $statusClass = 'bg-primary';
                                                    break;
                                                case 'Terminée':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'Annulée':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                            }
                                            ?>
                                            <span
                                                class="badge <?php echo $statusClass; ?>"><?php echo $seance['status']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($dernieresSeances)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune séance trouvée</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendances -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="data-table">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Enregistrements de Présence Récents</h5>
                        <a href="presences.php" class="btn btn-sm btn-primary">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Étudiant</th>
                                    <th>Module</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dernieresPresences as $presence): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($presence['date_time'])); ?></td>
                                        <td><?php echo htmlspecialchars($presence['etudiant_prenom'] . ' ' . $presence['etudiant_nom']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($presence['module_nom']); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($presence['status']) {
                                                case 'present':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Présent';
                                                    break;
                                                case 'absent':
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Absent';
                                                    break;
                                                case 'late':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Retard';
                                                    break;
                                            }
                                            ?>
                                            <span
                                                class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($dernieresPresences)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun enregistrement de présence trouvé</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="data-table">
                    <h5 class="mb-4">Actions Rapides</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="Gestion_etudiants.php" class="btn btn-primary d-block">
                                <i class="fas fa-user-plus"></i> Ajouter un étudiant
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="Gestion_enseignants.php" class="btn btn-success d-block">
                                <i class="fas fa-user-plus"></i> Ajouter un formateur
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="gestion_emploi.php" class="btn btn-warning d-block">
                                <i class="fas fa-calendar-plus"></i> Planifier une séance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart Initialization -->
    <script>
        // Données pour le graphique de présence
        const attendanceData = {
            labels: ['Présent', 'Absent', 'En retard'],
            datasets: [{
                data: [
                    <?php echo $presenceData['present']; ?>,
                    <?php echo $presenceData['absent']; ?>,
                    <?php echo $presenceData['late']; ?>
                ],
                backgroundColor: [
                    '#9AEBE3', // Présent - Vert d'eau
                    '#F3C3B2', // Absent - Rose saumon
                    '#FDE8D3'  // Retard - Beige clair
                ],
                borderColor: [
                    '#9AEBE3',
                    '#F3C3B2',
                    '#FDE8D3'
                ],
                borderWidth: 1
            }]
        };

        // Configuration du graphique
        const attendanceConfig = {
            type: 'doughnut',
            data: attendanceData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%',
                animation: {
                    animateScale: true
                }
            }
        };

        // Initialisation du graphique
        window.addEventListener('load', function () {
            const attendanceChart = new Chart(
                document.getElementById('attendanceChart'),
                attendanceConfig
            );
        });
    </script>
</body>

</html>