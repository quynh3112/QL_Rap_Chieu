<?php
include "../models/seat.php";
header("Content-Type: application/json");
$seat = new Seat();
$method = $_SERVER['REQUEST_METHOD'];
switch($method){

    case "GET":
        if (isset($_GET['tile_phim'])) {
    $result = $seat->tiLelapDay();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}
       if(isset($_GET['roomId'])){
            $result = $seat->findAll($_GET['roomId']);
            
            if($result->num_rows==0){
                echo json_encode([
                    "success" => false,
                    "message" => "không tìm thấy ghế"
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

       break;

   
}
?>