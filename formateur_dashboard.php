<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$nom = $_SESSION['nom'] ?? "Inconnu";
$prenom = $_SESSION['prenom'] ?? "";
$nom_complet = $prenom . " " . $nom;
$role = $_SESSION['role'] ?? "Inconnu";
$id_formateur = $_SESSION['user_id'] ?? 0;

if ($role !== 'formateur') {
    header("Location: " . $role . "Space.php");
    exit();
}

// Connexion à la base de données
$host = "localhost";
$dbname = "gestion_presence_qr";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Statistiques
$stmt = $pdo->prepare("SELECT COUNT(*) as total_modules FROM module WHERE id_formateur = ?");
$stmt->execute([$id_formateur]);
$totalModules = $stmt->fetch(PDO::FETCH_ASSOC)['total_modules'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total_seances FROM seance WHERE id_formateur = ?");
$stmt->execute([$id_formateur]);
$totalSeances = $stmt->fetch(PDO::FETCH_ASSOC)['total_seances'];

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT id_groupe) as total_groupes FROM seance WHERE id_formateur = ?");
$stmt->execute([$id_formateur]);
$totalGroupes = $stmt->fetch(PDO::FETCH_ASSOC)['total_groupes'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total_ressources FROM ressources WHERE id_formateur = ?");
$stmt->execute([$id_formateur]);
$totalRessources = $stmt->fetch(PDO::FETCH_ASSOC)['total_ressources'];

$stmt = $pdo->prepare("SELECT SUM(heure) as total_heures FROM module WHERE id_formateur = ?");
$stmt->execute([$id_formateur]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalHeures = $result['total_heures'] ?? 0;

$stmt = $pdo->prepare("
    SELECT s.id, s.date_debut, s.status, s.date_fin,
           g.nom as groupe_nom, m.nom as module_nom, sl.nom as salle_nom
    FROM seance s
    JOIN groupe g ON s.id_groupe = g.id
    JOIN cours c ON s.id_cours = c.id
    JOIN module m ON c.id_module = m.id
    JOIN salle sl ON s.id_salle = sl.id
    WHERE s.id_formateur = ? AND s.date_debut >= NOW()
    ORDER BY s.date_debut ASC
    LIMIT 5
");
$stmt->execute([$id_formateur]);
$prochainesSeances = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT s.id, s.date_debut, s.status, s.date_fin,
           g.nom as groupe_nom, m.nom as module_nom, sl.nom as salle_nom
    FROM seance s
    JOIN groupe g ON s.id_groupe = g.id
    JOIN cours c ON s.id_cours = c.id
    JOIN module m ON c.id_module = m.id
    JOIN salle sl ON s.id_salle = sl.id
    WHERE s.id_formateur = ? AND s.date_debut < NOW()
    ORDER BY s.date_debut DESC
    LIMIT 5
");
$stmt->execute([$id_formateur]);
$seancesPassees = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT p.status, COUNT(*) as count 
    FROM presence p
    JOIN seance s ON p.id_seance = s.id
    WHERE s.id_formateur = ?
    GROUP BY p.status
");
$stmt->execute([$id_formateur]);
$presenceStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
$presenceData = ['present' => 0, 'absent' => 0, 'late' => 0];
foreach ($presenceStats as $stat) {
    $presenceData[$stat['status']] = $stat['count'];
}

$stmt = $pdo->prepare("
    SELECT id, nom, heure 
    FROM module 
    WHERE id_formateur = ?
    ORDER BY nom
");
$stmt->execute([$id_formateur]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT p.id, p.date_time, p.status, e.nom as etudiant_nom, e.prenom as etudiant_prenom,
           m.nom as module_nom, g.nom as groupe_nom
    FROM presence p
    JOIN etudiant e ON p.id_etudiant = e.id
    JOIN seance s ON p.id_seance = s.id
    JOIN cours c ON s.id_cours = c.id
    JOIN module m ON c.id_module = m.id
    JOIN groupe g ON e.id_groupe = g.id
    WHERE s.id_formateur = ?
    ORDER BY p.date_time DESC
    LIMIT 5
");
$stmt->execute([$id_formateur]);
$dernieresPresences = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.id) as total_etudiants
    FROM etudiant e
    JOIN groupe g ON e.id_groupe = g.id
    JOIN seance s ON s.id_groupe = g.id
    WHERE s.id_formateur = ?
");
$stmt->execute([$id_formateur]);
$totalEtudiants = $stmt->fetch(PDO::FETCH_ASSOC)['total_etudiants'];

$stmt = $pdo->prepare("
    SELECT e.jour, e.creneau, m.nom as module_nom, g.nom as groupe_nom, s.nom as salle_nom
    FROM emploi_du_temps e
    JOIN module m ON e.id_module = m.id
    JOIN groupe g ON e.id_groupe = g.id
    JOIN salle s ON e.id_salle = s.id
    WHERE e.id_formateur = ?
    ORDER BY 
        CASE e.jour
            WHEN 'Lundi' THEN 1
            WHEN 'Mardi' THEN 2
            WHEN 'Mercredi' THEN 3
            WHEN 'Jeudi' THEN 4
            WHEN 'Vendredi' THEN 5
            WHEN 'Samedi' THEN 6
        END,
        CASE e.creneau
            WHEN '8h30-11h00' THEN 1
            WHEN '11h00-13h30' THEN 2
            WHEN '13h30-16h00' THEN 3
            WHEN '16h00-18h30' THEN 4
        END
");
$stmt->execute([$id_formateur]);
$emploiDuTemps = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPresences = $presenceData['present'] + $presenceData['absent'] + $presenceData['late'];
$tauxPresence = ($totalPresences > 0) ? round(($presenceData['present'] / $totalPresences) * 100) : 0;
$tauxAbsence = ($totalPresences > 0) ? round(($presenceData['absent'] / $totalPresences) * 100) : 0;
$tauxRetard = ($totalPresences > 0) ? round(($presenceData['late'] / $totalPresences) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Formateur - Gestion de Présence QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ... (reprendre ici le style CSS complet de ton fichier) ... */
        :root {
            --primary-color: #99CDD8;
            --secondary-color: #657166;
            --accent-color: #9AEBE3;
            --light-bg: #FDE8D3;
            --border-color: #F3C3B2;
            --text-color: #657166;
            --text-secondary: #99CDD8;
            --success-color: #9AEBE3;
            --danger-color: #F3C3B2;
            --warning-color: #FDE8D3;
            --shadow: 0 4px 6px rgba(101, 113, 102, 0.15);
            --radius: 8px;
        }

        /* ... (le reste du CSS copié depuis ton fichier) ... */
        /* (voir [1] pour le CSS complet) */
    </style>
</head>

<body>
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
    <div class="container-fluid">
        <div class="top-navbar d-flex justify-content-between">
            <h4 class="logo">Tableau de Bord Formateur</h4>
            <div>
                <span class="text-muted">Aujourd'hui: <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--primary-color);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalModules; ?></div>
                    <div class="stat-label">Modules</div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--accent-color);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalSeances; ?></div>
                    <div class="stat-label">Séances</div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--secondary-color);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalGroupes; ?></div>
                    <div class="stat-label">Groupes</div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--danger-color);">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalRessources; ?></div>
                    <div class="stat-label">Ressources</div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--success-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalHeures; ?></div>
                    <div class="stat-label">Heures de cours</div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="stat-card">
                    <div class="icon" style="background-color: var(--light-bg);">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalEtudiants; ?></div>
                    <div class="stat-label">Étudiants</div>
                </div>
            </div>
      
        <script>
            const ctx = document.getElementById('presenceChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Présent', 'Absent', 'Retard'],
                    datasets: [{
                        data: [<?php echo $presenceData['present']; ?>, <?php echo $presenceData['absent']; ?>, <?php echo $presenceData['late']; ?>],
                        backgroundColor: [
                            'var(--success-color)',
                            'var(--danger-color)',
                            'var(--warning-color)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        </script>

        <!-- Prochaines séances et séances passées -->
        <div class="row">
            <div class="col-md-6">
                <div class="data-table">
                    <h5>Prochaines séances</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Module</th>
                                <th>Groupe</th>
                                <th>Salle</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prochainesSeances as $seance): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($seance['date_debut'])); ?></td>
                                    <td><?php echo htmlspecialchars($seance['module_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($seance['groupe_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($seance['salle_nom']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($seance['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($prochainesSeances)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucune séance à venir</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="data-table">
                    <h5>Dernières séances passées</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Module</th>
                                <th>Groupe</th>
                                <th>Salle</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seancesPassees as $seance): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($seance['date_debut'])); ?></td>
                                    <td><?php echo htmlspecialchars($seance['module_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($seance['groupe_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($seance['salle_nom']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($seance['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($seancesPassees)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucune séance passée</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dernières présences -->
        <div class="row">
            <div class="col-md-12">
                <div class="data-table">
                    <h5>Dernières présences enregistrées</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Étudiant</th>
                                <th>Module</th>
                                <th>Groupe</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernieresPresences as $presence): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($presence['date_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($presence['etudiant_prenom'] . ' ' . $presence['etudiant_nom']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($presence['module_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($presence['groupe_nom']); ?></td>
                                    <td>
                                        <?php
                                        $status = $presence['status'];
                                        $badgeClass = $status === 'present' ? 'badge-present' : ($status === 'absent' ? 'badge-absent' : 'badge-late');
                                        ?>
                                        <span
                                            class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($dernieresPresences)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucune présence enregistrée</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modules enseignés -->
        <div class="row">
            <div class="col-md-12">
                <div class="data-table">
                    <h5>Modules enseignés</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Nombre d'heures</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $module): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($module['nom']); ?></td>
                                    <td><?php echo $module['heure']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($modules)): ?>
                                <tr>
                                    <td colspan="2" class="text-center">Aucun module</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Emploi du temps de la semaine -->
        <div class="row">
            <div class="col-md-12">
                <div class="data-table">
                    <h5>Emploi du temps de la semaine</h5>
                    <div class="calendar-grid">
                        <?php
                        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                        foreach ($jours as $jour) {
                            echo '<div class="calendar-header">' . $jour . '</div>';
                        }
                        $creneaux = ['8h30-11h00', '11h00-13h30', '13h30-16h00', '16h00-18h30'];
                        for ($i = 0; $i < count($creneaux); $i++) {
                            foreach ($jours as $jour) {
                                $coursTrouve = false;
                                foreach ($emploiDuTemps as $cours) {
                                    if ($cours['jour'] === $jour && $cours['creneau'] === $creneaux[$i]) {
                                        echo '<div class="calendar-cell">';
                                        echo '<div class="calendar-time">' . $cours['creneau'] . '</div>';
                                        echo '<div>' . htmlspecialchars($cours['module_nom']) . '</div>';
                                        echo '<div class="text-muted">' . htmlspecialchars($cours['groupe_nom']) . '</div>';
                                        echo '<div class="text-muted">' . htmlspecialchars($cours['salle_nom']) . '</div>';
                                        echo '</div>';
                                        $coursTrouve = true;
                                        break;
                                    }
                                }
                                if (!$coursTrouve) {
                                    echo '<div class="calendar-cell"></div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>