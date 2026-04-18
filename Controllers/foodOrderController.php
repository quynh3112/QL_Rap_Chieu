<?php
include "../Config/db.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $accountId  = intval($_GET['accountId']  ?? 0);
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
        $items     = $d['items'] ?? [];   // [{foodId, soLuong, gia}]

        if (!$accountId || empty($items)) {
            echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]); break;
        }

        $validatedItems = [];
        $tongTien = 0.0;
        foreach ($items as $item) {
            if (!is_array($item)) {
                echo json_encode(["status" => false, "message" => "Dữ liệu món ăn không hợp lệ"]); break 2;
            }

            $foodId   = intval($item['foodId'] ?? 0);
            $soLuong  = intval($item['soLuong'] ?? 0);
            $gia      = floatval($item['gia'] ?? 0);

            if ($foodId <= 0 || $soLuong <= 0 || $gia <= 0) {
                echo json_encode(["status" => false, "message" => "Mỗi món phải có foodId hợp lệ, số lượng > 0 và giá > 0"]); break 2;
            }

            $validatedItems[] = [
                'foodId' => $foodId,
                'soLuong' => $soLuong,
                'gia' => $gia
            ];
            $tongTien += ($gia * $soLuong);
        }

        if ($tongTien <= 0) {
            echo json_encode(["status" => false, "message" => "Tổng tiền không hợp lệ"]); break;
        }

        $conn->begin_transaction();

        try {
            $ok = $conn->query("INSERT INTO FoodOrder (accountId, bookingId, tongTienFood, trangThai)
                                VALUES ($accountId, NULL, $tongTien, 'Chờ xác nhận')");
            if (!$ok) {
                throw new Exception('Tạo FoodOrder thất bại');
            }

            $foodOrderId = $conn->insert_id;

            // Lưu chi tiết từng món
            $vals = [];
            foreach ($validatedItems as $item) {
                $foodId   = (int)$item['foodId'];
                $soLuong  = (int)$item['soLuong'];
                $gia      = (float)$item['gia'];
                $vals[]   = "($foodOrderId, $foodId, $soLuong, $gia)";

                // Trừ tồn kho, tồn <= 0 được xem là hết hàng
                $okStock = $conn->query("UPDATE Food
                                         SET soLuongTon = soLuongTon - $soLuong,
                                             trangThai = CASE WHEN soLuongTon - $soLuong <= 0 THEN 'Hết' ELSE 'Còn' END
                                         WHERE foodId = $foodId AND soLuongTon >= $soLuong AND soLuongTon > 0");
                if (!$okStock || $conn->affected_rows === 0) {
                    throw new Exception('Tồn kho không đủ hoặc món đã hết hàng');
                }
            }

            $detailOk = $conn->query("INSERT INTO FoodOrderDetail (foodOrderId,foodId,soLuong,giaLucMua)
                                      VALUES " . implode(',', $vals));
            if (!$detailOk) {
                throw new Exception('Lưu chi tiết FoodOrder thất bại');
            }

            $conn->commit();
            echo json_encode([
                "status"      => true,
                "message"     => "Đặt đồ ăn thành công",
                "foodOrderId" => $foodOrderId
            ]);
        } catch (Throwable $e) {
            $conn->rollback();
            echo json_encode(["status" => false, "message" => $e->getMessage()]);
        }
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

