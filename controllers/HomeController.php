<?php
class HomeController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function index() {
        // Récupérer toutes les catégories avec le nombre de candidats et de votes
        $query = "SELECT c.*,
                 (SELECT COUNT(*) FROM candidates WHERE category_id = c.id) as nombre_candidats,
                 (SELECT COUNT(*) FROM votes v JOIN candidates cand ON v.candidate_id = cand.id WHERE cand.category_id = c.id) as nombre_votes
                 FROM categories c 
                 ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'views/home.php';
    }
}
?> 