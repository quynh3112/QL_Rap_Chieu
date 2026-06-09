<?php
    session_start();
    include "../models/account.php";
    header("Content-Type: application/json");

    $account = new Account();
    $method = $_SERVER['REQUEST_METHOD'];

    $data = json_decode(file_get_contents("php://input"), true);

    switch ($method) {
        case "POST" :
            // Xử lý Đăng nhập hoặc Đăng ký dựa trên một tham số 'action'
            if($data['action'] == 'login') {
                // Kiểm tra dữ liệu rỗng
                if (empty($data['email']) || empty($data['password'])) {
                    echo json_encode([
                        "status" => false,
                        "message" => "Vui lòng nhập đầy đủ thông tin đăng nhập."
                    ]);
                    exit;
                }

                $user = $account->login($data['email'], $data['password']);
                if($user) {
                    unset($user['password']);
                    $_SESSION['user'] = $user;

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
            } else if($data['action'] == 'create') {
                if($account->isUsernameExists($data['username'])) {
                    echo json_encode([
                        "status" => false,
                        "message" => "Tên đăng nhập đã có người sử dụng, vui lòng sử dụng tên khác."
                    ]);
                    exit;
                }

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
            $statusInfo = false;
            $statusPass = false;

            // 1. Luôn cập nhật thông tin cá nhân
            $branchId_SQL = empty($data['branchId']) ? "NULL" : (int)$data['branchId'];
            if($account->update($data['accountId'], $data['username'], $data['hoTen'], $data['email'], $data['sdt'], $branchId_SQL, $data['role'])) {
                if (isset($_SESSION['user']) && $_SESSION['user']['accountId'] == $data['accountId']) {
                    $_SESSION['user']['username'] = $data['username'];
                    $_SESSION['user']['hoTen'] = $data['hoTen'];
                    $_SESSION['user']['email'] = $data['email'];
                    $_SESSION['user']['sdt'] = $data['sdt'];
                }
                $statusInfo = true;
            }

            // 2. Nếu có nhập mật khẩu thì cập nhật thêm mật khẩu
            if(!empty($data['password'])) {
                if($account->changePassword($data['accountId'], $data['password'])) {
                    $statusPass = true;
                }
            }

            echo json_encode([
                "status" => $statusInfo,
                "message" => "Cập nhật thành công!" . ($statusPass ? " (Đã đổi mật khẩu)" : "")
            ]);
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
            $branchId = $_GET['branchId'] ?? null;
            if ($branchId) {
                $result = $account->staffByBranch($branchId);
                $list = [];
                while ($row = $result->fetch_assoc()) {
                    $list[] = $row;
                }
                echo json_encode($list);
                exit;
            }

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