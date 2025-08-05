<?php
require_once 'config/config.php';
require_once 'controllers/ReportController.php';

// Handle regular requests
$action = $_GET['action'] ?? 'index';
$controller = new ReportController();

switch ($action) {
    case 'export':
        $controller->export();
        break;
    default:
        $controller->index();
        break;
}
?> 