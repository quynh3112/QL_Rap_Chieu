<?php
    include "../models/account.php";
    header("Content-Type: application/json");

    $account = new Account();
    $method = $_SERVER['REQUEST_METHOD'];

    $data = json_decode(file_get_contents("php://input"), true);

    switch ($method) {
        case "POST" :
            // Xử lý Đăng nhập hoặc Đăng ký dựa trên một tham số 'action'
            if($data['action'] == 'login') {
                $user = $account->login($data['email'], $data['password']);
                if($user) {
                    echo json_encode([
                        "status" => true,
                        "message" => "Đăng nhập thành công!", 
                        "user" => $user
                    ]);
                } else {
                    echo json_encode([
                        "status" => false,
                        "message" => "Sai email hoặc mật khẩu."
                    ]);
                }
            } else {
                // Mặc định là Đăng ký (Create)
                if($account->create($data['username'], $data['password'], $data['hoTen'], $data['email'], $data['sdt'], $data['role'], $data['branchId'], date('Y-m-d H:i:s'))) {
                    echo json_encode([
                        "status" => true,
                        "message" => "Tạo tài khoản thành công!"
                    ]);
                } else {
                    echo json_encode([
                        "status" => false,
                        "message" => "Không thể tạo tài khoản."
                    ]);
                }
            }
            break;

        case "PUT":
            // Tách biệt xử lý Update dựa trên dữ liệu gửi lên
            if(!empty($data['password'])) {
                // Nếu có password mới thì cập nhật mật khẩu
                if($account->changePassword($data['accountId'], $data['password'])) {
                    echo json_encode([
                        "status" => true,
                        "message" => "Đổi mật khẩu thành công!"
                    ]);
                }
            } else {
                $branchId_SQL = empty($data['branchId']) ? "NULL" : (int)$data['branchId'];
                // Cập nhật thông tin profile
                if($account->update($data['accountId'], $data['username'], $data['hoTen'], $data['email'], $data['sdt'], $branchId_SQL, $data['role'])) {
                    echo json_encode([
                        "status" => true,
                        "message" => "Cập nhật thông tin thành công!"
                    ]);
                }
            }
            break;

        case "DELETE":
            if($account->delete($data['accountId'])) {
                echo json_encode([
                    "status" => true,
                    "message" => "Xóa tài khoản thành công!"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Không thể xóa tài khoản."
                ]);
            }
            break;

        case "GET":
            $accountId = $_GET['accountId'] ?? $data['accountId'] ?? null;

            if ($accountId) {
                $result = $account->getById($accountId);

                if ($result->num_rows <= 0) {
                    echo json_encode([
                        "status" => false,
                         "message" => "Không tìm thấy tài khoản."
                    ]);
                    exit;
                }

                $row = $result->fetch_assoc();
                echo json_encode($row);
            } else {
                $result = $account->getAll();
                $list = [];

                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }
                echo json_encode($list);
            }
            break;
    }
?>