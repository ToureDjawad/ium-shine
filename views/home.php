<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IUM-SHINE - Votez pour vos favoris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgb(80, 44, 44);
            --secondary-color: rgb(219, 52, 52);
            --accent-color: rgb(231, 60, 60);
            --gold-color: #FFD700;
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
            color: var(--gold-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand i {
            color: var(--gold-color);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .category-card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
        }
        
        .category-card .card-body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .admin-link {
            color: white;
            text-decoration: none;
        }
        
        .admin-link:hover {
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-star me-2"></i>IUM-SHINE
            </a>
            <!-- <a href="admin.php" class="admin-link">
                <i class="fas fa-user-shield me-2"></i>Admin
            </a> -->
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-5">Catégories de Vote</h1>
        
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-4">
                <div class="category-card card h-100">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($category['description']); ?></p>
                        <a href="index.php?route=category&id=<?php echo $category['id']; ?>" 
                           class="btn btn-light">
                            Voir les candidats
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> IUM-SHINE. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 