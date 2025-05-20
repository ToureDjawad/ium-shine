<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - IUM-SHINE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--secondary-color);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        .action-card {
            text-align: center;
            padding: 30px;
        }
        
        .action-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }
        
        .action-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .action-description {
            color: #666;
            margin-bottom: 20px;
        }
        
        .btn-action {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="fas fa-star me-2"></i>IUM-SHINE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin.php">Tableau de Bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?route=candidates">Candidats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?route=votes">Votes</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="admin.php?route=settings">Paramètres</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="text-white me-3">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </span>
                    <a href="admin.php?route=logout" class="text-white text-decoration-none">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Tableau de Bord</h1>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-number"><?php echo $totalCandidates; ?></div>
                    <div class="stat-label">Candidats</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-list stat-icon"></i>
                    <div class="stat-number"><?php echo $totalCategories; ?></div>
                    <div class="stat-label">Catégories</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-vote-yea stat-icon"></i>
                    <div class="stat-number"><?php echo $totalVotes; ?></div>
                    <div class="stat-label">Votes Totaux</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-chart-line stat-icon"></i>
                    <div class="stat-number"><?php echo $totalVoters; ?></div>
                    <div class="stat-label">Votants</div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="row">
            <div class="col-md-4">
                <div class="dashboard-card action-card">
                    <i class="fas fa-user-plus action-icon"></i>
                    <h3 class="action-title">Gestion des Candidats</h3>
                    <p class="action-description">
                        Ajoutez, modifiez ou supprimez des candidats et gérez leurs informations.
                    </p>
                    <a href="admin.php?route=candidates" class="btn btn-primary btn-action">
                        <i class="fas fa-arrow-right me-2"></i>Gérer les candidats
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card action-card">
                    <i class="fas fa-vote-yea action-icon"></i>
                    <h3 class="action-title">Gestion des Votes</h3>
                    <p class="action-description">
                        Gérez les votes, consultez les statistiques et les résultats en temps réel.
                    </p>
                    <a href="admin.php?route=votes" class="btn btn-success btn-action">
                        <i class="fas fa-arrow-right me-2"></i>Gérer les votes
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card action-card">
                    <i class="fas fa-cog action-icon"></i>
                    <h3 class="action-title">Paramètres</h3>
                    <p class="action-description">
                        Configurez les paramètres du site et gérez les catégories de vote.
                    </p>
                    <a href="admin.php?route=settings" class="btn btn-info btn-action">
                        <i class="fas fa-arrow-right me-2"></i>Paramètres
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 