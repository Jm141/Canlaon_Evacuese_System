<?php
require_once 'config/config.php';
require_once 'controllers/ResidentController.php';

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    $controller = new ResidentController();
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
$controller = new ResidentController();

switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'add-family-member':
        $controller->addFamilyMember();
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
