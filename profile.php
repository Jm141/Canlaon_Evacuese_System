<?php
require_once 'config/config.php';
require_once 'Controller.php';
require_once 'controllers/ProfileController.php';

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    $controller = new ProfileController();
    switch ($_GET['ajax']) {
        case 'delete':
            $controller->delete();
            break;
        case 'toggle_status':
            $controller->toggleStatus();
            break;
        case 'reset_password':
            $controller->resetPassword();
            break;
        case 'search':
            $controller->search();
            break;
    }
    exit();
}

// Handle regular requests
$action = $_GET['action'] ?? 'index';
$controller = new ProfileController();

switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'view':
        $controller->view();
        break;
    default:
        $controller->index();
        break;
}
?>
