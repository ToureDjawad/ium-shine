<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Votes - IUM-SHINE</title>
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
        
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .candidate-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        
        .candidate-card:hover {
            transform: translateY(-2px);
        }
        
        .candidate-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
        }
        
        .progress {
            height: 20px;
            border-radius: 10px;
            margin: 10px 0;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        .vote-buttons .btn {
            margin-right: 5px;
        }
        
        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .category-stats {
            display: flex;
            gap: 15px;
        }
        
        .stat-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
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
                        <a class="nav-link" href="admin.php">Tableau de Bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?route=candidates">Candidats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin.php?route=votes">Votes</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="admin.php?route=settings">Paramètres</a>
                    </li>
                </ul>
                <div class="admin-info">
                    <i class="fas fa-user-shield me-2"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                     <a href="admin.php?route=logout" class="text-white text-decoration-none ms-3">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Votes</h1>
            <a href="admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
            </a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
        <?php endif; ?>
        
        <?php foreach ($categories as $category): ?>
        <div class="category-card">
            <div class="category-header">
                <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                <div class="category-stats">
                    <span class="stat-badge">
                        <i class="fas fa-users me-1"></i>
                        <?php echo $category['total_candidates']; ?> candidats
                    </span>
                    <span class="stat-badge">
                        <i class="fas fa-vote-yea me-1"></i>
                        <?php echo $category['total_votes']; ?> votes
                    </span>
                    <a href="admin.php?route=votes&action=reset&category_id=<?php echo $category['id']; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser tous les votes de cette catégorie ?')">
                        <i class="fas fa-redo me-1"></i>Réinitialiser
                    </a>
                </div>
            </div>
            
            <?php foreach ($category['candidates'] as $candidate): ?>
            <div class="candidate-card">
                <div class="d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" 
                         alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                         class="candidate-photo">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="mb-1"><?php echo htmlspecialchars($candidate['name']); ?></h3>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($candidate['description']); ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">
                                        <i class="fas fa-trophy me-1"></i>
                                        Rang #<?php echo $candidate['candidate_rank']; ?>
                                    </span>
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-percentage me-1"></i>
                                        <?php echo $candidate['percentage']; ?>%
                                    </span>
                                    <span class="badge bg-info">
                                        <i class="fas fa-vote-yea me-1"></i>
                                        <?php echo $candidate['vote_count']; ?> votes
                                    </span>
                                </div>
                            </div>
                            <div class="vote-buttons">
                                <a href="admin.php?route=votes&action=add&id=<?php echo $candidate['id']; ?>" 
                                   class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Ajouter un vote
                                </a>
                                <a href="admin.php?route=votes&action=remove&id=<?php echo $candidate['id']; ?>" 
                                   class="btn btn-warning">
                                    <i class="fas fa-minus me-1"></i>Retirer un vote
                                </a>
                            </div>
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo $candidate['percentage']; ?>%"
                                 aria-valuenow="<?php echo $candidate['percentage']; ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <?php echo $candidate['percentage']; ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 