<?php
include "../models/WorkSchedule.php";
header("Content-Type: application/json");

$work = new WorkSchedule();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case "GET":
        


        $id = $_GET['workId'] ?? null;
        $accountId = $_GET['accountId'] ?? null;
        $branchId = $_GET['branchId'] ?? null;
        $ngayLamViec = $_GET['ngayLamViec'] ?? null;
        if ($ngayLamViec && $branchId) {

        $result = $work->isDuplicate($branchId, $ngayLamViec);

        if ($result->num_rows <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "Không có nhân viên làm ngày này"
            ]);
            exit;
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        exit;
    }

        if ($id) {
            $result = $work->getById($id);

            if ($result->num_rows <= 0) {
                echo json_encode(["status" => false, "message" => "Không tìm thấy"]);
                exit;
            }

            echo json_encode($result->fetch_assoc());
            exit;
        }

        if ($accountId) {
            $result = $work->getByAccount($accountId);

            if ($result->num_rows <= 0) {
                echo json_encode(["status" => false, "message" => "Không có lịch"]);
                exit;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode($data);
            exit;
        }

        if ($branchId) {
            $result = $work->getByBranch($branchId);

            if ($result->num_rows <= 0) {
                echo json_encode(["status" => false, "message" => "Không có lịch"]);
                exit;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            echo json_encode($data);
            exit;
        }

        $result = $work->getAll();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;

    case "POST":

        $data = json_decode(file_get_contents("php://input"), true);

        $accountId = $data['accountId'] ?? null;
        $branchId = $data['branchId'] ?? null;
        $ngayLamViec = $data['ngayLamViec'] ?? null;
        $caLam = $data['caLam'] ?? null;
        $gioBatDau = $data['gioBatDau'] ?? null;
        $gioKetThuc = $data['gioKetThuc'] ?? null;

        if (!$accountId || !$branchId || !$ngayLamViec || !$caLam || !$gioBatDau || !$gioKetThuc) {
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]);
            exit;
        }
        if($work->isConflictSchedule($accountId,$branchId,$ngayLamViec,$gioBatDau,$gioKetThuc)->num_rows > 0){
            echo json_encode(["status" => false, "message" => "Lịch làm việc bị trùng"]);
            exit;
        }

        $result = $work->createWorkSchedule($accountId, $branchId, $ngayLamViec, $caLam, $gioBatDau, $gioKetThuc);

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Thêm thành công" : "Thêm thất bại"
        ]);
        break;

    case "PUT":

        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['workId'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "Thiếu workId"]);
            exit;
        }

        $found = $work->getById($id);
        if ($found->num_rows <= 0) {
            echo json_encode(["status" => false, "message" => "ID không tồn tại"]);
            exit;
        }

        $result = $work->updateWorkSchedule(
            $id,
            $data['accountId'],
            $data['branchId'],
            $data['ngayLamViec'],
            $data['caLam'],
            $data['gioBatDau'],
            $data['gioKetThuc']
        );

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Cập nhật thành công" : "Cập nhật thất bại"
        ]);
        break;

    case "DELETE":

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['workId'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "Thiếu workId"]);
            exit;
        }

        $found = $work->getById($id);
        if ($found->num_rows <= 0) {
            echo json_encode(["status" => false, "message" => "ID không tồn tại"]);
            exit;
        }

        $result = $work->deleteWorkSchedule($id);

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Xóa thành công" : "Xóa thất bại"
        ]);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Method không hỗ trợ"]);
        break;
}
?>