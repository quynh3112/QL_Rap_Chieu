<?php
class Food {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function getActive() {
        return $this->conn->query("SELECT * FROM Food WHERE trangThai = 'Còn hàng' ORDER BY loaiFood");
    }
    public function getAll() {
        return $this->conn->query("SELECT * FROM Food ORDER BY foodId DESC");
    }
    public function getById($id) {
        return $this->conn->query("SELECT * FROM Food WHERE foodId = $id")->fetch_assoc();  
    }
    public function save($data) {
        $ten = $data['tenFood']; $loai = $data['loaiFood']; $gia = $data['gia']; 
        $ton = $data['soLuongTon']; $tt = $data['trangThai'];
        if (isset($data['foodId'])) {
            $id = $data['foodId'];
            $sql = "UPDATE Food SET tenFood='$ten', loaiFood='$loai', gia=$gia, soLuongTon=$ton, trangThai='$tt' WHERE foodId=$id";
        }
        return $this->conn->query($sql);
    }
    public function delete($id) {
        return $this->conn->query("DELETE FROM Food WHERE foodId = $id");
    }
}
?>