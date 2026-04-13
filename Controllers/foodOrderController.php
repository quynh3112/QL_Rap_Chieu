<?php
include "../Config/db.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $bookingId  = intval($_GET['bookingId']  ?? 0);
        $accountId  = intval($_GET['accountId']  ?? 0);

        if ($bookingId) {
            // Chi tiết đồ ăn của 1 booking
            $res  = $conn->query("SELECT fo.*, fod.*, f.tenFood, f.loaiFood
                                  FROM FoodOrder fo
                                  JOIN FoodOrderDetail fod ON fod.foodOrderId = fo.foodOrderId
                                  JOIN Food f ON f.foodId = fod.foodId
                                  WHERE fo.bookingId = $bookingId");
            $data = [];
            while ($row = $res->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        if ($accountId) {
            $res  = $conn->query("SELECT fo.* FROM FoodOrder fo
                                  WHERE fo.accountId = $accountId
                                  ORDER BY fo.ngayMua DESC");
            $data = [];
            while ($row = $res->fetch_assoc()) $data[] = $row;
            echo json_encode($data);
            break;
        }
        // Admin: lấy tất cả
        $res  = $conn->query("SELECT fo.*, a.hoTen FROM FoodOrder fo
                              JOIN Account a ON fo.accountId = a.accountId
                              ORDER BY fo.ngayMua DESC");
        $data = [];
        while ($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        break;

    case 'POST':
        $d         = json_decode(file_get_contents("php://input"), true);
        $accountId = intval($d['accountId'] ?? 0);
        $bookingId = intval($d['bookingId'] ?? 0);
        $items     = $d['items'] ?? [];   // [{foodId, soLuong, gia}]

        if (!$accountId || empty($items)) {
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]); break;
        }

        // Tính tổng tiền đồ ăn
        $tongTien = array_reduce($items, fn($c, $i) => $c + ($i['gia'] * $i['soLuong']), 0);

        $bkVal = $bookingId ? $bookingId : 'NULL';
        $ok = $conn->query("INSERT INTO FoodOrder (accountId, bookingId, tongTienFood, trangThai)
                            VALUES ($accountId, $bkVal, $tongTien, 'Chờ xác nhận')");
        if (!$ok) {
            echo json_encode(["status" => false, "message" => "Tạo FoodOrder thất bại"]); break;
        }

        $foodOrderId = $conn->insert_id;

        // Lưu chi tiết từng món
        $vals = [];
        foreach ($items as $item) {
            $foodId   = intval($item['foodId']);
            $soLuong  = intval($item['soLuong']);
            $gia      = floatval($item['gia']);
            $vals[]   = "($foodOrderId, $foodId, $soLuong, $gia)";

            // Trừ tồn kho
            $conn->query("UPDATE Food SET soLuongTon = soLuongTon - $soLuong
                          WHERE foodId = $foodId AND soLuongTon >= $soLuong");
        }

        $conn->query("INSERT INTO FoodOrderDetail (foodOrderId,foodId,soLuong,giaLucMua)
                      VALUES " . implode(',', $vals));

        echo json_encode([
            "status"      => true,
            "message"     => "Đặt đồ ăn thành công",
            "foodOrderId" => $foodOrderId
        ]);
        break;

    case 'PUT':
        // Cập nhật trạng thái (admin)
        $d          = json_decode(file_get_contents("php://input"), true);
        $id         = intval($_GET['id'] ?? 0);
        $trangThai  = $conn->real_escape_string($d['trangThai'] ?? '');
        if (!$id || !$trangThai) {
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]); break;
        }
        $ok = $conn->query("UPDATE FoodOrder SET trangThai='$trangThai' WHERE foodOrderId=$id");
        echo json_encode(["status" => (bool)$ok, "message" => $ok ? "Cập nhật thành công" : "Thất bại"]);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Method không hỗ trợ"]);
}

