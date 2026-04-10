<?php
    include "../config/db.php";

    class Account {
        public function create($username, $password, $hoTen, $email, $sdt, $role, $branchId, $ngayDangKy) {
            global $conn;

            if (empty($role)) {
                $role = "customer";
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO account (username, password, hoTen, email, sdt, role, branchId, ngayDangKy)
                    VALUES ('$username', '$hash', '$hoTen', '$email', '$sdt', '$role', $branchId, '$ngayDangKy')";

            return $conn->query($sql);
        }

        public function update($accountId, $username, $hoTen, $email, $sdt, $branchId, $role) {
            global $conn;

            $sql = "UPDATE account 
                    SET username = '$username', hoTen = '$hoTen', email = '$email', sdt = '$sdt', role = '$role', branchId = $branchId
                    WHERE accountId = $accountId";

            return $conn->query($sql);
        }

        public function changePassword($accountId, $newPassword) {
            global $conn;

            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE account SET password = '$hash' WHERE accountId = $accountId";

            return $conn->query($sql);
        }

        public function delete($accountId) {
            global $conn;
            $sql = "DELETE FROM account WHERE accountId = $accountId";
            return $conn->query($sql);
        }

        public function login($email, $password) {
            global $conn;
            $sql = "SELECT * FROM account WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $account = $result->fetch_assoc();
                if (password_verify($password, $account['password'])) {
                    return $account;
                }
            }
            return false;
        }

        public function getAll() {
            global $conn;
            $sql = "SELECT * FROM account";
            return $conn->query($sql);
        }

        public function getById($accountId) {
            global $conn;
            $sql = "SELECT * FROM account WHERE accountId = $accountId";
            return $conn->query($sql);
        }
    }
?>