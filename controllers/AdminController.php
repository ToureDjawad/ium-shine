<?php
class AdminController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: admin.php?route=login');
            exit;
        }
    }
    
    public function login() {
        if (isset($_SESSION['admin_id'])) {
            header('Location: admin.php');
            exit;
        }
        include 'views/admin/login.php';
    }
    
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Veuillez remplir tous les champs";
                header('Location: admin.php?route=login');
                exit;
            }
            
            try {
                $query = "SELECT * FROM admins WHERE username = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: admin.php');
                    exit;
                } else {
                    $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect";
                    header('Location: admin.php?route=login');
                    exit;
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Une erreur est survenue lors de la connexion";
                header('Location: admin.php?route=login');
                exit;
            }
        }
        
        header('Location: admin.php?route=login');
        exit;
    }
    
    public function logout() {
        session_destroy();
        header('Location: admin.php?route=login');
        exit;
    }
    
    public function dashboard() {
        $this->checkAuth();
        
        // Récupérer les statistiques générales
        $query = "SELECT 
            (SELECT COUNT(*) FROM candidates) as total_candidates,
            (SELECT COUNT(*) FROM categories) as total_categories,
            (SELECT COUNT(*) FROM votes) as total_votes,
            (SELECT COUNT(DISTINCT voter_ip) FROM votes) as total_voters";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Si la table votes n'existe pas, on récupère juste les statistiques de base
            $query = "SELECT 
                (SELECT COUNT(*) FROM candidates) as total_candidates,
                (SELECT COUNT(*) FROM categories) as total_categories";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_votes'] = 0; // Valeur par défaut si la table votes n'existe pas
            $stats['total_voters'] = 0; // Valeur par défaut si la table votes n'existe pas
        }

        // Récupérer les statistiques par catégorie avec le rang des candidats
        $queryStats = "SELECT 
            c.name as category_name,
            (SELECT COUNT(*) FROM candidates WHERE category_id = c.id) as total_candidates_category,
            (SELECT COUNT(*) FROM votes v JOIN candidates cand ON v.candidate_id = cand.id WHERE cand.category_id = c.id) as total_votes_category
            FROM categories c
            ORDER BY c.name";

        $stmtStats = $this->db->prepare($queryStats);
        $stmtStats->execute();
        $statsByCategory = $stmtStats->fetchAll(PDO::FETCH_ASSOC);

        // Calculer la moyenne de votes par candidat (global)
        $moyenneVotes = ($stats['total_candidates'] > 0) ? round($stats['total_votes'] / $stats['total_candidates'], 2) : 0;
        
        // Extraire les statistiques
        $totalCandidates = $stats['total_candidates'] ?? 0;
        $totalCategories = $stats['total_categories'] ?? 0;
        $totalVotes = $stats['total_votes'] ?? 0;
        $totalVoters = $stats['total_voters'] ?? 0;
        
        include 'views/admin/dashboard.php';
    }
    
    public function settings() {
        $this->checkAuth();
        
        $action = $_GET['action'] ?? 'view';
        
        switch($action) {
            case 'add_category':
                $this->addCategory();
                break;
            case 'delete_category':
                $this->deleteCategory($_GET['id'] ?? 0);
                break;
            case 'update_general':
                $this->updateGeneralSettings();
                break;
            case 'update_security':
                $this->updateSecuritySettings();
                break;
            default:
                $this->viewSettings();
                break;
        }
    }
    
    private function viewSettings() {
        // Récupérer les catégories
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les paramètres généraux
        $query = "SELECT * FROM settings";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convertir en tableau associatif
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['key']] = $setting['value'];
        }
        
        include 'views/admin/settings.php';
    }
    
    private function addCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $name = trim($_POST['name']);
            
            $query = "INSERT INTO categories (name) VALUES (?)";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute([$name])) {
                $_SESSION['success'] = "La catégorie a été ajoutée avec succès.";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'ajout de la catégorie.";
            }
        }
        
        header('Location: admin.php?route=settings');
        exit;
    }
    
    private function deleteCategory($id) {
        if ($id > 0) {
            // Vérifier s'il y a des candidats dans cette catégorie
            $query = "SELECT COUNT(*) as count FROM candidates WHERE category_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $_SESSION['error'] = "Impossible de supprimer cette catégorie car elle contient des candidats.";
            } else {
                $query = "DELETE FROM categories WHERE id = ?";
                $stmt = $this->db->prepare($query);
                
                if ($stmt->execute([$id])) {
                    $_SESSION['success'] = "La catégorie a été supprimée avec succès.";
                } else {
                    $_SESSION['error'] = "Une erreur est survenue lors de la suppression de la catégorie.";
                }
            }
        }
        
        header('Location: admin.php?route=settings');
        exit;
    }
    
    private function updateGeneralSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
                'site_name' => $_POST['site_name'] ?? '',
                'site_description' => $_POST['site_description'] ?? '',
                'admin_email' => $_POST['admin_email'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                $query = "INSERT INTO settings (key, value) VALUES (?, ?) 
                         ON DUPLICATE KEY UPDATE value = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$key, $value, $value]);
            }
            
            $_SESSION['success'] = "Les paramètres ont été mis à jour avec succès.";
        }
        
        header('Location: admin.php?route=settings');
        exit;
    }
    
    private function updateSecuritySettings() {
        // Cette méthode n'est plus utilisée directement par le formulaire de sécurité
        // La logique est maintenant dans updateAdminCredentials()
        // Vous pouvez la supprimer ou la laisser comme référence si besoin.
    }
    
    // Nouvelle méthode pour mettre à jour les identifiants de l'administrateur
    public function updateAdminCredentials() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['admin_id'] ?? null;
            $currentPassword = $_POST['current_password'] ?? '';
            $newUsername = trim($_POST['new_username'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmNewPassword = $_POST['confirm_new_password'] ?? '';
            
            if (!$adminId) {
                 $_SESSION['error'] = "ID administrateur non trouvé en session.";
                 header('Location: admin.php?route=settings');
                 exit;
            }

            // Récupérer l'administrateur actuel pour vérifier le mot de passe
            $query = "SELECT id, username, password FROM admins WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$adminId]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin || !password_verify($currentPassword, $admin['password'])) {
                $_SESSION['error'] = "Mot de passe actuel incorrect.";
            } elseif (empty($newUsername)) {
                 $_SESSION['error'] = "Le nouveau nom d'utilisateur ne peut pas être vide.";
            } elseif ($newPassword !== '' && $newPassword !== $confirmNewPassword) {
                $_SESSION['error'] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
            } elseif ($newPassword !== '' && strlen($newPassword) < 8) {
                 $_SESSION['error'] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
            } else {
                // Préparer la requête de mise à jour
                $updateFields = [];
                $updateValues = [];
                
                // Mettre à jour le nom d'utilisateur s'il a changé
                if ($newUsername !== $admin['username']) {
                    $updateFields[] = "username = ?";
                    $updateValues[] = $newUsername;
                }
                
                // Mettre à jour le mot de passe s'il a été fourni
                if ($newPassword !== '') {
                    $updateFields[] = "password = ?";
                    $updateValues[] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
                
                // Exécuter la mise à jour si des champs doivent être modifiés
                if (!empty($updateFields)) {
                    $query = "UPDATE admins SET " . implode(", ", $updateFields) . " WHERE id = ?";
                    $updateValues[] = $adminId; // Ajouter l'ID à la fin pour la clause WHERE
                    
                    $stmt = $this->db->prepare($query);
                    
                    if ($stmt->execute($updateValues)) {
                        // Mettre à jour la session si le nom d'utilisateur a changé
                        if ($newUsername !== $admin['username']) {
                            $_SESSION['admin_username'] = $newUsername;
                        }
                        $_SESSION['success'] = "Vos identifiants ont été mis à jour avec succès.";
                    } else {
                        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de vos identifiants.";
                         // Gérer spécifiquement l'erreur de nom d'utilisateur déjà existant
                         if ($stmt->errorCode() === '23000') { // Code d'erreur pour violation d'intégrité (unique constraint)
                             $_SESSION['error'] = "Le nom d'utilisateur '".$newUsername."' est déjà utilisé.";
                         }
                    }
                } else {
                     $_SESSION['error'] = "Aucune modification à enregistrer.";
                }
            }
        }
        
        header('Location: admin.php?route=settings');
        exit;
    }
}
?> 