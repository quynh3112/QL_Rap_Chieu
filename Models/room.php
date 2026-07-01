<?php
include "../config/db.php";
include "../models/seat.php";

class Room {

 public function createRoom($tenPhong, $tongGhe, $loaiPhong, $branchId){
    global $conn;

    // 🔥 CHECK TRÙNG
    if ($this->checkDuplicateInsert($tenPhong, $branchId)) {
        return [
            "success" => false,
            "error" => "Tên phòng đã tồn tại"
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
        "error" => "Thêm phòng thất bại"
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
    public function checkDuplicateInsert($tenPhong, $branchId){
    global $conn;

    $sql = "SELECT * FROM room 
            WHERE tenPhong='$tenPhong' 
            AND branchId='$branchId'";

    $result = $conn->query($sql);

    return $result->num_rows > 0;
}
   
}
    
 
    

?>