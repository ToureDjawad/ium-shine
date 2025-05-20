<?php
class CandidateController {
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
    
    public function manage() {
        $this->checkAuth();
        
        $action = $_GET['action'] ?? 'list';
        
        switch($action) {
            case 'add':
                $this->add();
                break;
            case 'edit':
                $this->edit($_GET['id'] ?? 0);
                break;
            case 'delete':
                $this->delete($_GET['id'] ?? 0);
                break;
            case 'vote':
                $this->vote($_GET['id'] ?? 0, $_GET['type'] ?? 'add');
                break;
            default:
                $this->list();
                break;
        }
    }
    
    public function list() {
        $this->checkAuth();
        
        // Récupérer les catégories et leurs candidats avec le nombre de votes
        $query = "SELECT c.*, cat.name as category_name,
                 (SELECT COUNT(*) FROM votes WHERE candidate_id = c.id) as vote_count,
                 (SELECT COUNT(*) + 1 FROM candidates c2 
                  WHERE c2.category_id = c.category_id 
                  AND (SELECT COUNT(*) FROM votes WHERE candidate_id = c2.id) > 
                      (SELECT COUNT(*) FROM votes WHERE candidate_id = c.id)
                 ) as candidate_rank
                 FROM candidates c
                 JOIN categories cat ON c.category_id = cat.id
                 ORDER BY c.category_id, vote_count DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Si la table votes n'existe pas, on récupère juste les candidats
            $query = "SELECT c.*, cat.name as category_name, 0 as vote_count, 0 as candidate_rank
                     FROM candidates c
                     JOIN categories cat ON c.category_id = cat.id
                     ORDER BY c.category_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        include 'views/admin/candidates/list.php';
    }
    
    private function add() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $category_id = $_POST['category_id'] ?? 0;
            $motif = $_POST['motif'] ?? '';
            
            // Gestion de l'upload de photo
            $photo = 'uploads/default.jpg';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $photo = $upload_path;
                }
            }
            
            $query = "INSERT INTO candidates (name, description, motif, photo, category_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$name, $description, $motif, $photo, $category_id]);
            
            $_SESSION['success'] = "Le candidat a été ajouté avec succès";
            header('Location: admin.php?route=candidates');
            exit;
        }
        
        // Récupérer les catégories pour le formulaire
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/candidates/form.php';
    }
    
    private function edit($id) {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $category_id = $_POST['category_id'] ?? 0;
            $motif = $_POST['motif'] ?? '';
            
            // Gestion de l'upload de photo
            $photo = $_POST['current_photo'] ?? 'uploads/default.jpg';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    // Supprimer l'ancienne photo si ce n'est pas la photo par défaut
                    if ($photo !== 'uploads/default.jpg' && file_exists($photo)) {
                        unlink($photo);
                    }
                    $photo = $upload_path;
                }
            }
            
            $query = "UPDATE candidates SET name = ?, description = ?, motif = ?, photo = ?, category_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$name, $description, $motif, $photo, $category_id, $id]);
            
            $_SESSION['success'] = "Le candidat a été modifié avec succès";
            header('Location: admin.php?route=candidates');
            exit;
        }
        
        // Récupérer les informations du candidat
        $query = "SELECT * FROM candidates WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidate) {
            $_SESSION['error'] = "Candidat non trouvé";
            header('Location: admin.php?route=candidates');
            exit;
        }
        
        // Récupérer les catégories pour le formulaire
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/candidates/form.php';
    }
    
    private function delete($id) {
        $this->checkAuth();
        
        // Récupérer les informations du candidat pour supprimer sa photo
        $query = "SELECT photo FROM candidates WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($candidate) {
            if ($candidate['photo'] !== 'uploads/default.jpg' && file_exists($candidate['photo'])) {
                unlink($candidate['photo']);
            }
            
            // Supprimer le candidat
            $query = "DELETE FROM candidates WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            $_SESSION['success'] = "Le candidat a été supprimé avec succès";
        } else {
            $_SESSION['error'] = "Candidat non trouvé";
        }
        
        header('Location: admin.php?route=candidates');
        exit;
    }
    
    private function vote($id, $type) {
        $this->checkAuth();
        
        // Debug: Log received data
        error_log("Vote method called. Candidate ID: " . $id . ", Type: " . $type);
        error_log("POST Data: " . print_r($_POST, true));

        // Vérifier si le candidat existe
        $query = "SELECT * FROM candidates WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidate) {
            $_SESSION['error'] = "Candidat non trouvé";
            header('Location: admin.php?route=candidates');
            exit;
        }

        if ($type === 'add') {
            // Récupérer le nombre de votes à ajouter
            $vote_count = isset($_POST['vote_count']) ? (int)$_POST['vote_count'] : 1;
            
            if ($vote_count < 1) {
                $_SESSION['error'] = "Le nombre de votes doit être supérieur à 0";
                header('Location: admin.php?route=candidates');
                exit;
            }

            // Ajouter les votes
            $query = "INSERT INTO votes (candidate_id, voter_ip, voted_at) VALUES (?, ?, NOW())";
            $stmt = $this->db->prepare($query);
            
            $success_count = 0;
            for ($i = 0; $i < $vote_count; $i++) {
                $voter_ip = 'admin_' . $_SESSION['admin_id'] . '_' . time() . '_' . $i;
                try {
                    $stmt->execute([$id, $voter_ip]);
                    $success_count++;
                } catch (PDOException $e) {
                    // Ignorer les erreurs de contrainte d'unicité
                    continue;
                }
            }

            if ($success_count > 0) {
                $_SESSION['success'] = $success_count . " vote(s) ont été ajoutés au candidat";
            } else {
                $_SESSION['error'] = "Aucun vote n'a pu être ajouté";
            }
        } else {
            // Récupérer le nombre de votes à retirer
            $vote_count = isset($_POST['vote_count']) ? (int)$_POST['vote_count'] : 1;
            
            if ($vote_count < 1) {
                $_SESSION['error'] = "Le nombre de votes à retirer doit être supérieur à 0";
                header('Location: admin.php?route=candidates');
                exit;
            }

            // Vérifier le nombre total de votes
            $query = "SELECT COUNT(*) as total FROM votes WHERE candidate_id = ? AND voter_ip LIKE ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id, 'admin_' . $_SESSION['admin_id'] . '%']);
            $total_votes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            if ($vote_count > $total_votes) {
                $_SESSION['error'] = "Vous ne pouvez pas retirer plus de votes que le nombre total de votes";
                header('Location: admin.php?route=candidates');
                exit;
            }

            // Retirer les votes un par un pour éviter les problèmes de syntaxe
            $removed_count = 0;
            for ($i = 0; $i < $vote_count; $i++) {
                $query = "DELETE FROM votes WHERE candidate_id = ? AND voter_ip LIKE ? ORDER BY voted_at DESC LIMIT 1";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$id, 'admin_' . $_SESSION['admin_id'] . '%']);
                $removed_count += $stmt->rowCount();
            }
            
            if ($removed_count > 0) {
                $_SESSION['success'] = $removed_count . " vote(s) ont été retirés du candidat";
            } else {
                $_SESSION['error'] = "Aucun vote n'a pu être retiré";
            }
        }
        
        header('Location: admin.php?route=candidates');
        exit;
    }
}
?> 