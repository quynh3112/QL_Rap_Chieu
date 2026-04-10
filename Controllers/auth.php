<?php
    // Xử lý xác thực và phân quyền
    include "../config/db.php";

    function checkAuth($allowedRoles) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                "message" => "Unauthorized - Vui lòng đăng nhập."
            ]);
            exit;
        }

        $user = $_SESSION['user'];

        // Kiểm tra quyền (Hỗ trợ cả một chuỗi hoặc một mảng các quyền)
        // Ví dụ: checkAuth(['admin', 'manager'])
        $rolesArray = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        if (!in_array($user['role'], $rolesArray)) {
            http_response_code(403);
            echo json_encode([
                "status" => false,
                "message" => "Forbidden - Bạn không có quyền truy cập!"
            ]);
            exit;
        }
    }
?>