<?php
class FoodOrder {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($accId, $bookingId, $total) {
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

        return false;
    }

    public function updateStatus($id, $status) {
        $id = (int)$id;
        $sql = "UPDATE FoodOrder SET trangThai = ? WHERE foodOrderId = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}
?>