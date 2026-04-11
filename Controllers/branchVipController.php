<?php
header("Content-type: application/json");
$method=$_SERVER['REQUEST_METHOD'];
include "../models/branch.php";
$branch=new Branch();
switch($method){
    case "GET":
        $result=$branch->chiNhanhVip();
        if($result->num_rows==0){
            echo json_encode([
                "status"=>false,
                "message"=>"Không có chi nhánh vip "
            ]);
            exit;

        }
        $data=[];
        while($row=$result->fetch_assoc()){
            $data[]=$row;
        }
        echo json_encode($data);
        break;
}
?>