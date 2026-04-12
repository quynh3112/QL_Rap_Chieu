<?php
include "../Config/db.php";
include "../Models/Schedule.php";
$schedule = new Schedule($conn);
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'GET':
        if(isset($_GET['id'])){
            $data = $schedule->getById($_GET['id']);
            echo json_encode($data);
        }
        elseif(isset($_GET['movieId'])){
            $result = $schedule->getByMovie($_GET['movieId']);
            $data = [];
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
            echo json_encode($data);
        }
        else {
            $result = $schedule->getAll();
            $data = [];
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
            echo json_encode($data);
        }
    break;
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $schedule->create($input);
        if($result === "duplicate"){
            echo json_encode(["message" => "Trùng lịch chiếu"]);
        } elseif($result){
            echo json_encode(["message" => "Thêm thành công"]);
        } else {
            echo json_encode(["message" => "Lỗi"]);
        }
    break;
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo json_encode(["message" => "Thiếu ID"]);
            return;
        }
        $result = $schedule->update($id, $input);
        if($result === "duplicate"){
            echo json_encode(["message" => "Trùng lịch"]);
        } elseif($result){
            echo json_encode(["message" => "Cập nhật thành công"]);
        } else {
            echo json_encode(["message" => "Lỗi"]);
        }
    break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo json_encode(["message" => "Thiếu ID"]);
            return;
        }
        $result = $schedule->delete($id);
        if($result === "has_booking"){
            echo json_encode(["message" => "Không thể xóa, đã có người đặt vé"]);
        } elseif($result){
            echo json_encode(["message" => "Xóa thành công"]);
        } else {
            echo json_encode(["message" => "Lỗi"]);
        }
    break;
    default:
        echo json_encode(["message" => "Method không hợp lệ"]);
}