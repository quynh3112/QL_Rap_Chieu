<?php
include "../config/db.php";
class Seat{
public function createSeat($tongghe, $phongId){
    global $conn;

    $col = 10; // mỗi hàng 10 ghế
    $row = ceil($tongghe / $col);
    $count = 0;

    $mid = floor($row / 2); // hàng giữa VIP

    for($i = 0; $i < $row; $i++){
        for($j = 1; $j <= $col; $j++){

            if($count >= $tongghe){
                break 2;
            }

            $seat_number = chr(65 + $i) . $j;

            if($i == $mid){
                $loaiGhe = "VIP";
                $price = 10000;
            } else {
                $loaiGhe = "Normal";
                $price = 0;
            }

            $sql = "INSERT INTO seat(roomId, tenGhe, loaiGhe, giaGhe) 
                    VALUES ('$phongId', '$seat_number', '$loaiGhe', '$price')";
            $conn->query($sql);

            $count++;
        }
    }
}
    public function findAll($phongId){
        global $conn;
        $sql="SELECT * FROM seat where roomId='$phongId' ";
        return $conn->query($sql);
    }
    public function deleteSeat($phongId){
        global $conn;
        return $conn->query("DELETE From  seat where roomId='$phongId'");
    }
}
?>