<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Candidats - IUM-SHINE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: var(--primary-color);
            padding: 1rem;
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        
        .admin-info {
            color: white;
            margin-left: auto;
        }
        
        .candidate-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .candidate-photo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        
        .progress {
            height: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
        }
        
        .category-header {
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="fas fa-star me-2"></i>IUM-SHINE
            </a>
            <div class="admin-info">
                <i class="fas fa-user-shield me-2"></i><?php echo $_SESSION['admin_username']; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users me-2"></i>Gestion des Candidats</h2>
            <a href="admin.php?route=candidates&action=add" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajouter un candidat
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
        
        <?php
        $current_category = null;
        $category_total_votes = 0;
        $category_candidates = [];
        
        // Grouper les candidats par catégorie
        foreach ($candidates as $candidate) {
            if ($current_category !== $candidate['category_id']) {
                // Afficher la catégorie précédente si elle existe
                if ($current_category !== null) {
                    displayCategory($current_category, $category_candidates, $category_total_votes);
                }
                
                // Réinitialiser pour la nouvelle catégorie
                $current_category = $candidate['category_id'];
                $category_total_votes = 0;
                $category_candidates = [];
            }
            
            $category_candidates[] = $candidate;
            $category_total_votes += $candidate['vote_count'];
        }
        
        // Afficher la dernière catégorie
        if ($current_category !== null) {
            displayCategory($current_category, $category_candidates, $category_total_votes);
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function displayCategory($category_id, $candidates, $total_votes) {
    if (empty($candidates)) return;
    
    $category_name = $candidates[0]['category_name'];
    ?>
    <div class="category-header">
        <h3><?php echo htmlspecialchars($category_name); ?></h3>
        <small>Total des votes : <?php echo $total_votes; ?></small>
    </div>
    
    <div class="row">
        <?php foreach ($candidates as $candidate): 
            $percentage = $total_votes > 0 ? round(($candidate['vote_count'] / $total_votes) * 100, 1) : 0;
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="candidate-card">
                <div class="text-center">
                    <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" 
                         alt="<?php echo htmlspecialchars($candidate['name']); ?>" 
                         class="candidate-photo">
                    <h4><?php echo htmlspecialchars($candidate['name']); ?></h4>
                    <p class="text-muted">Rang #<?php echo $candidate['candidate_rank']; ?></p>
                </div>
                
                <div class="mt-3">
                    <p><strong>Votes :</strong> <?php echo $candidate['vote_count']; ?></p>
                    <p><strong>Pourcentage :</strong> <?php echo $percentage; ?>%</p>
                    <div class="progress">
                        <div class="progress-bar bg-primary" 
                             role="progressbar" 
                             style="width: <?php echo $percentage; ?>%">
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="admin.php?route=candidates&action=edit&id=<?php echo $candidate['id']; ?>" 
                       class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                            class="btn btn-sm btn-success btn-action"
                            data-bs-toggle="modal" 
                            data-bs-target="#addVoteModal<?php echo $candidate['id']; ?>">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-warning btn-action"
                            data-bs-toggle="modal" 
                            data-bs-target="#removeVoteModal<?php echo $candidate['id']; ?>">
                        <i class="fas fa-minus"></i>
                    </button>
                    <a href="admin.php?route=candidates&action=delete&id=<?php echo $candidate['id']; ?>" 
                       class="btn btn-sm btn-danger btn-action"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce candidat ?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal pour ajouter des votes -->
    <div class="modal fade" id="addVoteModal<?php echo $candidate['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter des votes pour <?php echo htmlspecialchars($candidate['name']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin.php?route=candidates&action=vote&id=<?php echo $candidate['id']; ?>&type=add" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="voteCount<?php echo $candidate['id']; ?>" class="form-label">Nombre de votes à ajouter</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="voteCount<?php echo $candidate['id']; ?>" 
                                   name="vote_count" 
                                   min="1" 
                                   value="1" 
                                   required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Ajouter les votes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour retirer des votes -->
    <div class="modal fade" id="removeVoteModal<?php echo $candidate['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Retirer des votes pour <?php echo htmlspecialchars($candidate['name']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin.php?route=candidates&action=vote&id=<?php echo $candidate['id']; ?>&type=remove" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="removeVoteCount<?php echo $candidate['id']; ?>" class="form-label">Nombre de votes à retirer</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="removeVoteCount<?php echo $candidate['id']; ?>" 
                                   name="vote_count" 
                                   min="1" 
                                   max="<?php echo $candidate['vote_count']; ?>" 
                                   value="1" 
                                   required>
                            <small class="text-muted">Votes actuels : <?php echo $candidate['vote_count']; ?></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">Retirer les votes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?> 