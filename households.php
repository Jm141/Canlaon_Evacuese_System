<?php
require_once 'config/config.php';
require_once 'controllers/HouseholdController.php';

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    $controller = new HouseholdController();
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
$controller = new HouseholdController();

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
