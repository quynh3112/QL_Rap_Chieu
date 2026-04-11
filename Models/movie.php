<?php
class Movie {
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function getAll(){
        $sql = "SELECT * FROM Movie ORDER BY movieId ASC";
        return $this->conn->query($sql);
    }
    public function getById($id){
        $sql = "SELECT * FROM Movie WHERE movieId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function create($data){
        $sql = "INSERT INTO Movie 
            (tenPhim, thoiLuong, moTa, img, daoDien, dienVien, namSanXuat, trangThai)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sissssis",
            $data['tenPhim'],
            $data['thoiLuong'],
            $data['moTa'],
            $data['img'],
            $data['daoDien'],
            $data['dienVien'],
            $data['namSanXuat'],
            $data['trangThai']
        );
        return $stmt->execute();
    }
    public function update($id, $data){
        $sql = "UPDATE Movie SET 
            tenPhim=?, thoiLuong=?, moTa=?, img=?, daoDien=?, dienVien=?, namSanXuat=?, trangThai=?
            WHERE movieId=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sissssisi",
            $data['tenPhim'],
            $data['thoiLuong'],
            $data['moTa'],
            $data['img'],
            $data['daoDien'],
            $data['dienVien'],
            $data['namSanXuat'],
            $data['trangThai'],
            $id
        );
        return $stmt->execute();
    }
    public function delete($id){
        $sql = "DELETE FROM Movie WHERE movieId=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function search($name = null, $category = null, $year = null,$status = null){
        $sql = "SELECT DISTINCT m.*
                FROM Movie m
                LEFT JOIN MovieCategory mc ON m.movieId = mc.movieId
                WHERE 1=1";
        if($name){
            $sql .= " AND m.tenPhim LIKE '%$name%'";
        }
        if($category){
            $sql .= " AND mc.categoryId = $category";
        }
        if($year){
            $sql .= " AND m.namSanXuat = $year";
        }
        if($status){
        $sql .= " AND m.trangThai = '$status'";
        }
        return $this->conn->query($sql);
    }
}