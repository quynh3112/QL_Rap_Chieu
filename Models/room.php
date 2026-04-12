<?php
include "../config/db.php";
include "../models/seat.php";

class Room {

  public function createRoom($tenPhong, $tongGhe, $loaiPhong, $branchId){
    global $conn;

    if(
        empty(trim($tenPhong)) ||
        empty(trim($tongGhe)) ||
        empty(trim($loaiPhong)) ||
        empty(trim($branchId))
    ){
        return [
            "success" => false,
            "message" => "Không được để trống thông tin"
        ];
    }

    $checkName = $conn->query("SELECT * FROM room WHERE tenPhong='$tenPhong' and branchId='$branchId'");
    if($checkName->num_rows > 0){
        return [
            "success" => false,
            "message" => "Tên phòng đã tồn tại"
        ];
    }

    $sql = "INSERT INTO room(tenPhong, tongGhe, loaiPhong, branchId)
            VALUES ('$tenPhong', '$tongGhe', '$loaiPhong', '$branchId')";

    if($conn->query($sql)){

        $room_id = $conn->insert_id;

        $seat = new Seat();
        $seat->createSeat($tongGhe, $room_id);

        return [
            "success" => true,
            "message" => "Thêm phòng thành công"
        ];
    }

    return [
        "success" => false,
        "message" => "Thêm phòng thất bại"
    ];
}

    public function getAllRooms(){
        global $conn;
        return $conn->query("SELECT * FROM room");
    }

    public function getRoomById($branchId){
        global $conn;
        return $conn->query("SELECT * FROM room WHERE branchId='$branchId'");
    }

    public function updateRoom($room_id, $tenPhong, $tongGhe, $loaiPhong, $branchId){
        global $conn;

        $sql = "UPDATE room
                SET tenPhong='$tenPhong',
                    tongGhe='$tongGhe',
                    loaiPhong='$loaiPhong',
                    branchId='$branchId'
                WHERE roomId='$room_id'";

        if($conn->query($sql)){
            $seat = new Seat();
            $seat->deleteSeat($room_id);
            $seat->createSeat($tongGhe, $room_id);

            return true;
        }

        return false;
    }

    public function deleteRoom($room_id){
        global $conn;

        $seat = new Seat();
        $seat->deleteSeat($room_id);

        return $conn->query("DELETE FROM room WHERE roomId='$room_id'");
    }
   
}
    
 
    

?>