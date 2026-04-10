<?php
class Food {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getAll() {
        return $this->conn->query("SELECT * FROM food ORDER BY foodId DESC");
    }

    public function save($data) {
        $ten = $this->conn->real_escape_string($data['tenFood']);
        $loai = $this->conn->real_escape_string($data['loaiFood']);
        $gia = (float)$data['gia'];
        $ton = (int)$data['soLuongTon'];
        $tt = $data['trangThai'] ?? 'Còn';

        if (isset($data['foodId']) && !empty($data['foodId'])) {
            $id = (int)$data['foodId'];
            $sql = "UPDATE food SET tenFood='$ten', loaiFood='$loai', gia=$gia, soLuongTon=$ton, trangThai='$tt' WHERE foodId=$id";
        } else {
            $sql = "INSERT INTO food (tenFood, loaiFood, gia, soLuongTon, trangThai) VALUES ('$ten', '$loai', $gia, $ton, '$tt')";
        }
        return $this->conn->query($sql);
    }
    public function delete($id) {
    $id = (int)$id;
    return $this->conn->query("DELETE FROM food WHERE foodId = $id");
}
}
?>