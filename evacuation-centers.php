<?php
require_once 'config/config.php';
require_once 'controllers/EvacuationCenterController.php';

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    $controller = new EvacuationCenterController();
    switch ($_GET['ajax']) {
        case 'delete':
            $controller->delete();
            break;
        case 'auto_assign':
            $controller->autoAssign();
            break;
        case 'reassign':
            $controller->reassign();
            break;
        case 'search':
            $controller->search();
            break;
    }
    exit();
}

// Handle regular requests
$action = $_GET['action'] ?? 'index';
$controller = new EvacuationCenterController();

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