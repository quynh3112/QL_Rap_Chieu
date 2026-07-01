<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php'; 

require_once '../Config/db.php';
require_once '../models/foodModels.php';
require_once '../models/foodOrder.php';
require_once '../models/foodOrderDetail.php';
<<<<<<< HEAD

ob_clean();
=======
require_once '../models/payment.php';

if (ob_get_level() > 0) {
    ob_clean();
}
>>>>>>> origin/dev-food
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
<<<<<<< HEAD
$foodModel = new Food($conn);

=======
if (!is_array($input)) {
    $input = [];
}

$foodModel = new Food($conn);

function foodRespond($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);

    $response = [
        'success' => (bool)$success,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response);
}

function normalizeTextForCompare($value) {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    $lower = function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $lower);
    if ($ascii === false || $ascii === '') {
        $ascii = $lower;
    }

    $ascii = str_replace('đ', 'd', strtolower($ascii));
    return preg_replace('/[^a-z0-9]+/', '', $ascii);
}

function normalizePaymentMethod($rawMethod) {
    $compact = normalizeTextForCompare($rawMethod);
    if ($compact === '') {
        return 'Tiền mặt';
    }

    $cashAliases = ['tienmat', 'cash', 'tm', 'tinmt', 'tienmt', 'tienma'];
    if (
        in_array($compact, $cashAliases, true) ||
        str_contains($compact, 'cash') ||
        (str_contains($compact, 'tien') && str_contains($compact, 'mat'))
    ) {
        return 'Tiền mặt';
    }

    return null;
}

function parseCheckoutPayload($input) {
    $items = $input['items'] ?? [];
    if (!is_array($items) || count($items) === 0) {
        return [
            'success' => false,
            'message' => 'Giỏ hàng trống hoặc dữ liệu món không hợp lệ.'
        ];
    }

    // Food checkout currently supports only direct cash payment.
    $phuongThuc = 'Tiền mặt';

    $bookingId = null;
    if (array_key_exists('bookingId', $input) && $input['bookingId'] !== null && $input['bookingId'] !== '') {
        if (!is_numeric($input['bookingId']) || (int)$input['bookingId'] <= 0) {
            return [
                'success' => false,
                'message' => 'Mã booking không hợp lệ.'
            ];
        }
        $bookingId = (int)$input['bookingId'];
    }

    $normalizedItems = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            return [
                'success' => false,
                'message' => 'Dữ liệu món gửi lên không hợp lệ.'
            ];
        }

        $foodId = isset($item['foodId']) ? (int)$item['foodId'] : 0;
        $qty = isset($item['soLuong']) ? (int)$item['soLuong'] : 0;
        if ($foodId <= 0 || $qty <= 0) {
            return [
                'success' => false,
                'message' => 'Mỗi món phải có foodId hợp lệ và số lượng lớn hơn 0.'
            ];
        }

        if (!isset($normalizedItems[$foodId])) {
            $normalizedItems[$foodId] = 0;
        }
        $normalizedItems[$foodId] += $qty;
    }

    if (count($normalizedItems) === 0) {
        return [
            'success' => false,
            'message' => 'Giỏ hàng trống hoặc dữ liệu món không hợp lệ.'
        ];
    }

    return [
        'success' => true,
        'bookingId' => $bookingId,
        'phuongThuc' => $phuongThuc,
        'normalizedItems' => $normalizedItems
    ];
}

