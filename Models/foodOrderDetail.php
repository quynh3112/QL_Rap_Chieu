<?php
class FoodOrderDetail {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($orderId, $foodId, $qty, $price) {
        $sql = "INSERT INTO FoodOrderDetail (foodOrderId, foodId, soLuong, giaLucMua) 
                VALUES ($orderId, $foodId, $qty, $price)";
        return $this->conn->query($sql);
    }
}
?>