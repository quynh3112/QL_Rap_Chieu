<?php
include "../config/db.php";
class Seat{
    public function createSeat($tongghe,$phongId){
       global $conn;
       $row=min(10,ceil($tongghe/10));
       $col=ceil($tongghe/10);
       $count=0;
       $mid=ceil($tongghe/2);
       for($i=0;$i<$row;$i++){
        for($j=1;$j<=$col;$j++){
            if($count>=$tongghe) break;
            $seat_number=chr(65+$i).$j;
            if($i==$mid){
                $loaiGhe="VIP";
                $price=10000;
            }
            else{
                $loaiGhe="Normal";
                $price=0;
            }
            $sql="INSERT INTO seat(room_id,tenGhe,loaiGhe,giaGhe) VALUES ('$phongId','$seat_number','$loaiGhe','$price')";
            $conn->query($sql);
            $count++;

        }
       }
    }
    public function findAll($phongId){
        global $conn;
        $sql="SELECT * FROM seat where phongId='$phongId' ";
        return $conn->query($sql);
    }
    public function deleteSeat($phongId){
        global $conn;
        return $conn->query("DELETE seat where phongId='$phongId'");
    }
}
?>