>>>>>>> origin/dev-food
switch ($action) {
    case 'list_all':
        //ai cũng xem được
        checkAuth(['Admin', 'Manager', 'Employee', 'Customer']);
        
        $res = $foodModel->getAll();
        $data = [];
        if ($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
<<<<<<< HEAD
        echo json_encode(['success' => true, 'data' => $data]);
=======
        foodRespond(true, 'Lấy danh sách món thành công', $data);
>>>>>>> origin/dev-food
        break;

    case 'list_orders':
        //nhân viên, quản lý mới xem được
        checkAuth(['Admin', 'Manager', 'Employee']);
        
        $orderM = new FoodOrder($conn);
        $res = $orderM->getAll();
        $data = [];
        if ($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
<<<<<<< HEAD
        echo json_encode(['success' => true, 'data' => $data]);
=======
        foodRespond(true, 'Lấy danh sách đơn đồ ăn thành công', $data);
>>>>>>> origin/dev-food
        break;

    case 'save':
        // admin or man mới sửa được
        checkAuth(['Admin', 'Manager']);
<<<<<<< HEAD
        
        if ($foodModel->save($input)) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'message' => $conn->error]);
=======

        if (empty($input)) {
            foodRespond(false, 'Dữ liệu gửi lên không hợp lệ', null, 400);
            break;
        }
        
        if ($foodModel->save($input)) {
            foodRespond(true, 'Lưu món thành công');
        } else {
            foodRespond(false, 'Không thể lưu món', null, 400);
        }
>>>>>>> origin/dev-food
        break;

    case 'delete':
        // admin xoá
        checkAuth(['Admin']);
        
<<<<<<< HEAD
        $id = $_GET['foodId'] ?? 0;
        if ($foodModel->delete($id)) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'message' => 'Lỗi xoá']);
