<?php
session_start();
//checkAdminRole
require_once '../Config/db.php';
require_once '../models/foodModels.php';
require_once '../models/foodOrder.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$foodModel = new Food($conn);
$orderModel = new FoodOrder($conn);
header('Content-Type: application/json');

switch ($action) {
    case 'list_all':
        echo json_encode(['success' => true, 'data' => $foodModel->getAll()->fetch_all(MYSQLI_ASSOC)]);
        break;
    case 'save':
        $foodModel->save($input);
        echo json_encode(['success' => true, 'message' => 'Lưu món ăn thành công']);
        break;
    case 'list_orders':
        echo json_encode(['success' => true, 'data' => $orderModel->getAll()->fetch_all(MYSQLI_ASSOC)]);
        break;
}
?>