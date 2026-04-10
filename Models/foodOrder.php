<?php
class FoodOrder {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($accId, $bookingId, $total) {
        $bId = $bookingId ? $bookingId : 'NULL';
        $sql = "INSERT INTO FoodOrder (accountId, bookingId, ngayMua, tongTienFood, trangThai) 
                VALUES ($accId, $bId, NOW(), $total, 'Chờ xác nhận')";
        if($this->conn->query($sql)) {
            return $this->conn->insert_id; // trả về id vừa tạo để detail dùng
        }
        return false;
    }

    public function getAll() {
        return $this->conn->query("SELECT o.*, a.hoTen 
                                   FROM FoodOrder o 
                                   JOIN Account a ON o.accountId = a.accountId 
                                   ORDER BY o.ngayMua DESC");   
    }

    public function updateStatus($id, $status) {
        return $this->conn->query("UPDATE FoodOrder SET trangThai = '$status' WHERE foodOrderId = $id");
    }
}
?>