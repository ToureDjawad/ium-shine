<?php
class CategoryController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function show($categoryId) {
        // Récupérer les informations de la catégorie
        $query = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$category) {
            header('Location: index.php');
            exit;
        }
        
        // D'abord, récupérer tous les candidats de la catégorie
        $query = "SELECT * FROM candidates WHERE category_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$categoryId]);
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensuite, pour chaque candidat, récupérer son nombre de votes
        foreach ($candidates as &$candidate) {
            $query = "SELECT COUNT(*) as vote_count FROM votes WHERE candidate_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$candidate['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $candidate['vote_count'] = $result['vote_count'];
        }
        
        // Calculer le total des votes pour cette catégorie
        $totalCategoryVotes = array_sum(array_column($candidates, 'vote_count'));
        
        // Calculer les pourcentages pour chaque candidat
        foreach ($candidates as &$candidate) {
            $candidate['percentage'] = $totalCategoryVotes > 0 ? 
                round(($candidate['vote_count'] / $totalCategoryVotes) * 100, 1) : 0;
        }
        
        // Trier les candidats par nombre de votes décroissant, puis par nom
        usort($candidates, function($a, $b) {
            if ($a['vote_count'] == $b['vote_count']) {
                return strcmp($a['name'], $b['name']);
            }
            return $b['vote_count'] - $a['vote_count'];
        });
        
        // Debug output
        // echo "Debug: Catégorie ID: " . $categoryId . "";
        // echo "Debug: Nombre de candidats trouvés : " . count($candidates) . " ";
        // echo "Debug: Liste des candidats : ";
        // foreach ($candidates as $c) {
        //     echo "\n- " . $c['name'] . " (ID: " . $c['id'] . ", Votes: " . $c['vote_count'] . ")";
        // }
        // echo "";
        
        // Inclure la vue
        include 'views/category.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if (empty($name)) {
                $_SESSION['error'] = "Le nom de la catégorie est requis.";
                header('Location: admin.php?route=settings');
                exit;
            }
            
            $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute([$name, $description])) {
                $_SESSION['success'] = "Catégorie ajoutée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de la catégorie.";
            }
            header('Location: admin.php?route=settings');
            exit;
        }
    }
    
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if (empty($name)) {
                $_SESSION['error'] = "Le nom de la catégorie est requis.";
                header('Location: admin.php?route=settings');
                exit;
            }
            
            // Vérifier si la catégorie existe
            $checkQuery = "SELECT id FROM categories WHERE id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([$id]);
            
            if (!$checkStmt->fetch()) {
                $_SESSION['error'] = "Catégorie non trouvée.";
                header('Location: admin.php?route=settings');
                exit;
            }
            
            $query = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute([$name, $description, $id])) {
                $_SESSION['success'] = "Catégorie mise à jour avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour de la catégorie.";
            }
            header('Location: admin.php?route=settings');
            exit;
        }
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            
            // Vérifier s'il y a des candidats dans cette catégorie
            $query = "SELECT COUNT(*) as count FROM candidates WHERE category_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $_SESSION['error'] = "Impossible de supprimer cette catégorie car elle contient des candidats.";
                header('Location: admin.php?route=settings');
                exit;
            }
            
            $query = "DELETE FROM categories WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute([$id])) {
                $_SESSION['success'] = "Catégorie supprimée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de la catégorie.";
            }
            
            header('Location: admin.php?route=settings');
            exit;
        }
    }
}
?> 