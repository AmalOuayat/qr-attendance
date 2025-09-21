<?php
session_start();
require_once 'back-end/api.php'; // Fichier de configuration de la base de données


$formateur_id = $_SESSION['formateur_id'];

// Récupérer les informations du formateur
$stmt = $pdo->prepare("SELECT * FROM formateur WHERE id = ?");
$stmt->execute([$formateur_id]);
$formateur = $stmt->fetch();

// Récupérer les modules enseignés par le formateur
$modules = $pdo->prepare("SELECT * FROM module WHERE id_formateur = ?");
$modules->execute([$formateur_id]);
$modules_list = $modules->fetchAll();

// Récupérer les groupes associés
$groupes = $pdo->query("SELECT * FROM groupe");
$groupes_list = $groupes->fetchAll();

// Traitement du formulaire d'ajout de ressource
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_ressource'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $id_module = $_POST['module'];
    $id_groupe = $_POST['groupe'];
    
    // Gestion du fichier uploadé
    $upload_dir = 'uploads/';
    $fichier_name = basename($_FILES['fichier']['name']);
    $fichier_path = $upload_dir . $fichier_name;
    
    if (move_uploaded_file($_FILES['fichier']['tmp_name'], $fichier_path)) {
        // Insertion dans la base de données
        $insert = $pdo->prepare("INSERT INTO ressources (titre, description, fichier_url, date_ajout, id_module, id_formateur) 
                                VALUES (?, ?, ?, NOW(), ?, ?)");
        $insert->execute([$titre, $description, $fichier_path, $id_module, $formateur_id]);
        
        // Lier la ressource au groupe
        $ressource_id = $pdo->lastInsertId();
        $link = $pdo->prepare("INSERT INTO ressources_groupes (id_ressource, id_groupe) VALUES (?, ?)");
        $link->execute([$ressource_id, $id_groupe]);
        
        $_SESSION['success'] = "Ressource ajoutée avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de l'upload du fichier";
    }
    header('Location: ressources_formateur.php');
    exit();
}

// Récupérer les ressources du formateur
$ressources = $pdo->prepare("SELECT r.*, m.nom as module_nom 
                            FROM ressources r
                            JOIN module m ON r.id_module = m.id
                            WHERE r.id_formateur = ?
                            ORDER BY r.date_ajout DESC");
$ressources->execute([$formateur_id]);
$ressources_list = $ressources->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressources Pédagogiques</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .module-badge {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .file-icon {
            font-size: 3rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Ressources Pédagogiques</h1>
            <div>
                <span class="me-3"><?= htmlspecialchars($formateur['prenom'] . ' ' . htmlspecialchars($formateur['nom']) )?></span>
                <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
            </div>
        </div>

        <!-- Messages d'alerte -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Ajouter une nouvelle ressource</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="titre" class="form-label">Titre*</label>
                            <input type="text" class="form-control" id="titre" name="titre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="module" class="form-label">Module*</label>
                            <select class="form-select" id="module" name="module" required>
                                <option value="">Choisir un module</option>
                                <?php foreach ($modules_list as $module): ?>
                                    <option value="<?= $module['id'] ?>"><?= htmlspecialchars($module['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="groupe" class="form-label">Groupe*</label>
                            <select class="form-select" id="groupe" name="groupe" required>
                                <option value="">Choisir un groupe</option>
                                <?php foreach ($groupes_list as $groupe): ?>
                                    <option value="<?= $groupe['id'] ?>"><?= htmlspecialchars($groupe['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fichier" class="form-label">Fichier*</label>
                            <input type="file" class="form-control" id="fichier" name="fichier" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="ajouter_ressource" class="btn btn-primary">Ajouter la ressource</button>
                </form>
            </div>
        </div>

        <!-- Liste des ressources -->
        <h3 class="mb-3">Mes ressources</h3>
        
        <?php if (empty($ressources_list)): ?>
            <div class="alert alert-info">Vous n'avez pas encore de ressources partagées.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($ressources_list as $ressource): 
                    $extension = pathinfo($ressource['fichier_url'], PATHINFO_EXTENSION);
                    $icon = getFileIcon($extension);
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <i class="<?= $icon ?> file-icon"></i>
                                </div>
                                <h5 class="card-title"><?= htmlspecialchars($ressource['titre']) ?></h5>
                                <span class="badge module-badge mb-2"><?= htmlspecialchars($ressource['module_nom']) ?></span>
                                <p class="card-text text-muted"><?= htmlspecialchars($ressource['description']) ?></p>
                                <p class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i> 
                                    <?= date('d/m/Y', strtotime($ressource['date_ajout'])) ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="<?= $ressource['fichier_url'] ?>" class="btn btn-sm btn-outline-primary" download>
                                    <i class="fas fa-download me-1"></i> Télécharger
                                </a>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#shareModal<?= $ressource['id'] ?>">
                                    <i class="fas fa-share-alt me-1"></i> Partager
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de partage -->
                    <div class="modal fade" id="shareModal<?= $ressource['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Partager "<?= htmlspecialchars($ressource['titre']) ?>"</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="partager_ressource.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="ressource_id" value="<?= $ressource['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Groupes</label>
                                            <?php foreach ($groupes_list as $groupe): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="groupes[]" 
                                                           value="<?= $groupe['id'] ?>" id="groupe<?= $groupe['id'] ?>_<?= $ressource['id'] ?>">
                                                    <label class="form-check-label" for="groupe<?= $groupe['id'] ?>_<?= $ressource['id'] ?>">
                                                        <?= htmlspecialchars($groupe['nom']) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="message<?= $ressource['id'] ?>" class="form-label">Message (optionnel)</label>
                                            <textarea class="form-control" id="message<?= $ressource['id'] ?>" name="message" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Partager</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Fonction pour obtenir l'icône appropriée selon l'extension du fichier
function getFileIcon($extension) {
    $extension = strtolower($extension);
    switch ($extension) {
        case 'pdf':
            return 'fas fa-file-pdf';
        case 'doc':
        case 'docx':
            return 'fas fa-file-word';
        case 'xls':
        case 'xlsx':
            return 'fas fa-file-excel';
        case 'ppt':
        case 'pptx':
            return 'fas fa-file-powerpoint';
        case 'zip':
        case 'rar':
            return 'fas fa-file-archive';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'fas fa-file-image';
        default:
            return 'fas fa-file';
    }
}
?>