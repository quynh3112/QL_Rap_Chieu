<?php
include "../config/db.php";

class Branch {

    public function findAll() {
        global $conn;
        $sql = "SELECT * FROM branch";
        return $conn->query($sql);
    }

    public function find($id) {
        global $conn;
        $sql = "SELECT * FROM branch WHERE branchId = $id";
        return $conn->query($sql);
    }

    public function check($name) {
        global $conn;
        $sql = "SELECT 1 FROM branch WHERE tenBranch = '$name'";
        return $conn->query($sql);
    }

    public function create($tenBranch, $diaChi, $thanhPho) {
        global $conn;

        $sql = "INSERT INTO branch (tenBranch, diaChi, thanhPho)
                VALUES ('$tenBranch', '$diaChi', '$thanhPho')";

        if ($conn->query($sql)) {
            $id = $conn->insert_id;

            $res = $conn->query("SELECT * FROM branch WHERE branchId = $id");
            return $res->fetch_assoc();
        }

        return false;
    }

    public function edit($id, $tenBranch, $diaChi, $thanhPho) {
        global $conn;

        $sql = "UPDATE branch 
                SET tenBranch = '$tenBranch',
                    diaChi = '$diaChi',
                    thanhPho = '$thanhPho'
                WHERE branchId = $id";

        return $conn->query($sql);
    }

    public function delete($id) {
        global $conn;
        $sql = "DELETE FROM branch WHERE branchId = $id";
        return $conn->query($sql);
    }
       public function city(){
        global $conn;
        return $conn->query("SELECT DISTINCT thanhPho from branch ");
        
    }
    public function brandBycity($city){
        global $conn;
        return $conn->query("SELECT branchId,tenBranch FROM branch WHERE thanhPho='$city'");
    }
 public function movieByBranch($branch_id){
    global $conn;

  $sql = "SELECT DISTINCT m.movieId, m.tenPhim, m.thoiLuong, m.img
        FROM movie m
        JOIN schedule s ON s.movieId = m.movieId
        JOIN room r ON r.roomId = s.roomId
        JOIN branch b ON b.branchId = r.branchId
        WHERE b.branchId = '$branch_id'
        AND (
            s.ngayChieu > CURDATE() 
            OR (s.ngayChieu = CURDATE() AND s.gioChieu > CURTIME())
        )
        GROUP BY m.movieId";
    return $conn->query($sql);
}
}
?>