<?php
include "../models/room.php";

header("Content-Type: application/json");

$room = new Room();
$method = $_SERVER['REQUEST_METHOD'];

switch($method){

 
    case "GET":

        if(isset($_GET['room_id'])){
            $result = $room->getRoomById($_GET['room_id']);
            $data = $result->fetch_assoc();

            if(!$data){
                echo json_encode([
                    "success" => false,
                    "message" => "Không tìm thấy phòng"
                ]);
                exit;
            }

            echo json_encode([
                "success" => true,
                "data" => $data
            ]);
            exit;
        }

        $result = $room->getAllRooms();
        $data = [];

        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data" => $data
        ]);
        exit;



  
    case "POST":

        $input = json_decode(file_get_contents("php://input"), true);

        $response = $room->createRoom(
            $input['tenPhong'] ?? '',
            $input['tongGhe'] ?? '',
            $input['loaiPhong'] ?? '',
            $input['branchId'] ?? ''
        );

        echo json_encode($response);
        exit;



    // =========================
    // PUT: Cập nhật phòng
    // =========================
    case "PUT":

        $input = json_decode(file_get_contents("php://input"), true);

        $response = $room->updateRoom(
            $input['room_id'] ?? '',
            $input['tenPhong'] ?? '',
            $input['tongGhe'] ?? '',
            $input['loaiPhong'] ?? '',
            $input['branchId'] ?? ''
        );

        echo json_encode($response);
        exit;



    // =========================
    // DELETE: Xóa phòng
    // =========================
    case "DELETE":

        $input = json_decode(file_get_contents("php://input"), true);

        $response = $room->deleteRoom(
            $input['room_id'] ?? ''
        );

        echo json_encode($response);
        exit;



    // =========================
    // METHOD KHÔNG HỢP LỆ
    // =========================
    default:
        echo json_encode([
            "success" => false,
            "message" => "Method không hợp lệ"
        ]);
        exit;
}
?>