<?php
include "../models/room.php";

header("Content-Type: application/json");

$room = new Room();
$method = $_SERVER['REQUEST_METHOD'];

switch($method){

 
    case "GET":
        

        if(isset($_GET['branchId'])){
            $result = $room->getRoomById($_GET['branchId']);
            
            if($result->num_rows==0){
                echo json_encode([
                    "success" => false,
                    "message" => "Không tìm thấy phòng"
                ]);
                exit;
            }
            $data=[];
            while($row=$result->fetch_assoc()){
                $data[]=$row;


            }
            echo json_encode($data);
             exit;
        }

        $result = $room->getAllRooms();
        $data = [];

        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }

        echo json_encode($data);
        exit;



  
   case "POST":

    $input = json_decode(file_get_contents("php://input"), true);

    $tenPhong = $input['tenPhong'] ?? '';
    $tongGhe  = $input['tongGhe'] ?? 0;
    $loaiPhong = $input['loaiPhong'] ?? '';
    $branchId = $input['branchId'] ?? '';

    // 🔥 check thiếu dữ liệu
    if ($tenPhong == '' || $branchId == '') {
        echo json_encode(["error" => "Thiếu dữ liệu"]);
        exit;
    }

    // 🔥 check số ghế
    if ($tongGhe <= 0) {
        echo json_encode(["error" => "Số ghế phải > 0"]);
        exit;
    }

    // 🔥 check trùng
    if ($room->checkDuplicateInsert($tenPhong, $branchId)) {
        echo json_encode(["error" => "Tên phòng đã tồn tại"]);
        exit;
    }

    // 🔥 thêm phòng
    $response = $room->createRoom($tenPhong, $tongGhe, $loaiPhong, $branchId);

    echo json_encode($response);
    exit;


    case "PUT":

        $input = json_decode(file_get_contents("php://input"), true);

        $response = $room->updateRoom(
            $input['roomId'] ?? '',
            $input['tenPhong'] ?? '',
            $input['tongGhe'] ?? '',
            $input['loaiPhong'] ?? '',
            $input['branchId'] ?? ''
        );

        echo json_encode($response);
        exit;



    
    case "DELETE":

        $input = json_decode(file_get_contents("php://input"), true);

        $response = $room->deleteRoom(
            $input['roomId'] ?? ''
        );

        echo json_encode([
            "status"=>$response,
            "message"=>$response ? "Xóa thành công!" : "Xóa thất bại!"
        ]);
        exit;



    default:
        echo json_encode([
            "success" => false,
            "message" => "Method không hợp lệ"
        ]);
        exit;
}
?>