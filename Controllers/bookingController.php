<?php
include "../Config/db.php";
include "../Models/Booking.php";
include "../Models/BookingSeat.php";

header("Content-Type: application/json; charset=utf-8");

$booking     = new Booking();
$bookingSeat = new BookingSeat();
$action      = $_GET['action'] ?? null;

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents("php://input"), true) ?? [];

if ($method === 'POST' && !empty($input['_method'])) {
    $method = strtoupper($input['_method']);
}

switch ($method) {

    case 'GET':
        if ($action === 'doanhthu') {
            $loai    = $_GET['loai'] ?? 'month';
            $giaTri  = $_GET['gia_tri'] ?? null;
            $result  = $booking->doanhThu($loai, $giaTri);
            $data    = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        if ($action === 'thongke_phong') {
            $scheduleId = intval($_GET['scheduleId'] ?? 0);
            if (!$scheduleId) { echo json_encode(["status" => false, "message" => "Thieu scheduleId"]); break; }
            $result = $booking->thongKePhong($scheduleId);
            $data   = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        if ($action === 'ghe_da_dat') {
            $scheduleId = intval($_GET['scheduleId'] ?? 0);
            if (!$scheduleId) { echo json_encode(["status" => false, "message" => "Thieu scheduleId"]); break; }
            echo json_encode($bookingSeat->findBySchedule($scheduleId));
            break;
        }
        if ($action === 'ghe_booking') {
            $bookingId = intval($_GET['bookingId'] ?? 0);
            $result    = $bookingSeat->findByBooking($bookingId);
            $data      = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        if (isset($_GET['accountId'])) {
            $result = $booking->findByUser(intval($_GET['accountId']));
            $data   = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        if (isset($_GET['id'])) {
            $data = $booking->findById(intval($_GET['id']));
            echo json_encode($data ?: ["status" => false, "message" => "Khong tim thay"]);
            break;
        }
        $result = $booking->findAll();
        $data   = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        break;

    case 'POST':
        $accountId  = intval($input['accountId']  ?? 0);
        $scheduleId = intval($input['scheduleId'] ?? 0);
        $seatIds    = $input['seatIds'] ?? [];

        if (!$accountId || !$scheduleId || empty($seatIds)) {
            echo json_encode(["status" => false, "message" => "Thieu du lieu"]);
            break;
        }

        $trung = $booking->checkTrungGhe($scheduleId, $seatIds);
        if (!empty($trung)) {
            echo json_encode([
                "status"   => false,
                "message"  => "Ghe da duoc dat boi nguoi khac",
                "gheTrung" => $trung
            ]);
            break;
        }

        $tenKhach      = $input['tenKhach'] ?? '';
        $trangThaiInit = ($input['trangThai'] ?? '') === 'Da xac nhan' ? 'Da xac nhan' : 'Cho thanh toan';
        $bookingId = $booking->createWithStatus($accountId, $scheduleId, count($seatIds), $tenKhach, $trangThaiInit);
        if (!$bookingId) {
            echo json_encode(["status" => false, "message" => "Tao booking that bai"]);
            break;
        }

        if (!$bookingSeat->createBulk($bookingId, $seatIds)) {
            $booking->delete($bookingId);
            echo json_encode(["status" => false, "message" => "Luu ghe that bai"]);
            break;
        }

        echo json_encode(["status" => true, "message" => "Dat ve thanh cong", "bookingId" => $bookingId]);
        break;

    case 'PUT':
        $id        = intval($_GET['id'] ?? 0);
        $trangThai = $input['trangThai'] ?? null;

        if (!$id || !$trangThai) {
            echo json_encode(["status" => false, "message" => "Thieu du lieu"]);
            break;
        }

        $allowed = ['Cho thanh toan', 'Da xac nhan', 'Da huy'];
        if (!in_array($trangThai, $allowed)) {
            echo json_encode(["status" => false, "message" => "Trang thai khong hop le"]);
            break;
        }

        $result = $booking->updateStatus($id, $trangThai);
        echo json_encode([
            "status"  => (bool)$result,
            "message" => $result ? "Cap nhat thanh cong" : "Cap nhat that bai"
        ]);
        break;

    case 'DELETE':
        $id = intval($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(["status" => false, "message" => "Thieu id"]);
            break;
        }

        $bookingSeat->deleteByBooking($id);
        $result = $booking->delete($id);

        echo json_encode([
            "status"  => (bool)$result,
            "message" => $result ? "Xoa thanh cong" : "Xoa that bai"
        ]);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Method khong ho tro"]);
}