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
public function isDuplicate($branchId, $ngayLamViec){
    global $conn;

    $sql = "SELECT a.hoTen, ws.ngayLamViec, ws.caLam, ws.gioBatDau, ws.gioKetThuc 
            FROM workschedule ws
            JOIN account a ON ws.accountId = a.accountId
            WHERE ws.branchId = '$branchId' 
            AND ws.ngayLamViec = '$ngayLamViec'";

    return $conn->query($sql);  
}
public function isConflictSchedule($accountId, $branchId, $ngayLamViec, $gioBatDau, $gioKetThuc){
    global $conn;

    $sql = "SELECT * FROM workschedule
            WHERE accountId = '$accountId'
            AND branchId = '$branchId'
            AND ngayLamViec = '$ngayLamViec'
            AND NOT (
                gioKetThuc <= '$gioBatDau'
                OR
                gioBatDau >= '$gioKetThuc'
            )";

    return $conn->query($sql);
}
}
?>