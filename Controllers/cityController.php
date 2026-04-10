<?php
include "../models/branch.php";
header("Content-Type: application/json");
$branch=new Branch();
$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'GET':
        $result=$branch->city();
        $data=[];
        while($row=$result->fetch_assoc()){
            $data[]=$row;
        }
        echo json_encode($data);
        break;

}
?>