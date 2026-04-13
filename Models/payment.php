<?php
class Payment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($bookingId, $foodOrderId, $tongTien, $phuongThuc) {
        $bookingId = $bookingId !== null ? (int)$bookingId : null;
        $foodOrderId = $foodOrderId !== null ? (int)$foodOrderId : null;
        $tongTien = (float)$tongTien;

        if ($bookingId === null && $foodOrderId === null) {
            return false;
        }

        if ($bookingId === null) {
            $sql = "INSERT INTO Payment (bookingId, foodOrderId, tongTien, phuongThuc, ngayThanhToan, trangThai) VALUES (NULL, ?, ?, ?, NOW(), 'Chờ xác nhận')";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("ids", $foodOrderId, $tongTien, $phuongThuc);
        } elseif ($foodOrderId === null) {
            $sql = "INSERT INTO Payment (bookingId, foodOrderId, tongTien, phuongThuc, ngayThanhToan, trangThai) VALUES (?, NULL, ?, ?, NOW(), 'Chờ xác nhận')";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("ids", $bookingId, $tongTien, $phuongThuc);
        } else {
            $sql = "INSERT INTO Payment (bookingId, foodOrderId, tongTien, phuongThuc, ngayThanhToan, trangThai) VALUES (?, ?, ?, ?, NOW(), 'Chờ xác nhận')";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("iids", $bookingId, $foodOrderId, $tongTien, $phuongThuc);
        }

        if (!$stmt->execute()) {
            return false;
        }

        return (int)$this->conn->insert_id;
    }

    public function getBookingInfoForCheckout($bookingId, $accountId, $forUpdate = false) {
        $bookingId = (int)$bookingId;
        $accountId = (int)$accountId;

        $sql = "SELECT b.bookingId, b.accountId, b.soLuong, b.trangThai, s.giaVe
                FROM Booking b
                JOIN Schedule s ON b.scheduleId = s.scheduleId
                WHERE b.bookingId = ? AND b.accountId = ?
                LIMIT 1";

        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("ii", $bookingId, $accountId);
        if (!$stmt->execute()) {
            return null;
        }

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        $row['ticketTotal'] = (float)$row['soLuong'] * (float)$row['giaVe'];
        return $row;
    }

    public function getPending() {
        return $this->getByStatus('Chờ xác nhận');
    }

    public function getByStatus($status = null) {
        $sql = "SELECT p.paymentId, p.bookingId, p.foodOrderId, p.tongTien, p.phuongThuc, p.ngayThanhToan,
                       p.trangThai AS paymentStatus,
                       fo.tongTienFood,
                       fo.trangThai AS foodOrderStatus,
                       fo.ngayMua,
                       b.trangThai AS bookingStatus,
                       a.hoTen AS customerName
                FROM Payment p
                LEFT JOIN FoodOrder fo ON p.foodOrderId = fo.foodOrderId
                LEFT JOIN Booking b ON p.bookingId = b.bookingId
                LEFT JOIN Account a ON fo.accountId = a.accountId";

        $rows = [];

        if ($status !== null) {
            $sql .= " WHERE p.trangThai = ?";
        }

        $sql .= " ORDER BY p.ngayThanhToan DESC";

        if ($status !== null) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return $rows;
            }
            $stmt->bind_param("s", $status);
            if (!$stmt->execute()) {
                return $rows;
            }
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        if (!$result) {
            return $rows;
        }

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getByIdForUpdate($paymentId) {
        $paymentId = (int)$paymentId;
        $sql = "SELECT paymentId, bookingId, foodOrderId, trangThai
                FROM Payment
                WHERE paymentId = ?
                LIMIT 1
                FOR UPDATE";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $paymentId);
        if (!$stmt->execute()) {
            return null;
        }

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function updateStatus($paymentId, $status, $adminId) {
        $paymentId = (int)$paymentId;
        $adminId = (int)$adminId;
        $sql = "UPDATE Payment SET trangThai = ?, adminId = ? WHERE paymentId = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sii", $status, $adminId, $paymentId);
        return $stmt->execute();
    }

    public function updateBookingStatus($bookingId, $status) {
        $bookingId = (int)$bookingId;
        $sql = "UPDATE Booking SET trangThai = ? WHERE bookingId = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $status, $bookingId);
        return $stmt->execute();
    }
}
?>