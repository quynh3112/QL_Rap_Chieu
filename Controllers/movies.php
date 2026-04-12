<?php
include "../Config/db.php";
include "../Models/movie.php";
$movie = new Movie($conn);
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case 'GET':
        if(isset($_GET['id'])){
            $data = $movie->getById($_GET['id']);
            echo json_encode($data);
        } 
        elseif(isset($_GET['name']) || isset($_GET['category']) || isset($_GET['year']) || isset($_GET['status'])){
            $result = $movie->search(
                $_GET['name'] ?? null,
                $_GET['category'] ?? null,
                $_GET['year'] ?? null,
                $_GET['status'] ?? null
            );
            $data = [];
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
            echo json_encode($data);
        }
        else {
            $result = $movie->getAll();
            $data = [];
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
            echo json_encode($data);
        }
    break;
    case 'POST':
    $uploadDir = "../uploads/";

    $imgName = $_POST['oldImg'] ?? null;

    if (!empty($_FILES['img']['name'])) {
        $imgName = time() . "_" . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $uploadDir . $imgName);
    }

    $data = $_POST;
    $data['img'] = $imgName;
if (isset($_GET['id'])) {
        if ($movie->update($_GET['id'], $data)) {
            echo json_encode(["message" => "Cập nhật thành công"]);
        } else {
            echo json_encode(["message" => "Lỗi"]);
        }
    } else {
    if ($movie->create($data)) {
        echo json_encode(["message" => "Thêm phim thành công"]);
    } else {
        echo json_encode(["message" => "Lỗi"]);
    }
    }
break;
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo json_encode(["message" => "Thiếu ID"]);
            return;
        }
        if($movie->update($id, $input)){
            echo json_encode(["message" => "Cập nhật thành công"]);
        } else {
            echo json_encode(["message" => "Lỗi"]);
        }
    break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo json_encode(["message" => "Thiếu ID"]);
            return;
        }
        if($movie->delete($id)){
            echo json_encode(["message" => "Xóa thành công"]);
        } else {
            echo json_encode(["message" => "Lỗi"]);
        }
    break;
    default:
        echo json_encode(["message" => "Method không hợp lệ"]);
}