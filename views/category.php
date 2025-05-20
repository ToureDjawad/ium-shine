<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - IUM-SHINE</title>
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
        
        .candidate-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .candidate-card:hover {
            transform: translateY(-5px);
        }
        
        .candidate-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        
        .progress {
            height: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .category-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-star me-2"></i>IUM-SHINE
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="category-header">
            <h1><?php echo htmlspecialchars($category['name']); ?></h1>
            <p class="mb-0"><?php echo htmlspecialchars($category['description']); ?></p>
        </div>
        
        <div class="row">
            <?php foreach ($candidates as $candidate): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="candidate-card">
                    <div class="text-center">
                        <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($candidate['name']); ?>" 
                             class="candidate-photo">
                        <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                        <?php if (!empty($candidate['motif'])): ?>
                             <p class="text-muted mb-2"><em><?php echo htmlspecialchars($candidate['motif']); ?></em></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-3">
                        <p><strong>Description :</strong> <?php echo htmlspecialchars($candidate['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 