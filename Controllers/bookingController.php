<?php
include "../Config/db.php";
include "../Models/Booking.php";
include "../Models/BookingSeat.php";

header("Content-Type: application/json");

$booking     = new Booking();
$bookingSeat = new BookingSeat();
$method      = $_SERVER['REQUEST_METHOD'];
$action      = $_GET['action'] ?? null;

switch ($method) {

    // ── GET ──────────────────────────────────────────────────
    case 'GET':
        // Thống kê doanh thu
        if ($action === 'doanhthu') {
            $loai    = $_GET['loai'] ?? 'month';
            $giaTri  = $_GET['gia_tri'] ?? null;
            $result  = $booking->doanhThu($loai, $giaTri);
            $data    = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        // Thống kê phòng
        if ($action === 'thongke_phong') {
            $scheduleId = intval($_GET['scheduleId'] ?? 0);
            if (!$scheduleId) { echo json_encode(["status" => false, "message" => "Thiếu scheduleId"]); break; }
            $result = $booking->thongKePhong($scheduleId);
            $data   = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        // Ghế đã đặt theo suất chiếu
        if ($action === 'ghe_da_dat') {
            $scheduleId = intval($_GET['scheduleId'] ?? 0);
            if (!$scheduleId) { echo json_encode(["status" => false, "message" => "Thiếu scheduleId"]); break; }
            echo json_encode($bookingSeat->findBySchedule($scheduleId));
            break;
        }
        // Chi tiết ghế của booking
        if ($action === 'ghe_booking') {
            $bookingId = intval($_GET['bookingId'] ?? 0);
            $result    = $bookingSeat->findByBooking($bookingId);
            $data      = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        //Phanh thêm: API lấy lịch sử đặt vé (bao gồm cả đã hủy) của user
        if ($action === 'history' && isset($_GET['accountId'])) {
            $result = $booking->findHistoryByUser(intval($_GET['accountId']));
            $data   = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
            break; 
        }
        // Booking của user
        if (isset($_GET['accountId'])) {
            $result = $booking->findByUser(intval($_GET['accountId']));
            $data   = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        // Chi tiết 1 booking
        if (isset($_GET['id'])) {
            $data = $booking->findById(intval($_GET['id']));
            echo json_encode($data ?: ["status" => false, "message" => "Không tìm thấy"]);
            break;
        }
        // Tất cả (admin)
        $result = $booking->findAll();
        $data   = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        break;

    // ── POST — tạo booking + lưu ghế ─────────────────────────
    case 'POST':
        $input      = json_decode(file_get_contents("php://input"), true);
        $accountId  = intval($input['accountId']  ?? 0);
        $scheduleId = intval($input['scheduleId'] ?? 0);
        $seatIds    = $input['seatIds'] ?? [];

        if (!$accountId || !$scheduleId || empty($seatIds)) {
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]);
            break;
        }

        // Kiểm tra ghế trùng (race condition)
        $trung = $booking->checkTrungGhe($scheduleId, $seatIds);
        if (!empty($trung)) {
            echo json_encode([
                "status"    => false,
                "message"   => "Ghế đã được đặt bởi người khác",
                "gheTrung"  => $trung
            ]);
            break;
        }

        // Tạo booking
        $tenKhach      = $input['tenKhach'] ?? '';
        $trangThaiInit = in_array($input['trangThai'] ?? '', ['Đã xác nhận']) ? 'Đã xác nhận' : 'Chờ thanh toán';
        $bookingId = $booking->createWithStatus($accountId, $scheduleId, count($seatIds), $tenKhach, $trangThaiInit);
        if (!$bookingId) {
            echo json_encode(["status" => false, "message" => "Tạo booking thất bại"]);
            break;
        }

        // Lưu ghế
        if (!$bookingSeat->createBulk($bookingId, $seatIds)) {
            // Rollback booking nếu lưu ghế thất bại
            $booking->delete($bookingId);
            echo json_encode(["status" => false, "message" => "Lưu ghế thất bại"]);
            break;
        }

        echo json_encode([
            "status"    => true,
            "message"   => "Đặt vé thành công",
            "bookingId" => $bookingId
        ]);
        break;

    // ── PUT — cập nhật trạng thái ────────────────────────────
    case 'PUT':
        $input     = json_decode(file_get_contents("php://input"), true);
        $id        = intval($_GET['id'] ?? 0);
        $trangThai = $input['trangThai'] ?? null;

        if (!$id || !$trangThai) {
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]);
            break;
        }

        $allowed = ['Chờ thanh toán', 'Đã xác nhận', 'Đã hủy'];
        if (!in_array($trangThai, $allowed)) {
            echo json_encode(["status" => false, "message" => "Trạng thái không hợp lệ"]);
            break;
        }

        $result = $booking->updateStatus($id, $trangThai);
        echo json_encode([
            "status"  => (bool)$result,
            "message" => $result ? "Cập nhật thành công" : "Cập nhật thất bại"
        ]);
        break;

    // ── DELETE ───────────────────────────────────────────────
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $id    = intval($input['id'] ?? 0);

        if (!$id) { echo json_encode(["status" => false, "message" => "Thiếu id"]); break; }

        $bookingSeat->deleteByBooking($id);
        $result = $booking->delete($id);

        echo json_encode([
            "status"  => (bool)$result,
            "message" => $result ? "Xóa thành công" : "Xóa thất bại"
        ]);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Method không hỗ trợ"]);
}

