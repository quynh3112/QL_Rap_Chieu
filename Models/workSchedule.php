<?php
include "../config/db.php";
class WorkSchedule {

    public function createWorkSchedule($accountId, $branchId, $ngayLamViec, $caLam, $gioBatDau, $gioKetThuc){
        global $conn;

        $sql = "INSERT INTO workschedule 
        (accountId, branchId, ngayLamViec, caLam, gioBatDau, gioKetThuc)
        VALUES 
        ('$accountId', '$branchId', '$ngayLamViec', '$caLam', '$gioBatDau', '$gioKetThuc')";

        return $conn->query($sql);
    }

    public function updateWorkSchedule($workId, $accountId, $branchId, $ngayLamViec, $caLam, $gioBatDau, $gioKetThuc){
        global $conn;

        $sql = "UPDATE workschedule SET
            accountId = '$accountId',
            branchId = '$branchId',
            ngayLamViec = '$ngayLamViec',
            caLam = '$caLam',
            gioBatDau = '$gioBatDau',
            gioKetThuc = '$gioKetThuc'
        WHERE workId = '$workId'";

        return $conn->query($sql);
    }

    public function deleteWorkSchedule($workId){
        global $conn;

        $sql = "DELETE FROM workschedule WHERE workId = '$workId'";
        return $conn->query($sql);
    }

    public function getById($workId){
        global $conn;

        $sql = "SELECT * FROM workschedule WHERE workId = '$workId'";
        return $conn->query($sql);
    }

    public function getAll(){
        global $conn;

        $sql = "SELECT * FROM workschedule";
        return $conn->query($sql);
    }
    public function getByAccount($accountId){
    global $conn;

    $sql = "SELECT * FROM workschedule 
            WHERE accountId = '$accountId'";

    return $conn->query($sql);
}
public function getByBranch($branchId){
    global $conn;
    $sql = "SELECT * FROM workschedule WHERE branchId = '$branchId'";
    return $conn->query($sql);
}
}
?>