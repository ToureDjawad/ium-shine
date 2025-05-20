<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($candidate) ? 'Modifier' : 'Ajouter'; ?> un Candidat - IUM-SHINE</title>
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
        
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #eee;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: none;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: bold;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        
        .preview-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            margin: 20px auto;
            display: block;
            border: 5px solid white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
                        <a class="nav-link active" aria-current="page" href="admin.php?route=candidates">Candidats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?route=votes">Votes</a>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-card">
                    <h1 class="text-center mb-4">
                        <?php echo isset($candidate) ? 'Modifier' : 'Ajouter'; ?> un Candidat
                    </h1>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?php if (isset($candidate)): ?>
                        <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($candidate['photo']); ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du candidat</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($candidate) ? htmlspecialchars($candidate['name']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php 
                                echo isset($candidate) ? htmlspecialchars($candidate['description']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="motif" class="form-label">Motif de candidature</label>
                            <textarea class="form-control" id="motif" name="motif" rows="4" required><?php 
                                echo isset($candidate) ? htmlspecialchars($candidate['motif']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Catégorie</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (isset($candidate) && $candidate['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            <?php if (isset($candidate) && $candidate['photo']): ?>
                            <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" 
                                 alt="Photo actuelle" class="preview-photo">
                            <?php endif; ?>
                            <input type="file" class="form-control" id="photo" name="photo" 
                                   accept="image/*" <?php echo !isset($candidate) ? 'required' : ''; ?>>
                            <small class="text-muted">
                                <?php echo isset($candidate) ? 'Laissez vide pour conserver la photo actuelle' : 'Format recommandé : JPG, PNG (max 2MB)'; ?>
                            </small>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save me-2"></i>
                                <?php echo isset($candidate) ? 'Mettre à jour' : 'Ajouter'; ?>
                            </button>
                            <a href="index.php?route=admin-candidates" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prévisualisation de la photo
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.preview-photo');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-photo';
                        document.querySelector('.mb-3').appendChild(img);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 