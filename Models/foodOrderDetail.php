<?php
class FoodOrderDetail {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($orderId, $foodId, $qty, $price) {
<<<<<<< HEAD
        $sql = "INSERT INTO FoodOrderDetail (foodOrderId, foodId, soLuong, giaLucMua) 
                VALUES ($orderId, $foodId, $qty, $price)";
        return $this->conn->query($sql);
=======
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
>>>>>>> origin/dev-food
    }
}
?>