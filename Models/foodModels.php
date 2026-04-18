<?php
class Food {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getAll() {
        return $this->conn->query("SELECT * FROM food ORDER BY foodId DESC");
    }

    public function save($data) {
        $ten = trim((string)($data['tenFood'] ?? ''));
        $loai = trim((string)($data['loaiFood'] ?? ''));
        $gia = isset($data['gia']) ? (float)$data['gia'] : -1;
        $ton = isset($data['soLuongTon']) ? (int)$data['soLuongTon'] : -1;

        if ($ten === '' || $loai === '' || $gia <= 0 || $ton < 0) {
            return false;
        }

        $tt = $ton > 0 ? 'Còn' : 'Hết';

        if (isset($data['foodId']) && !empty($data['foodId'])) {
            $id = (int)$data['foodId'];
            if ($id <= 0) {
                return false;
            }

            $sql = "UPDATE food
                    SET tenFood = ?, loaiFood = ?, gia = ?, soLuongTon = ?, trangThai = ?
                    WHERE foodId = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }

            $stmt->bind_param("ssdisi", $ten, $loai, $gia, $ton, $tt, $id);
            return $stmt->execute();
        }

        $sql = "INSERT INTO food (tenFood, loaiFood, gia, soLuongTon, trangThai)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssdis", $ten, $loai, $gia, $ton, $tt);
        return $stmt->execute();
    }
    public function delete($id) {
        $id = (int)$id;
        return $this->conn->query("DELETE FROM food WHERE foodId = $id");
    }
}
?>