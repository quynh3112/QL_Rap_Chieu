<?php

class BookingSeat {

    // Lấy ghế đã đặt theo suất chiếu
    public function findBySchedule($scheduleId) {
        global $conn;
        $sql = "SELECT bs.seatId FROM BookingSeat bs
                JOIN Booking b ON bs.bookingId = b.bookingId
                WHERE b.scheduleId = $scheduleId
                  AND b.trangThai IN ('Chờ thanh toán','Đã xác nhận')";
        $res = $conn->query($sql);
        $ids = [];
        while ($row = $res->fetch_assoc()) {
            $ids[] = $row['seatId'];
        }
        return $ids;
    }

    // Lấy chi tiết ghế của 1 booking
    public function findByBooking($bookingId) {
        global $conn;
        $sql = "SELECT bs.*, se.tenGhe, se.loaiGhe, se.giaGhe
                FROM BookingSeat bs
                JOIN Seat se ON bs.seatId = se.seatId
                WHERE bs.bookingId = $bookingId";
        return $conn->query($sql);
    }

    // Lưu danh sách ghế vào booking
    public function createBulk($bookingId, $seatIds) {
        global $conn;
        $values = [];
        foreach ($seatIds as $seatId) {
            $values[] = "($bookingId, " . intval($seatId) . ")";
        }
        $sql = "INSERT INTO BookingSeat (bookingId, seatId) VALUES " . implode(',', $values);
        return $conn->query($sql);
    }

    // Xóa ghế theo booking (khi hủy)
    public function deleteByBooking($bookingId) {
        global $conn;
        return $conn->query("DELETE FROM BookingSeat WHERE bookingId = $bookingId");
    }

    // Đếm ghế đã đặt theo phòng + suất chiếu
    public function demGheDaDat($roomId, $scheduleId = null) {
        global $conn;
        $where = $scheduleId ? "AND b.scheduleId = $scheduleId" : "";
        $sql = "SELECT COUNT(bs.bookingSeatId) AS soGheDaDat
                FROM BookingSeat bs
                JOIN Booking b ON bs.bookingId = b.bookingId
                JOIN Schedule s ON b.scheduleId = s.scheduleId
                WHERE s.roomId = $roomId
                  AND b.trangThai IN ('Chờ thanh toán','Đã xác nhận')
                  $where";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        return $row['soGheDaDat'] ?? 0;
    }
}

