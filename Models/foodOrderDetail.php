<?php
class FoodOrderDetail {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($orderId, $foodId, $qty, $price) {
        $orderId = (int)$orderId;
        $foodId = (int)$foodId;
        $qty = (int)$qty;
        $price = (float)$price;

        $sql = "INSERT INTO FoodOrderDetail (foodOrderId, foodId, soLuong, giaLucMua)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iiid", $orderId, $foodId, $qty, $price);
        return $stmt->execute();
    }
}
?>