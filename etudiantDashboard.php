<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord étudiant - Gestion de présence QR</title>
    <style>
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
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: white;
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .logo {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .user-name {
            font-weight: 500;
        }

        .user-role {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(101, 113, 102, 0.2);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-bg);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-bg);
            border-radius: 50%;
            color: var(--primary-color);
            font-size: 20px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .progress-container {
            margin-top: 10px;
            height: 8px;
            background-color: var(--light-bg);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: var(--success-color);
            border-radius: 4px;
        }

        .progress-danger {
            background-color: var(--danger-color);
        }

        .progress-warning {
            background-color: var(--warning-color);
        }

        .qr-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .qr-title {
            margin-bottom: 15px;
            color: var(--secondary-color);
        }

        .qr-container {
            background-color: white;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: inline-block;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            background-color: var(--light-bg);
            border: 2px solid var(--border-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .qr-code img {
            width: 90%;
            height: 90%;
        }

        .qr-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius);
            margin-top: 15px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .qr-button:hover {
            background-color: var(--secondary-color);
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-bg);
        }

        .schedule-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .schedule-table tr:hover {
            background-color: rgba(253, 232, 211, 0.3);
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-present {
            background-color: var(--success-color);
            color: var(--secondary-color);
        }

        .status-absent {
            background-color: var(--danger-color);
            color: white;
        }

        .status-late {
            background-color: var(--warning-color);
            color: var(--secondary-color);
        }

        .upcoming-section {
            margin-top: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .view-all {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .view-all:hover {
            color: var(--secondary-color);
        }

        .calendar-card {
            margin-top: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .month-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--primary-color);
            font-size: 18px;
        }

        .current-month {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .day {
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .day:hover {
            background-color: var(--light-bg);
        }

        .day.today {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .day.has-class {
            border: 2px solid var(--accent-color);
        }

        .day.absent {
            background-color: rgba(243, 195, 178, 0.3);
        }

        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px 0;
            border-top: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .qr-code {
                width: 150px;
                height: 150px;
            }

            .schedule-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">QR Présence</div>
            <div class="user-info">
                <div>
                    <div class="user-name">OUAYAT Amal</div>
                    <div class="user-role">Étudiant - Devowfs201</div>
                </div>
                <img src="/api/placeholder/40/40" alt="Photo de profil">
            </div>
        </header>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Taux de présence</div>
                    <div class="card-icon">📊</div>
                </div>
                <div class="stat-value">85%</div>
                <div class="stat-label">Ce semestre</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 85%;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Absences</div>
                    <div class="card-icon">❌</div>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Ce mois-ci</div>
                <div class="progress-container">
                    <div class="progress-bar progress-danger" style="width: 15%;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Retards</div>
                    <div class="card-icon">⏰</div>
                </div>
                <div class="stat-value">5</div>
                <div class="stat-label">Ce mois-ci</div>
                <div class="progress-container">
                    <div class="progress-bar progress-warning" style="width: 25%;"></div>
                </div>
            </div>
        </div>

        <div class="qr-section">
            <h2 class="qr-title">Scannez pour marquer votre présence</h2>
            <div class="qr-container">
                <div class="qr-code">
                    <!-- Placeholder for QR code -->
                    <img src="/api/placeholder/180/180" alt="QR Code">
                </div>
                <button class="qr-button">Générer mon QR code</button>
            </div>
        </div>

        <div class="upcoming-section">
            <div class="section-header">
                <h2 class="section-title">Séances d'aujourd'hui</h2>
                <a href="#" class="view-all">Voir tout</a>
            </div>
            <div class="card">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Horaire</th>
                            <th>Salle</th>
                            <th>Formateur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Front-end</td>
                            <td>8h30 - 11h00</td>
                            <td>Salle info2</td>
                            <td>ELGHAZI Mohamed</td>
                            <td><span class="status status-present">Présent</span></td>
                        </tr>
                        <tr>
                            <td>Python</td>
                            <td>11h00 - 13h30</td>
                            <td>Salle info1</td>
                            <td>ELHYYANI Isam</td>
                            <td><span class="status status-late">En retard</span></td>
                        </tr>
                        <tr>
                            <td>Soft-skills</td>
                            <td>13h30 - 16h00</td>
                            <td>Salle 3</td>
                            <td>MESRAR Youssef</td>
                            <td><span class="status">À venir</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="calendar-card card">
            <div class="calendar-header">
                <h3>Mai 2025</h3>
                <div class="month-nav">
                    <button class="nav-btn">◀</button>
                    <span class="current-month">Mai 2025</span>
                    <button class="nav-btn">▶</button>
                </div>
            </div>
            <div class="weekdays">
                <div>Lun</div>
                <div>Mar</div>
                <div>Mer</div>
                <div>Jeu</div>
                <div>Ven</div>
                <div>Sam</div>
                <div>Dim</div>
            </div>
            <div class="days">
                <!-- Première rangée -->
                <div class="day"></div>
                <div class="day"></div>
                <div class="day">1</div>
                <div class="day">2</div>
                <div class="day">3</div>
                <div class="day">4</div>
                <div class="day">5</div>
                <!-- Deuxième rangée -->
                <div class="day">6</div>
                <div class="day">7</div>
                <div class="day">8</div>
                <div class="day">9</div>
                <div class="day">10</div>
                <div class="day">11</div>
                <div class="day">12</div>
                <!-- Troisième rangée -->
                <div class="day">13</div>
                <div class="day">14</div>
                <div class="day">15</div>
                <div class="day">16</div>
                <div class="day">17</div>
                <div class="day">18</div>
                <div class="day today">19</div>
                <!-- Quatrième rangée -->
                <div class="day has-class">20</div>
                <div class="day">21</div>
                <div class="day">22</div>
                <div class="day has-class">23</div>
                <div class="day">24</div>
                <div class="day">25</div>
                <div class="day">26</div>
                <!-- Cinquième rangée -->
                <div class="day">27</div>
                <div class="day has-class">28</div>
                <div class="day">29</div>
                <div class="day">30</div>
                <div class="day">31</div>
                <div class="day"></div>
                <div class="day"></div>
            </div>
        </div>

        <div class="upcoming-section">
            <div class="section-header">
                <h2 class="section-title">Historique des présences</h2>
                <a href="#" class="view-all">Voir tout</a>
            </div>
            <div class="card">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Module</th>
                            <th>Formateur</th>
                            <th>Horaire</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>18/05/2025</td>
                            <td>Python</td>
                            <td>ELHYYANI Isam</td>
                            <td>8h30 - 11h00</td>
                            <td><span class="status status-late">En retard</span></td>
                        </tr>
                        <tr>
                            <td>17/05/2025</td>
                            <td>Front-end</td>
                            <td>ELGHAZI Mohamed</td>
                            <td>11h00 - 13h30</td>
                            <td><span class="status status-late">En retard</span></td>
                        </tr>
                        <tr>
                            <td>17/05/2025</td>
                            <td>Python</td>
                            <td>ELHYYANI Isam</td>
                            <td>8h30 - 11h00</td>
                            <td><span class="status status-late">En retard</span></td>
                        </tr>
                        <tr>
                            <td>15/05/2025</td>
                            <td>Soft-skills</td>
                            <td>MESRAR Youssef</td>
                            <td>13h30 - 16h00</td>
                            <td><span class="status status-present">Présent</span></td>
                        </tr>
                        <tr>
                            <td>14/05/2025</td>
                            <td>Front-end</td>
                            <td>ELGHAZI Mohamed</td>
                            <td>11h00 - 13h30</td>
                            <td><span class="status status-present">Présent</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>
            <p>© 2025 QR Présence - Gestion des présences par QR code</p>
        </footer>
    </div>
</body>

</html>