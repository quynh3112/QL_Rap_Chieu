<?php
session_start();
require_once '../Config/db.php';
require_once '../models/foodModels.php';
require_once '../models/foodOrder.php';
require_once '../models/foodOrderDetail.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$foodModel = new Food($conn);
header('Content-Type: application/json');

if ($action == 'get_list') {
    $accId = $_SESSION['user_id'] ?? 1;
    $conn->begin_transaction();
    try {
        $orderM = new FoodOrder($conn);
        $detailM = new FoodOrderDetail($conn);
        $total = 0;
        foreach($input['items'] as $i) $total += $i['price'] * $i['soLuong'];

        $orderId = $orderM->create($accId, $input['bookingId'] ?? null, $total);
        foreach($input['items'] as $i) {
            $detailM->create($orderId, $i['foodId'], $i['soLuong'], $i['price']);
            //tru ton kho 
            $conn->query("UPDATE Food SET soLuongTon = soLuongTon - {$i['soLuong']} WHERE foodId = {$i['foodId']}");
        }
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đặt món thành công']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>