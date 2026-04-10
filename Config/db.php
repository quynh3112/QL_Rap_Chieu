<?php
$host="localhost";
$name="root";
$password="";
$db="ql_rap";
$conn=new mysqli($host,$name,$password,$db);
if($conn->connect_error){
    die("Kết nối thất bại" . $conn->connect_error);
}
?>