<?php
class FoodOrder {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($accId, $bookingId, $total) {
<<<<<<< HEAD
        $bId = $bookingId ? $bookingId : 'NULL';
        $sql = "INSERT INTO FoodOrder (accountId, bookingId, ngayMua, tongTienFood, trangThai) 
                VALUES ($accId, $bId, NOW(), $total, 'Chờ xác nhận')";
        if($this->conn->query($sql)) {
            return $this->conn->insert_id; // trả về id vừa tạo để detail dùng
        }
=======
        $accId = (int)$accId;
        $total = (float)$total;

        if ($bookingId === null) {
            $sql = "INSERT INTO FoodOrder (accountId, bookingId, ngayMua, tongTienFood, trangThai)
                    VALUES (?, NULL, NOW(), ?, 'Chờ xác nhận')";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("id", $accId, $total);
        } else {
            $bookingId = (int)$bookingId;
            $sql = "INSERT INTO FoodOrder (accountId, bookingId, ngayMua, tongTienFood, trangThai)
                    VALUES (?, ?, NOW(), ?, 'Chờ xác nhận')";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("iid", $accId, $bookingId, $total);
        }

        if ($stmt->execute()) {
            return (int)$this->conn->insert_id;
        }

>>>>>>> origin/dev-food
        return false;
    }

    public function getAll() {
        return $this->conn->query("SELECT o.*, a.hoTen 
                                   FROM FoodOrder o 
                                   JOIN Account a ON o.accountId = a.accountId 
                                   ORDER BY o.ngayMua DESC");   
    }

    public function updateStatus($id, $status) {
<<<<<<< HEAD
        return $this->conn->query("UPDATE FoodOrder SET trangThai = '$status' WHERE foodOrderId = $id");
=======
        $id = (int)$id;
        $sql = "UPDATE FoodOrder SET trangThai = ? WHERE foodOrderId = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
>>>>>>> origin/dev-food
    }
}
?>