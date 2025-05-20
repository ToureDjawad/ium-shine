<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/VoteController.php';

// Router simple
$route = $_GET['route'] ?? 'home';

switch($route) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;
        
    case 'category':
        $controller = new CategoryController();
        $controller->show($_GET['id'] ?? 1);
        break;
        
    case 'vote':
        $controller = new VoteController();
        $controller->vote();
        break;
        
    default:
        header('HTTP/1.0 404 Not Found');
        echo '404 - Page non trouvée';
        break;
}
?> 