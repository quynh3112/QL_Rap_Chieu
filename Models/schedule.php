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
                    WHEN CONCAT(s.ngayChieu, ' ', s.gioChieu) < NOW() THEN 'Đã kết thúc'
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
        $sql = "SELECT * FROM Schedule WHERE movieId=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$movieId]);
        return $stmt->get_result();
    }
    private function isDuplicate($roomId, $date, $time, $excludeId = null){
        $sql = "SELECT * FROM Schedule 
                WHERE roomId=? AND ngayChieu=? AND gioChieu=?";
        $params = [$roomId, $date, $time];
        if($excludeId){
            $sql .= " AND scheduleId != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->get_result()->num_rows > 0;
    }
    public function create($data){
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
            $data['isCancelled'],
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