=======
        $id = isset($_GET['foodId']) ? (int)$_GET['foodId'] : 0;
        if ($id <= 0) {
            foodRespond(false, 'foodId không hợp lệ', null, 400);
            break;
        }

        if ($foodModel->delete($id)) {
            foodRespond(true, 'Xóa món thành công');
        } else {
            foodRespond(false, 'Không thể xóa món', null, 400);
        }
        break;

    case 'checkout_preview':
        checkAuth(['Customer', 'Admin', 'Manager', 'Employee']);

        $accId = isset($_SESSION['user']['accountId']) ? (int)$_SESSION['user']['accountId'] : 0;
        if ($accId <= 0) {
            foodRespond(false, 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.', null, 401);
            break;
        }

        $checkoutData = parseCheckoutPayload($input);
        if (!$checkoutData['success']) {
            foodRespond(false, $checkoutData['message'], null, 400);
            break;
        }

        $bookingId = $checkoutData['bookingId'];
        $phuongThuc = $checkoutData['phuongThuc'];
        $normalizedItems = $checkoutData['normalizedItems'];

        try {
            $paymentM = new Payment($conn);

            $ticketTotal = 0.0;
            $bookingSummary = null;
            if ($bookingId !== null) {
                $bookingInfo = $paymentM->getBookingInfoForCheckout($bookingId, $accId, false);
                if (!$bookingInfo) {
                    throw new Exception('Không tìm thấy booking hợp lệ của tài khoản hiện tại.');
                }

                if ($bookingInfo['trangThai'] === 'Đã hủy') {
                    throw new Exception('Booking đã bị hủy, không thể thanh toán kèm đồ ăn.');
                }

                $ticketTotal = (float)$bookingInfo['ticketTotal'];
                $bookingSummary = [
                    'bookingId' => (int)$bookingInfo['bookingId'],
                    'soLuongVe' => (int)$bookingInfo['soLuong'],
                    'giaVe' => (float)$bookingInfo['giaVe'],
                    'tongTienVe' => $ticketTotal,
                    'trangThai' => $bookingInfo['trangThai']
                ];
            }

            $selectFoodSql = "SELECT foodId, tenFood, gia, soLuongTon, trangThai
                              FROM Food
                              WHERE foodId = ?
                              LIMIT 1";
            $selectFoodStmt = $conn->prepare($selectFoodSql);
            if (!$selectFoodStmt) {
                throw new Exception('Không thể kiểm tra dữ liệu món ăn lúc này.');
            }

            $previewItems = [];
            $foodTotal = 0.0;

            foreach ($normalizedItems as $foodId => $qty) {
                $selectFoodStmt->bind_param("i", $foodId);
                if (!$selectFoodStmt->execute()) {
                    throw new Exception('Không thể đọc thông tin món ăn.');
                }

                $foodResult = $selectFoodStmt->get_result();
                if (!$foodResult || $foodResult->num_rows === 0) {
                    throw new Exception('Có món ăn không còn tồn tại trong hệ thống.');
                }

                $foodRow = $foodResult->fetch_assoc();
                if ((int)$foodRow['soLuongTon'] < $qty) {
                    throw new Exception('Số lượng tồn không đủ cho món: ' . $foodRow['tenFood']);
                }

                $gia = (float)$foodRow['gia'];
                $thanhTien = $gia * (int)$qty;
                $foodTotal += $thanhTien;

                $previewItems[] = [
                    'foodId' => (int)$foodRow['foodId'],
                    'tenFood' => $foodRow['tenFood'],
                    'gia' => $gia,
                    'soLuong' => (int)$qty,
                    'thanhTien' => $thanhTien,
                    'soLuongTon' => (int)$foodRow['soLuongTon'],
                    'trangThai' => $foodRow['trangThai']
                ];
            }

            $paymentTotal = $foodTotal + $ticketTotal;

            foodRespond(true, 'Lấy thông tin thanh toán thành công.', [
                'items' => $previewItems,
                'booking' => $bookingSummary,
                'tongTienFood' => $foodTotal,
                'tongTienVe' => $ticketTotal,
                'tongTienThanhToan' => $paymentTotal,
                'phuongThuc' => $phuongThuc
            ]);
        } catch (Throwable $e) {
            foodRespond(false, $e->getMessage(), null, 400);
        }
>>>>>>> origin/dev-food
        break;

    case 'place_order':
        // tất cả role có thể đặt
        checkAuth(['Customer', 'Admin', 'Manager', 'Employee']);
<<<<<<< HEAD
        
        $conn->begin_transaction();
        try {
            $orderM = new FoodOrder($conn);
            $detailM = new FoodOrderDetail($conn);
            
            // lấy id từ session
            $accId = $_SESSION['user']['accountId'] ?? 1; 
            
            $total = 0;
            foreach($input['items'] as $i) $total += $i['price'] * $i['soLuong'];
            
            $orderId = $orderM->create($accId, $input['bookingId'] ?? null, $total);
            foreach($input['items'] as $i) {
                $detailM->create($orderId, $i['foodId'], $i['soLuong'], $i['price']);
                $conn->query("UPDATE Food SET soLuongTon = soLuongTon - {$i['soLuong']} WHERE foodId = {$i['foodId']}");
            }
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
=======

        $accId = isset($_SESSION['user']['accountId']) ? (int)$_SESSION['user']['accountId'] : 0;
        if ($accId <= 0) {
            foodRespond(false, 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.', null, 401);
            break;
        }

        $checkoutData = parseCheckoutPayload($input);
        if (!$checkoutData['success']) {
            foodRespond(false, $checkoutData['message'], null, 400);
            break;
        }

        $bookingId = $checkoutData['bookingId'];
        $phuongThuc = $checkoutData['phuongThuc'];
        $normalizedItems = $checkoutData['normalizedItems'];
        
        $conn->begin_transaction();

        try {
            $orderM = new FoodOrder($conn);
            $detailM = new FoodOrderDetail($conn);
            $paymentM = new Payment($conn);

            $ticketTotal = 0.0;
            if ($bookingId !== null) {
                $bookingInfo = $paymentM->getBookingInfoForCheckout($bookingId, $accId, true);
                if (!$bookingInfo) {
                    throw new Exception('Không tìm thấy booking hợp lệ của tài khoản hiện tại.');
                }
                if ($bookingInfo['trangThai'] === 'Đã hủy') {
                    throw new Exception('Booking đã bị hủy, không thể thanh toán kèm đồ ăn.');
                }
                $ticketTotal = (float)$bookingInfo['ticketTotal'];
            }
            
            $selectFoodSql = "SELECT foodId, tenFood, gia, soLuongTon, trangThai
                              FROM Food
                              WHERE foodId = ?
                              LIMIT 1
                              FOR UPDATE";
            $selectFoodStmt = $conn->prepare($selectFoodSql);
            if (!$selectFoodStmt) {
                throw new Exception('Không thể kiểm tra dữ liệu món ăn lúc này.');
            }

            $updateStockSql = "UPDATE Food
                               SET soLuongTon = soLuongTon - ?,
                                   trangThai = CASE WHEN soLuongTon - ? <= 0 THEN 'Hết' ELSE 'Còn' END
                               WHERE foodId = ? AND soLuongTon >= ?";
            $updateStockStmt = $conn->prepare($updateStockSql);
            if (!$updateStockStmt) {
                throw new Exception('Không thể cập nhật tồn kho lúc này.');
            }
            
            $foodTotal = 0.0;
            $detailRows = [];

            foreach ($normalizedItems as $foodId => $qty) {
                $selectFoodStmt->bind_param("i", $foodId);
                if (!$selectFoodStmt->execute()) {
                    throw new Exception('Không thể đọc thông tin món ăn.');
                }

                $foodResult = $selectFoodStmt->get_result();
                if (!$foodResult || $foodResult->num_rows === 0) {
                    throw new Exception('Có món ăn không còn tồn tại trong hệ thống.');
                }

                $foodRow = $foodResult->fetch_assoc();
                if ((int)$foodRow['soLuongTon'] < $qty) {
                    throw new Exception('Số lượng tồn không đủ cho món: ' . $foodRow['tenFood']);
                }

                $price = (float)$foodRow['gia'];
                $foodTotal += $price * $qty;
                $detailRows[] = [
                    'foodId' => (int)$foodId,
                    'soLuong' => (int)$qty,
                    'gia' => $price
                ];
            }
            
            $orderId = $orderM->create($accId, $bookingId, $foodTotal);
            if (!$orderId) {
                throw new Exception('Không thể tạo đơn đồ ăn. Vui lòng thử lại.');
            }

            foreach ($detailRows as $detail) {
                if (!$detailM->create($orderId, $detail['foodId'], $detail['soLuong'], $detail['gia'])) {
                    throw new Exception('Không thể lưu chi tiết đơn đồ ăn.');
                }

                $qty = (int)$detail['soLuong'];
                $foodId = (int)$detail['foodId'];
                $updateStockStmt->bind_param("iiii", $qty, $qty, $foodId, $qty);
                if (!$updateStockStmt->execute()) {
                    throw new Exception('Không thể cập nhật tồn kho.');
                }
                if ($updateStockStmt->affected_rows === 0) {
                    throw new Exception('Tồn kho vừa thay đổi, vui lòng kiểm tra lại giỏ hàng.');
                }
            }

            $paymentTotal = $foodTotal + $ticketTotal;
            $paymentId = $paymentM->create($bookingId, $orderId, $paymentTotal, $phuongThuc);
            if (!$paymentId) {
                throw new Exception('Không thể tạo thanh toán cho đơn đồ ăn.');
            }

            $conn->commit();

            foodRespond(true, 'Đặt món thành công. Đơn hàng đang chờ xác nhận.', [
                'foodOrderId' => (int)$orderId,
                'paymentId' => (int)$paymentId,
                'bookingId' => $bookingId,
                'tongTienFood' => $foodTotal,
                'tongTienVe' => $ticketTotal,
                'tongTienThanhToan' => $paymentTotal,
                'phuongThuc' => $phuongThuc,
                'trangThaiThanhToan' => 'Chờ xác nhận'
            ]);
        } catch (Throwable $e) {
            $conn->rollback();

            foodRespond(false, $e->getMessage(), null, 400);
>>>>>>> origin/dev-food
        }
        break;

    default:
<<<<<<< HEAD
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
=======
        foodRespond(false, 'Hành động không hợp lệ', null, 400);
>>>>>>> origin/dev-food
        break;
}
exit;