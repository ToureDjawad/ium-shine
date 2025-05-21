<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - IUM-SHINE</title>
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
        
        .settings-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            padding: 15px 25px;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom: 3px solid var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border: none;
            color: var(--secondary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .btn-save {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .category-list {
            list-style: none;
            padding: 0;
        }
        
        .category-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .category-name {
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .category-actions .btn {
            margin-left: 10px;
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
                        <a class="nav-link" href="admin.php?route=votes">Votes</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin.php?route=settings">Paramètres</a>
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

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Paramètres</h1>
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
        
        <div class="settings-card">
            <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" 
                            data-bs-target="#categories" type="button" role="tab">
                        <i class="fas fa-list me-2"></i>Catégories
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="general-tab" data-bs-toggle="tab" 
                            data-bs-target="#general" type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>Général
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" 
                            data-bs-target="#security" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="settingsTabsContent">
                <!-- Catégories -->
                <div class="tab-pane fade show active" id="categories" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Gestion des Catégories</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-2"></i>Nouvelle Catégorie
                        </button>
                    </div>
                    
                    <ul class="category-list">
                        <?php foreach ($categories as $category): ?>
                        <li class="category-item">
                            <span class="category-name"><?php echo htmlspecialchars($category['name']); ?></span>
                            <div class="category-actions">
                                <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal" 
                                        data-id="<?php echo $category['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                        data-description="<?php echo htmlspecialchars($category['description']); ?>">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" data-bs-target="#deleteCategoryModal" 
                                        data-id="<?php echo $category['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($category['name']); ?>">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Général -->
                <div class="tab-pane fade" id="general" role="tabpanel">
                    <h3 class="mb-4">Paramètres Généraux</h3>
                    <form action="admin.php?route=settings&action=update_general" method="POST">
                        <div class="mb-3">
                            <label for="siteName" class="form-label">Nom du Site</label>
                            <input type="text" class="form-control" id="siteName" name="site_name" 
                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="siteDescription" class="form-label">Description du Site</label>
                            <textarea class="form-control" id="siteDescription" name="site_description" rows="3"><?php 
                                echo htmlspecialchars($settings['site_description'] ?? ''); 
                            ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">Email Administrateur</label>
                            <input type="email" class="form-control" id="adminEmail" name="admin_email" 
                                   value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </form>
                </div>
                
                <!-- Sécurité -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <h3 class="mb-4">Paramètres de Sécurité</h3>
                    <p>Modifier votre nom d'utilisateur et votre mot de passe administrateur.</p>
                    
                    <form action="admin.php?route=update_admin_credentials" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de Passe Actuel</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_username" class="form-label">Nouveau Nom d'Utilisateur</label>
                            <input type="text" class="form-control" id="new_username" name="new_username" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau Mot de Passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <small class="form-text text-muted">Laissez vide si vous ne souhaitez pas changer de mot de passe.</small>
                        </div>
                         <div class="mb-3">
                            <label for="confirm_new_password" class="form-label">Confirmer Nouveau Mot de Passe</label>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                        </div>
                        <button type="submit" class="btn btn-save">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modals pour Catégories (Ajout, Modification, Suppression) -->
    
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une Nouvelle Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin.php?route=add_category" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Nom de la Catégorie</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Modifier la Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin.php?route=edit_category" method="POST">
                    <input type="hidden" id="editCategoryId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Nom de la Catégorie</label>
                            <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">Supprimer la Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer la catégorie "<strong id="deleteCategoryName"></strong>" ? Cette action est irréversible et supprimera également tous les candidats associés.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteCategoryForm" action="admin.php?route=delete_category" method="POST">
                        <input type="hidden" id="deleteCategoryId" name="id">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript pour remplir les modales de modification et suppression de catégorie
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de la modale d'édition
            var editCategoryModal = document.getElementById('editCategoryModal');
            if (editCategoryModal) {
                editCategoryModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var description = button.getAttribute('data-description');

                    var modalTitle = editCategoryModal.querySelector('.modal-title');
                    var modalIdInput = editCategoryModal.querySelector('#editCategoryId');
                    var modalNameInput = editCategoryModal.querySelector('#editCategoryName');
                    var modalDescriptionTextarea = editCategoryModal.querySelector('#editCategoryDescription');

                    modalTitle.textContent = 'Modifier la catégorie : ' + name;
                    modalIdInput.value = id;
                    modalNameInput.value = name;
                    modalDescriptionTextarea.value = description;
                });
            }

            // Gestion de la modale de suppression
            var deleteCategoryModal = document.getElementById('deleteCategoryModal');
            if (deleteCategoryModal) {
                deleteCategoryModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');

                    var modalBody = deleteCategoryModal.querySelector('.modal-body strong');
                    var modalIdInput = deleteCategoryModal.querySelector('#deleteCategoryId');

                    modalBody.textContent = name;
                    modalIdInput.value = id;
                });
            }

            // Validation des formulaires
            var editForm = document.querySelector('#editCategoryModal form');
            if (editForm) {
                editForm.addEventListener('submit', function(event) {
                    var nameInput = this.querySelector('#editCategoryName');
                    if (!nameInput.value.trim()) {
                        event.preventDefault();
                        alert('Le nom de la catégorie est requis.');
                    }
                });
            }

            var addForm = document.querySelector('#addCategoryModal form');
            if (addForm) {
                addForm.addEventListener('submit', function(event) {
                    var nameInput = this.querySelector('#categoryName');
                    if (!nameInput.value.trim()) {
                        event.preventDefault();
                        alert('Le nom de la catégorie est requis.');
                    }
                });
            }
        });
    </script>
</body>
</html> 