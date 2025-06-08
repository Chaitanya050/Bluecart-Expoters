<?php
require_once 'controllers/ProductController.php';

$controller = new ProductController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit($id);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    case 'search':
        $controller->search();
        break;
    case 'update-stock':
        $controller->updateStock();
        break;
    default:
        $controller->index();
        break;
}
