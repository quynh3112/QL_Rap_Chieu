<?php
include "../models/Branch.php";
header("Content-Type: application/json");


$branch = new Branch();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
   
    case "GET":

        $id = $_GET['id'] ?? null;
        $city=$_GET['thanhPho']??null;
        $vip=$_GET['vip']??null;
        

        $branchId=$_GET['branchId']?? null;
        if(isset($_GET['doanhthu_all'])){
            $result=$branch->doanhThuTheoChiNhanh();
            $data=[];
            while($row=$result->fetch_assoc()){
                $data[]=$row;
            }
            echo json_encode($data);
            exit;
        }

        if($id){

            $result = $branch->find($id);

            if ($result->num_rows <= 0) {
                echo json_encode(["status" => false, "message" => "Không tìm thấy chi nhánh"]);
                exit;
            }

            echo json_encode($result->fetch_assoc());
            exit;
            
        }
        if($branchId){
            $result = $branch->movieByBranch($branchId);

            if ($result->num_rows <= 0) {
                echo json_encode(["status" => false, "message" => "Hiện tại chưa có bộ phim nào chiếu ở rạp này!"]);
                exit;
            }

            $data=[];
            while($row=$result->fetch_assoc()){
                $data[]=$row;
            }
            echo json_encode($data);
            exit;
        }
        if($city){
            $result=$branch->brandBycity($city);
            if($result->num_rows==0){
                echo json_encode([
                    "status"=>false,
                    "message"=>"Không tồn tại!"
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
        if($vip){
            $result=$branch->movieByRoomVip($vip);
            if($result->num_rows==0){
                echo json_encode([
                    "status"=>false,
                    "message"=>"Không có bộ phim nào đang chiếu ở chi nhánh này"
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


        
            $result = $branch->findAll();
            $list = [];

            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
            echo json_encode($list);

        
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        $tenBranch = $data['tenBranch'] ?? null;
        $diaChi = $data['diaChi'] ?? null;
        $thanhPho = $data['thanhPho'] ?? null;

        if (!$tenBranch || !$diaChi || !$thanhPho) {
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Không được bỏ trống dữ liệu!"]);
            exit;
        }

        $check = $branch->check($tenBranch);

        if ($check->num_rows > 0) {
            http_response_code(409);
            echo json_encode(["status" => false, "message" => "Tên chi nhánh đã tồn tại!"]);
            exit;
        }

        $result = $branch->create($tenBranch, $diaChi, $thanhPho);

        echo json_encode([

            "status" => $result ? true : false,
            "message" => $result ? "Thêm chi nhánh thành công!" : "Thêm thất bại!",
            "data" => $result
        ]);
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $tenBranch = $data['tenBranch'] ?? null;
        $diaChi = $data['diaChi'] ?? null;
        $thanhPho = $data['thanhPho'] ?? null;

        if (!$id || !$tenBranch || !$diaChi || !$thanhPho) {
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu cập nhật!"]);
            exit;
        }

        $found = $branch->find($id);
        if ($found->num_rows <= 0) {
            http_response_code(404);
            echo json_encode(["status" => false, "message" => "ID không tồn tại"]);
            exit;
        }

        $current = $found->fetch_assoc();

        if ($tenBranch != $current['tenBranch']) {

            $check = $branch->check($tenBranch);
            if ($check->num_rows > 0) {
                http_response_code(409);
                echo json_encode(["status" => false, "message" => "Tên chi nhánh đã tồn tại!"]);
                exit;
            }
        }

        $result = $branch->edit($id, $tenBranch, $diaChi, $thanhPho);

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Cập nhật thành công!" : "Cập nhật thất bại!"
        ]);
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "Thiếu id"]);
            exit;
        }

        $found = $branch->find($id);
        if ($found->num_rows <= 0) {
            echo json_encode(["status" => false, "message" => "ID không tồn tại"]);
            exit;
        }

        $result = $branch->delete($id);

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Xóa thành công!" : "Xóa thất bại!"
        ]);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Method không hỗ trợ"]);
        break;
}
?>