<?php
session_start();
header('Content-Type: application/json'); 
require_once '../Config/db.php';
require_once '../models/foodModels.php';

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Lỗi kết nối database']));
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$foodModel = new Food($conn);

switch ($action) {
    case 'list_all':
        $res = $foodModel->getAll();
        echo json_encode(['success' => true, 'data' => $res->fetch_all(MYSQLI_ASSOC)]);
        break;

    case 'save':
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu gửi lên không đúng định dạng JSON']);
            break;
        }
        if ($foodModel->save($input)) {
            echo json_encode(['success' => true, 'message' => 'Lưu thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi SQL: ' . $conn->error]);
        }
        break;
}
?>