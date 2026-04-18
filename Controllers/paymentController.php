<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once '../Config/db.php';
require_once '../models/payment.php';
require_once '../models/foodOrder.php';

if (ob_get_level() > 0) {
    ob_clean();
}
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = [];
}

$paymentM = new Payment($conn);
$foodOrderM = new FoodOrder($conn);

switch ($action) {
    case 'list_pending':
        checkAuth(['Admin', 'Manager', 'Employee']);

        paymentRespond(true, 'Lấy danh sách thanh toán chờ xác nhận thành công', [
            'payments' => $paymentM->getPending(),
            'currentRole' => $_SESSION['user']['role'] ?? ''
        ]);
        break;

    case 'list_all':
        checkAuth(['Admin', 'Manager', 'Employee']);

        $status = $_GET['status'] ?? null;
        if ($status === '') {
            $status = null;
        }

        paymentRespond(true, 'Lấy danh sách thanh toán thành công', [
            'payments' => $paymentM->getByStatus($status),
            'currentRole' => $_SESSION['user']['role'] ?? ''
        ]);
        break;

    case 'approve':
        checkAuth(['Admin', 'Manager']);
        handlePaymentDecision($conn, $paymentM, $foodOrderM, $input, true);
        break;

    case 'cancel':
        checkAuth(['Admin', 'Manager']);
        handlePaymentDecision($conn, $paymentM, $foodOrderM, $input, false);
        break;

    default:
        paymentRespond(false, 'Hành động không hợp lệ', null, 400);
        break;
}

exit;

function handlePaymentDecision($conn, $paymentM, $foodOrderM, $input, $isApprove) {
    $paymentId = isset($input['paymentId']) ? (int)$input['paymentId'] : 0;
    if ($paymentId <= 0) {
        paymentRespond(false, 'paymentId không hợp lệ', null, 400);
        return;
    }

    $adminId = isset($_SESSION['user']['accountId']) ? (int)$_SESSION['user']['accountId'] : 0;
    if ($adminId <= 0) {
        paymentRespond(false, 'Không xác định tài khoản admin', null, 401);
        return;
    }

    $conn->begin_transaction();

    try {
        $payment = $paymentM->getByIdForUpdate($paymentId);
        if (!$payment) {
            throw new Exception('Không tìm thấy thanh toán cần xử lý');
        }

        if ($payment['trangThai'] !== 'Chờ xác nhận') {
            throw new Exception('Thanh toán đã được xử lý trước đó');
        }

        if ((float)$payment['tongTien'] <= 0) {
            throw new Exception('Thanh toán có tổng tiền không hợp lệ.');
        }

        $paymentStatus = $isApprove ? 'Đã duyệt' : 'Đã hủy';
        $foodOrderStatus = $isApprove ? 'Đã giao' : 'Đã hủy';
        $bookingStatus = $isApprove ? 'Đã xác nhận' : 'Đã hủy';

        if (!$paymentM->updateStatus($paymentId, $paymentStatus, $adminId)) {
            throw new Exception('Không thể cập nhật trạng thái thanh toán');
        }

        if (!empty($payment['foodOrderId']) && !$foodOrderM->updateStatus((int)$payment['foodOrderId'], $foodOrderStatus)) {
            throw new Exception('Không thể đồng bộ trạng thái đơn đồ ăn');
        }

        if (!empty($payment['bookingId']) && !$paymentM->updateBookingStatus((int)$payment['bookingId'], $bookingStatus)) {
            throw new Exception('Không thể đồng bộ trạng thái booking');
        }

        $conn->commit();

        paymentRespond(true, $isApprove ? 'Duyệt thanh toán thành công' : 'Hủy thanh toán thành công', [
            'paymentId' => $paymentId,
            'paymentStatus' => $paymentStatus,
            'foodOrderId' => !empty($payment['foodOrderId']) ? (int)$payment['foodOrderId'] : null,
            'bookingId' => !empty($payment['bookingId']) ? (int)$payment['bookingId'] : null
        ]);
    } catch (Throwable $e) {
        $conn->rollback();
        paymentRespond(false, $e->getMessage(), null, 400);
    }
}

function paymentRespond($success, $message, $data = null, $httpCode = 200) {
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
?>