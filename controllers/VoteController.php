<?php
class VoteController {
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
                $this->addVote($_GET['id'] ?? 0);
                break;
            case 'remove':
                $this->removeVote($_GET['id'] ?? 0);
                break;
            case 'reset':
                $this->resetVotes($_GET['category_id'] ?? 0);
                break;
            default:
                $this->list();
                break;
        }
    }
    
    private function list() {
        $this->checkAuth();
        
        // Récupérer toutes les catégories avec leurs statistiques (total candidats, total votes)
        $query = "SELECT c.*, 
                 (SELECT COUNT(*) FROM candidates WHERE category_id = c.id) as total_candidates,
                 (SELECT COUNT(*) FROM votes WHERE candidate_id IN (SELECT id FROM candidates WHERE category_id = c.id)) as total_votes
                 FROM categories c 
                 ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Pour chaque catégorie, récupérer les candidats avec leurs statistiques (vote_count, candidate_rank)
        foreach ($categories as &$category) {
            $query = "SELECT c.*, 
                     (SELECT COUNT(*) FROM votes WHERE candidate_id = c.id) as vote_count,
                     (SELECT COUNT(*) + 1 FROM candidates c2 
                      LEFT JOIN (SELECT candidate_id, COUNT(*) as vote_count FROM votes GROUP BY candidate_id) v2 ON c2.id = v2.candidate_id
                      WHERE c2.category_id = c.category_id 
                      AND COALESCE(v2.vote_count, 0) > COALESCE((SELECT COUNT(*) FROM votes WHERE candidate_id = c.id), 0)
                     ) as candidate_rank
                     FROM candidates c 
                     WHERE c.category_id = ? 
                     ORDER BY vote_count DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$category['id']]);
            $category['candidates'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculer les pourcentages pour chaque candidat
            $totalCategoryVotes = array_sum(array_column($category['candidates'], 'vote_count'));
            foreach ($category['candidates'] as &$candidate) {
                $candidate['percentage'] = $totalCategoryVotes > 0 ? 
                    round(($candidate['vote_count'] / $totalCategoryVotes) * 100, 1) : 0;
            }
        }
        
        include 'views/admin/votes/list.php';
    }
    
    private function addVote($candidateId) {
        $this->checkAuth();
        
        // Vérifier si le candidat existe
        $query = "SELECT * FROM candidates WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$candidateId]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidate) {
            $_SESSION['error'] = "Candidat non trouvé";
            header('Location: admin.php?route=votes');
            exit;
        }
        
        // Ajouter un vote dans la table votes
        $query = "INSERT INTO votes (candidate_id, voter_ip, voted_at) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        // Utiliser un identifiant admin unique pour les votes ajoutés manuellement par l'admin
        $voter_ip = 'admin_' . $_SESSION['admin_id'] . '_' . time(); 
        $stmt->execute([$candidateId, $voter_ip]);
        
        $_SESSION['success'] = "Un vote a été ajouté au candidat " . htmlspecialchars($candidate['name']);
        header('Location: admin.php?route=votes');
        exit;
    }
    
    private function removeVote($candidateId) {
        $this->checkAuth();
        
        // Vérifier si le candidat existe
        $query = "SELECT * FROM candidates WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$candidateId]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidate) {
            $_SESSION['error'] = "Candidat non trouvé";
            header('Location: admin.php?route=votes');
            exit;
        }
        
        // Vérifier si le candidat a des votes à retirer (on compte les votes dans la table votes)
        $query = "SELECT COUNT(*) as total FROM votes WHERE candidate_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$candidateId]);
        $total_votes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        if ($total_votes <= 0) {
            $_SESSION['error'] = "Ce candidat n'a pas de votes à retirer";
            header('Location: admin.php?route=votes');
            exit;
        }
        
        // Retirer le dernier vote pour ce candidat (potentiellement ajouté par admin)
        $query = "DELETE FROM votes WHERE candidate_id = ? ORDER BY voted_at DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$candidateId]);
        
        $_SESSION['success'] = "Un vote a été retiré au candidat " . htmlspecialchars($candidate['name']);
        header('Location: admin.php?route=votes');
        exit;
    }
    
    private function resetVotes($categoryId) {
        $this->checkAuth();
        
        // Vérifier si la catégorie existe
        $query = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            $_SESSION['error'] = "Catégorie non trouvée";
            header('Location: admin.php?route=votes');
            exit;
        }
        
        // Réinitialiser les votes pour tous les candidats de la catégorie (supprimer les entrées de la table votes)
        $query = "DELETE FROM votes WHERE candidate_id IN (SELECT id FROM candidates WHERE category_id = ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$categoryId]);
        
        $_SESSION['success'] = "Les votes ont été réinitialisés pour la catégorie " . htmlspecialchars($category['name']);
        header('Location: admin.php?route=votes');
        exit;
    }
    
    public function vote() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $candidateId = $_POST['candidate_id'] ?? 0;
            $voterIp = $_SERVER['REMOTE_ADDR'];
            
            try {
                // Vérifier si le candidat existe
                $query = "SELECT * FROM candidates WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$candidateId]);
                $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$candidate) {
                    throw new Exception("Candidat non trouvé");
                }
                
                // Vérifier si l'utilisateur a déjà voté pour ce candidat
                $query = "SELECT * FROM votes WHERE candidate_id = ? AND voter_ip = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$candidateId, $voterIp]);
                
                if ($stmt->fetch()) {
                    throw new Exception("Vous avez déjà voté pour ce candidat");
                }
                
                // Enregistrer le vote
                $this->db->beginTransaction();
                
                // Ajouter le vote dans la table votes
                $query = "INSERT INTO votes (candidate_id, voter_ip) VALUES (?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$candidateId, $voterIp]);
                
                // Mettre à jour le compteur de votes du candidat
                $query = "UPDATE candidates SET votes = votes + 1 WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$candidateId]);
                
                $this->db->commit();
                
                $_SESSION['success'] = "Votre vote a été enregistré avec succès";
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        // Rediriger vers la page précédente
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    public function getVoteStatus($candidateId) {
        $voterIp = $_SERVER['REMOTE_ADDR'];
        
        $query = "SELECT * FROM votes WHERE candidate_id = ? AND voter_ip = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$candidateId, $voterIp]);
        
        return $stmt->fetch() ? true : false;
    }
    
    public function getCandidateVotes($candidateId) {
        $query = "SELECT COUNT(*) as vote_count FROM votes WHERE candidate_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$candidateId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['vote_count'] ?? 0;
    }
    
    public function getCategoryVotes($categoryId) {
        $query = "SELECT c.id, c.name, COUNT(v.id) as vote_count 
                 FROM candidates c 
                 LEFT JOIN votes v ON c.id = v.candidate_id 
                 WHERE c.category_id = ? 
                 GROUP BY c.id 
                 ORDER BY vote_count DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$categoryId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 