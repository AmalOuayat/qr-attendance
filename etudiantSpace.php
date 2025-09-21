<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/adminSpace.css">
</head>

<body>
    <div class="wrapper">
        <div id="sidebar-overlay" class="sidebar-overlay"></div>
        <nav id="sidebar" class="sidebar hidden" aria-label="Sidebar">
            <h2>Espace Étudiant</h2>
            <button id="close-sidebar" class="close-sidebar" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
            <ul>
                <li><a target="iframe" href="etudiantDashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i>
                        Tableau de bord</a></li>
                <li class="title">Pages</li>
                <!-- <li><a target="iframe" href="coursSuivis.php" class="nav-link"><i class="bi bi-journal-text"></i> Cours
                        suivis</a></li>
                <li><a target="iframe" href="scannerQRCode.php" class="nav-link"><i class="bi bi-upc-scan"></i> Scanner
                        QR Code</a></li> -->
                <li><a target="iframe" href="scanner_presence.php" class="nav-link"><i
                            class="bi bi-file-earmark-text"></i> Scanner
                        QR Code</a></li>
                <li><a target="iframe" href="Presences_etudiants.php" class="nav-link"><i
                            class="bi bi-calendar-check"></i> Historique des présences</a></li>
                <!-- <li><a target="iframe" href="ressourcesCours.php" class="nav-link"><i
                            class="bi bi-file-earmark-text"></i> Ressources de cours</a></li> -->
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

                    </div>
                </div>
            </header>
            <main>
                <iframe src="etudiantDashboard.php" id="iframe" name="iframe" frameborder="0"
                    title="Main Content"></iframe>
            </main>
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
                sidebar.classList.add('visible'); // Ajout de la classe 'visible' pour les mobiles
                sidebarOverlay.classList.add('active');
            }

            // Function to hide sidebar
            function hideSidebar() {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('visible'); // Suppression de la classe 'visible'
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
                    sidebar.classList.remove('visible'); // Retirer la classe 'visible' sur desktop
                    sidebarOverlay.classList.remove('active');
                } else {
                    // On mobile, always hide sidebar by default
                    if (!sidebar.classList.contains('hidden')) {
                        sidebar.classList.add('hidden');
                        sidebar.classList.remove('visible'); // Retirer aussi 'visible'
                        mainContent.classList.add('full-width');
                    }
                }
            });

            // Initial setup
            if (window.innerWidth <= 768) {
                // Mobile: hide sidebar by default
                sidebar.classList.add('hidden');
                sidebar.classList.remove('visible'); // S'assurer que 'visible' est retiré initialement
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
    </script>
</body>

</html>