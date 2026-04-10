<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php'; 

require_once '../Config/db.php';
require_once '../models/foodModels.php';
require_once '../models/foodOrder.php';
require_once '../models/foodOrderDetail.php';

ob_clean();
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$foodModel = new Food($conn);

switch ($action) {
    case 'list_all':
        //ai cũng xem được
        checkAuth(['Admin', 'Manager', 'Employee', 'Customer']);
        
        $res = $foodModel->getAll();
        $data = [];
        if ($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'list_orders':
        //nhân viên, quản lý mới xem được
        checkAuth(['Admin', 'Manager', 'Employee']);
        
        $orderM = new FoodOrder($conn);
        $res = $orderM->getAll();
        $data = [];
        if ($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'save':
        // admin or man mới sửa được
        checkAuth(['Admin', 'Manager']);
        
        if ($foodModel->save($input)) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'message' => $conn->error]);
        break;

    case 'delete':
        // admin xoá
        checkAuth(['Admin']);
        
        $id = $_GET['foodId'] ?? 0;
        if ($foodModel->delete($id)) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'message' => 'Lỗi xoá']);
        break;

    case 'place_order':
        // tất cả role có thể đặt
        checkAuth(['Customer', 'Admin', 'Manager', 'Employee']);
        
        $conn->begin_transaction();
        try {
            $orderM = new FoodOrder($conn);
            $detailM = new FoodOrderDetail($conn);
            
            // lấy id từ session
            $accId = $_SESSION['user']['accountId'] ?? 1; 
            
            $total = 0;
            foreach($input['items'] as $i) $total += $i['price'] * $i['soLuong'];
            
            $orderId = $orderM->create($accId, $input['bookingId'] ?? null, $total);
            foreach($input['items'] as $i) {
                $detailM->create($orderId, $i['foodId'], $i['soLuong'], $i['price']);
                $conn->query("UPDATE Food SET soLuongTon = soLuongTon - {$i['soLuong']} WHERE foodId = {$i['foodId']}");
            }
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
        break;
}
exit;