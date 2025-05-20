<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/CandidateController.php';
require_once 'controllers/VoteController.php';
require_once 'controllers/CategoryController.php';

// Router simple pour l'admin
$route = $_GET['route'] ?? 'dashboard';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id']) && $route !== 'login' && $route !== 'process-login') {
    header('Location: admin.php?route=login');
    exit;
}

switch($route) {
    case 'login':
        $controller = new AdminController();
        $controller->login();
        break;
        
    case 'process-login':
        $controller = new AdminController();
        $controller->processLogin();
        break;
        
    case 'logout':
        $controller = new AdminController();
        $controller->logout();
        break;
        
    case 'dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;
        
    case 'candidates':
        $controller = new CandidateController();
        $controller->manage();
        break;
        
    case 'votes':
        $controller = new VoteController();
        $controller->manage();
        break;
        
    case 'settings':
        $controller = new AdminController();
        $controller->settings();
        break;

    case 'update_admin_credentials':
        $controller = new AdminController();
        $controller->updateAdminCredentials();
        break;

    case 'add_category':
        $controller = new CategoryController();
        $controller->add();
        break;

    case 'edit_category':
        $controller = new CategoryController();
        $controller->edit();
        break;

    case 'delete_category':
        $controller = new CategoryController();
        $controller->delete();
        break;
        
    default:
        header('HTTP/1.0 404 Not Found');
        echo '404 - Page non trouvée';
        break;
}
?> 