<?php
class Schedule {
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function getAll(){
    $sql = "SELECT s.*, m.tenPhim, r.tenPhong,
            CASE 
    WHEN s.isCancelled = 1 THEN 'Đã hủy'

    WHEN NOW() BETWEEN 
        STR_TO_DATE(CONCAT(s.ngayChieu, ' ', s.gioChieu), '%Y-%m-%d %H:%i:%s')
        AND DATE_ADD(
            STR_TO_DATE(CONCAT(s.ngayChieu, ' ', s.gioChieu), '%Y-%m-%d %H:%i:%s'),
            INTERVAL m.thoiLuong MINUTE
        )
    THEN 'Đang chiếu'

    WHEN NOW() > DATE_ADD(
        STR_TO_DATE(CONCAT(s.ngayChieu, ' ', s.gioChieu), '%Y-%m-%d %H:%i:%s'),
        INTERVAL m.thoiLuong MINUTE
    )
    THEN 'Đã kết thúc'

    ELSE 'Sắp diễn ra'
END AS trangThai

            FROM Schedule s
            JOIN Movie m ON s.movieId = m.movieId
            JOIN Room r ON s.roomId = r.roomId
            ORDER BY s.ngayChieu, s.gioChieu";

    return $this->conn->query($sql);
}
    public function getById($id){
        $sql = "SELECT * FROM Schedule WHERE scheduleId=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->get_result()->fetch_assoc();
    }
    public function getByMovie($movieId){
    $sql = "SELECT s.*, m.tenPhim, r.tenPhong,
            CASE 
                WHEN s.isCancelled = 1 THEN 'Đã hủy'

                WHEN NOW() BETWEEN 
                    TIMESTAMP(s.ngayChieu, s.gioChieu) 
                    AND TIMESTAMP(s.ngayChieu, s.gioChieu) + INTERVAL m.thoiLuong MINUTE
                THEN 'Đang chiếu'

                WHEN NOW() > TIMESTAMP(s.ngayChieu, s.gioChieu) + INTERVAL m.thoiLuong MINUTE
                THEN 'Đã kết thúc'

                ELSE 'Sắp diễn ra'
            END AS trangThai

            FROM Schedule s
            JOIN Movie m ON s.movieId = m.movieId
            JOIN Room r ON s.roomId = r.roomId
            WHERE s.movieId=?
            ORDER BY s.ngayChieu, s.gioChieu";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$movieId]);
    return $stmt->get_result();
}
    private function isDuplicate($roomId, $date, $time, $excludeId = null){

    $start = "$date $time";
    $sql = "SELECT * FROM Schedule s
            JOIN Movie m ON s.movieId = m.movieId
            WHERE s.roomId=? 
            AND (
                TIMESTAMP(s.ngayChieu, s.gioChieu) < DATE_ADD(?, INTERVAL m.thoiLuong MINUTE)
                AND DATE_ADD(TIMESTAMP(s.ngayChieu, s.gioChieu), INTERVAL m.thoiLuong MINUTE) > ?
            )";

    $params = [$roomId, $start, $start];

    if($excludeId){
        $sql .= " AND s.scheduleId != ?";
        $params[] = $excludeId;
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);

    return $stmt->get_result()->num_rows > 0;
}
    public function create($data){
    if($data['giaVe'] <= 0){
    return "invalid_price";
}
        if($this->isDuplicate($data['roomId'], $data['ngayChieu'], $data['gioChieu'])){
return "duplicate";
        }
        $sql = "INSERT INTO Schedule (movieId, roomId, ngayChieu, gioChieu, giaVe)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['movieId'],
            $data['roomId'],
            $data['ngayChieu'],
            $data['gioChieu'],
            $data['giaVe']
        ]);
    }
    public function update($id, $data){
if(isset($data['giaVe']) && $data['giaVe'] <= 0){
    return "invalid_price";
}
    // chỉ update trạng thái (hủy)
    if(isset($data['isCancelled']) && count($data) == 1){
        $sql = "UPDATE Schedule SET isCancelled=? WHERE scheduleId=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$data['isCancelled'], $id]);
    }

    // update bình thường
    if($this->isDuplicate($data['roomId'], $data['ngayChieu'], $data['gioChieu'], $id)){
        return "duplicate";
    }

    $sql = "UPDATE Schedule SET 
            movieId=?, roomId=?, ngayChieu=?, gioChieu=?, giaVe=?, isCancelled=?
            WHERE scheduleId=?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        $data['movieId'],
        $data['roomId'],
        $data['ngayChieu'],
        $data['gioChieu'],
        $data['giaVe'],
        $data['isCancelled'] ?? 0,
        $id
    ]);
}
    public function delete($id){
        // check đã có booking chưa
        $check = "SELECT * FROM Booking WHERE scheduleId=?";
        $stmt = $this->conn->prepare($check);
        $stmt->execute([$id]);
        if($stmt->get_result()->num_rows > 0){
            return "has_booking";
        }
        $sql = "DELETE FROM Schedule WHERE scheduleId=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}