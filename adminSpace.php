<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch admin profile information
$admin_name = $_SESSION['admin_name'] ?? 'Administrateur';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';
$admin_role = $_SESSION['admin_role'] ?? 'Administrateur Principal';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/adminSpace.css">
</head>

<body>
    <div class="wrapper">
        <div id="sidebar-overlay" class="sidebar-overlay"></div>
        <nav id="sidebar" class="sidebar hidden" aria-label="Sidebar">
            <h2>Admin Dashboard</h2>
            <button id="close-sidebar" class="close-sidebar" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
            <ul>
                <li><a target="iframe" href="adminDashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i>
                        Tableau de bord</a></li>
                <li class="title">Pages</li>
                <li><a target="iframe" href="Gestion_enseignants.php" class="nav-link"><i
                            class="bi bi-person-badge"></i> Gestion des enseignants</a></li>
                <li><a target="iframe" href="Gestion_etudiants.php" class="nav-link"><i class="bi bi-people"></i>
                        Gestion des étudiants</a></li>
                <li><a target="iframe" href="Gestion_groupe.php" class="nav-link"><i class="bi bi-diagram-3"></i>
                        Gestion des groupes</a></li>
                <li><a target="iframe" href="Historique_presences.php" class="nav-link"><i
                            class="bi bi-calendar-check"></i> Historique des présences</a></li>
                <li><a target="iframe" href="Generation_qr_codes.php" class="nav-link"><i class="bi bi-upc-scan"></i>
                        Génération des QR Codes</a></li>
                <li><a target="iframe" href="cours_module.php" class="nav-link"><i class="bi bi-journal-text"></i>
                        Gestion des modules</a></li>
                <li><a target="iframe" href="gestion_emploi.php" class="nav-link"><i class="bi bi-calendar-week"></i>
                        Gestion des emploi</a></li>
                <li><a target="iframe" href="generation_qrcode.php" class="nav-link"><i class="bi bi-calendar-week"></i>
                        Qr code</a></li>
                <li class="title">Autre</li>
                <li><a href="logout.php" class="logout nav-link"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
                </li>
            </ul>
        </nav>
        <div class="main-content full-width">
            <header>
                <button class="hamburger-menu" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <button class="toggle-btn" aria-label="Toggle sidebar">☰</button>
                <div class="search-container">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Rechercher..." class="search-bar" aria-label="Search">
                </div>
                <div class="icons">
                    <span class="messages"><i class="bi bi-envelope"></i></span>
                    <div class="profile">
                        <i class="bi bi-person"></i>
                        <div class="profile-dropdown">
                            <div class="profile-header">
                                <img src="/api/placeholder/60/60" alt="Profile Picture" class="profile-img">
                                <div>
                                    <strong><?php echo htmlspecialchars($admin_name); ?></strong>
                                    <p><?php echo htmlspecialchars($admin_role); ?></p>
                                </div>
                            </div>
                            <ul>
                                <li onclick="openProfileModal()">
                                    <i class="bi bi-person-circle"></i> Voir Profil
                                </li>
                                <li onclick="openSettingsModal()">
                                    <i class="bi bi-gear"></i> Paramètres
                                </li>
                                <li onclick="openSecurityModal()">
                                    <i class="bi bi-shield-lock"></i> Sécurité
                                </li>
                                <li>
                                    <a href="logout.php" class="logout">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <main>
                <iframe src="adminDashboard.php" id="iframe" name="iframe" frameborder="0"
                    title="Main Content"></iframe>
            </main>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('profileModal')">&times;</span>
            <h2>Profil Administrateur</h2>
            <div class="profile-details">
                <img src="/api/placeholder/120/120" alt="Profile Picture" class="profile-img">
                <div>
                    <p><strong>Nom:</strong> <?php echo htmlspecialchars($admin_name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($admin_email); ?></p>
                    <p><strong>Rôle:</strong> <?php echo htmlspecialchars($admin_role); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('settingsModal')">&times;</span>
            <h2>Paramètres</h2>
            <div class="settings-form">
                <form>
                    <div class="form-group">
                        <label for="language">Langue:</label>
                        <select id="language" class="form-control">
                            <option value="fr">Français</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notifications">Notifications:</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="notifications" checked>
                            <span class="slider"></span>
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Sauvegarder</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Security Modal -->
    <div id="securityModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('securityModal')">&times;</span>
            <h2>Sécurité</h2>
            <div class="security-form">
                <form>
                    <div class="form-group">
                        <label for="current-password">Mot de passe actuel:</label>
                        <input type="password" id="current-password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="new-password">Nouveau mot de passe:</label>
                        <input type="password" id="new-password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirmer mot de passe:</label>
                        <input type="password" id="confirm-password" class="form-control">
                    </div>
                    <button type="submit" class="btn-save">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburgerMenu = document.querySelector('.hamburger-menu');
            const toggleBtn = document.querySelector('.toggle-btn');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const navLinks = document.querySelectorAll('.nav-link');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const closeSidebarBtn = document.getElementById('close-sidebar');

            // Function to show sidebar
            function showSidebar() {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('visible');
                sidebarOverlay.classList.add('active');
            }

            // Function to hide sidebar
            function hideSidebar() {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('visible');
                sidebarOverlay.classList.remove('active');
            }

            // Hamburger menu (mobile)
            hamburgerMenu.addEventListener('click', function () {
                if (sidebar.classList.contains('hidden')) {
                    showSidebar();
                } else {
                    hideSidebar();
                }
            });

            // Toggle button (desktop)
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('hidden');
                mainContent.classList.toggle('full-width');

                // Store user preference
                const isHidden = sidebar.classList.contains('hidden');
                localStorage.setItem('sidebarHidden', isHidden);
            });

            // Close sidebar when clicking overlay
            sidebarOverlay.addEventListener('click', hideSidebar);

            // Close sidebar when clicking close button
            closeSidebarBtn.addEventListener('click', hideSidebar);

            // Hide sidebar after clicking a link on mobile
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        hideSidebar();
                    }
                });
            });

            // Add active class to current page
            function setActivePage() {
                const iframe = document.getElementById('iframe');
                const currentPage = iframe.contentWindow.location.href.split('/').pop();

                navLinks.forEach(link => {
                    const linkHref = link.getAttribute('href');

                    if (linkHref === currentPage) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            // Set active page when iframe loads
            document.getElementById('iframe').addEventListener('load', setActivePage);

            // Window resize handler
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    // On desktop, restore saved preference
                    const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
                    if (!sidebarHidden) {
                        sidebar.classList.remove('hidden');
                        mainContent.classList.remove('full-width');
                    }
                    sidebar.classList.remove('visible');
                    sidebarOverlay.classList.remove('active');
                } else {
                    // On mobile, always hide sidebar by default
                    if (!sidebar.classList.contains('hidden')) {
                        sidebar.classList.add('hidden');
                        sidebar.classList.remove('visible');
                        mainContent.classList.add('full-width');
                    }
                }
            });

            // Initial setup
            if (window.innerWidth <= 768) {
                // Mobile: hide sidebar by default
                sidebar.classList.add('hidden');
                sidebar.classList.remove('visible');
                mainContent.classList.add('full-width');
            } else {
                // Desktop: use saved preference
                const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
                if (sidebarHidden) {
                    sidebar.classList.add('hidden');
                    mainContent.classList.add('full-width');
                } else {
                    sidebar.classList.remove('hidden');
                    mainContent.classList.remove('full-width');
                }
            }
        });

        // Modal Functions
        function openProfileModal() {
            document.getElementById('profileModal').style.display = 'block';
        }

        function openSettingsModal() {
            document.getElementById('settingsModal').style.display = 'block';
        }

        function openSecurityModal() {
            document.getElementById('securityModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }
    </script>
</body>

</html>