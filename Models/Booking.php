<?php

class Booking {

    // Lấy tất cả booking (admin)
    public function findAll() {
        global $conn;
        $sql = "SELECT b.*, a.hoTen, a.email,
                       s.ngayChieu, s.gioChieu, s.giaVe,
                       m.tenPhim, r.tenPhong
                FROM Booking b
                LEFT JOIN Account a  ON b.accountId  = a.accountId
                LEFT JOIN Schedule s ON b.scheduleId = s.scheduleId
                LEFT JOIN Movie m    ON s.movieId     = m.movieId
                LEFT JOIN Room r     ON s.roomId      = r.roomId
                ORDER BY b.ngayDat DESC";
        return $conn->query($sql);
    }

    // Lấy booking theo id
    public function findById($id) {
        global $conn;
        $sql = "SELECT b.*, a.hoTen, a.email,
                       s.ngayChieu, s.gioChieu, s.giaVe,
                       m.tenPhim, r.tenPhong, r.roomId
                FROM Booking b
                LEFT JOIN Account a  ON b.accountId  = a.accountId
                LEFT JOIN Schedule s ON b.scheduleId = s.scheduleId
                LEFT JOIN Movie m    ON s.movieId     = m.movieId
                LEFT JOIN Room r     ON s.roomId      = r.roomId
                WHERE b.bookingId = $id";
        $res = $conn->query($sql);
        return $res ? $res->fetch_assoc() : null;
    }

    // Lấy booking của 1 user
    public function findByUser($accountId) {
        global $conn;
        $sql = "SELECT b.*, s.ngayChieu, s.gioChieu, s.giaVe,
                       m.tenPhim, r.tenPhong
                FROM Booking b
                LEFT JOIN Schedule s ON b.scheduleId = s.scheduleId
                LEFT JOIN Movie m    ON s.movieId     = m.movieId
                LEFT JOIN Room r     ON s.roomId      = r.roomId
                WHERE b.accountId = $accountId
                ORDER BY b.ngayDat DESC";
        return $conn->query($sql);
    }

    // Tạo booking mới - trả về bookingId
    public function create($accountId, $scheduleId, $soLuong) {
        global $conn;
        $sql = "INSERT INTO Booking (accountId, scheduleId, soLuong, trangThai)
                VALUES ($accountId, $scheduleId, $soLuong, 'Chờ thanh toán')";
        if ($conn->query($sql)) {
            return $conn->insert_id;
        }
        return false;
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $trangThai) {
        global $conn;
        $trangThai = $conn->real_escape_string($trangThai);
        $sql = "UPDATE Booking SET trangThai = '$trangThai' WHERE bookingId = $id";
        return $conn->query($sql);
    }

    // Xóa booking (admin)
    public function delete($id) {
        global $conn;
        return $conn->query("DELETE FROM Booking WHERE bookingId = $id");
    }

    // Thống kê doanh thu theo tuần/tháng (bao gồm cả đồ ăn)
    public function doanhThu($loai = 'month', $gia_tri = null) {
        global $conn;
        if ($loai === 'month') {
            $gia_tri = $gia_tri ?? date('Y-m');
            $where = "DATE_FORMAT(b.ngayDat, '%Y-%m') = '$gia_tri'";
        } else {
            $gia_tri = $gia_tri ?? date('Y-W');
            $where = "DATE_FORMAT(b.ngayDat, '%Y-%u') = '$gia_tri'";
        }

        $sql = "SELECT
                    m.movieId,
                    m.tenPhim,
                    COUNT(DISTINCT b.bookingId)      AS soBooking,
                    SUM(bs_count.soGhe)              AS soVe,
                    SUM(bs_count.soGhe * s.giaVe)    AS doanhThuVe,
                    IFNULL(SUM(fo.tongTienFood), 0)  AS doanhThuFood,
                    SUM(bs_count.soGhe * s.giaVe) + IFNULL(SUM(fo.tongTienFood), 0) AS tongDoanh
                FROM Booking b
                JOIN Schedule s ON b.scheduleId = s.scheduleId
                JOIN Movie m    ON s.movieId = m.movieId
                JOIN (
                    SELECT bookingId, COUNT(*) AS soGhe FROM BookingSeat GROUP BY bookingId
                ) bs_count ON bs_count.bookingId = b.bookingId
                LEFT JOIN FoodOrder fo ON fo.bookingId = b.bookingId
                WHERE b.trangThai = 'Đã xác nhận' AND $where
                GROUP BY m.movieId
                ORDER BY tongDoanh DESC";
        return $conn->query($sql);
    }

    // Thống kê theo phòng
    public function thongKePhong($scheduleId) {
        global $conn;
        $sql = "SELECT
                    r.roomId, r.tenPhong, r.tongGhe,
                    COUNT(DISTINCT bs.bookingSeatId)                            AS soGheDaDat,
                    COUNT(DISTINCT b.bookingId)                                 AS soBooking,
                    COUNT(DISTINCT bs.bookingSeatId) * s.giaVe                  AS doanhThuVe,
                    IFNULL(SUM(DISTINCT fo.tongTienFood), 0)                    AS doanhThuFood,
                    COUNT(DISTINCT bs.bookingSeatId) * s.giaVe
                        + IFNULL(SUM(DISTINCT fo.tongTienFood), 0)              AS tongDoanhPhong
                FROM Schedule s
                JOIN Room r          ON s.roomId      = r.roomId
                LEFT JOIN Booking b  ON b.scheduleId  = s.scheduleId
                                    AND b.trangThai   = 'Đã xác nhận'
                LEFT JOIN BookingSeat bs ON bs.bookingId = b.bookingId
                LEFT JOIN FoodOrder fo   ON fo.bookingId = b.bookingId
                WHERE s.scheduleId = $scheduleId
                GROUP BY r.roomId, s.giaVe";
        return $conn->query($sql);
    }

    // Kiểm tra booking có ghế trùng không (xử lý race condition)
    public function checkTrungGhe($scheduleId, $seatIds) {
        global $conn;
        $ids = implode(',', array_map('intval', $seatIds));
        $sql = "SELECT bs.seatId FROM BookingSeat bs
                JOIN Booking b ON bs.bookingId = b.bookingId
                WHERE b.scheduleId = $scheduleId
                  AND b.trangThai IN ('Chờ thanh toán','Đã xác nhận')
                  AND bs.seatId IN ($ids)";
        $res = $conn->query($sql);
        $trung = [];
        while ($row = $res->fetch_assoc()) {
            $trung[] = $row['seatId'];
        }
        return $trung;
    }

    // Tạo booking với tenKhach và trangThai tùy chỉnh (dùng cho nhân viên)
  public function createWithStatus($accountId, $scheduleId, $soLuong, $trangThai = 'Chờ thanh toán') {
    global $conn;

    $sql  = "INSERT INTO Booking (accountId, scheduleId, soLuong, trangThai) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iiis", $accountId, $scheduleId, $soLuong, $trangThai);

    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}
}
