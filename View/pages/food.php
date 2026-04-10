<?php
$currentDir = __DIR__;
$controllersDir = realpath(__DIR__ . '/../../Controllers');

if ($controllersDir) {
    chdir($controllersDir); // Nhảy vào Controllers để file auth.php không bị lỗi include
}
//phân quyền
session_start();

require_once __DIR__ . '/../../Controllers/auth.php';

// Kiểm tra quyền
if (function_exists('checkAuth')) {
    checkAuth(['Customer', 'Admin', 'Manager', 'Employee']);
}

//hiển thị lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

$headerPath = __DIR__ . '/../component/header.php';
if (!file_exists($headerPath)) {
    $headerPath = __DIR__ . '/../components/header.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEMA - Bắp Nước</title>
    <link rel="stylesheet" href="../css/food.css"> 
    <style>
        .food-body { background-color: #000; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .cgv-container { display: flex; max-width: 1200px; margin: 30px auto; gap: 20px; padding: 0 15px; }
        .btn-cgv-red { background: #e71a0f; color: #fff; border: none; padding: 12px; cursor: pointer; font-weight: bold; border-radius: 4px; transition: 0.3s; }
        .btn-cgv-red:hover { background: #ff1a0f; }
        .cart-sidebar { flex: 1; background: #1a1a1a; padding: 20px; border-radius: 8px; height: fit-content; border: 1px solid #222; }
    </style>
</head>
<body class="food-body">

    <?php 
        if (file_exists($headerPath) && filesize($headerPath) > 0) {
            include $headerPath;
        } else {
            echo '<div style="background: #111; padding: 25px; text-align: center; border-bottom: 2px solid #e71a0f;">
                <h1 style="color: #e71a0f; margin: 0; letter-spacing: 2px;">CINEMA</h1>
            </div>';
        }
    ?>

    <div class="cgv-container">
        <!-- Danh sách bắp nước -->
        <div id="food-list" style="flex: 3; display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px;">
            <p style="color: #666;">Đang tải danh sách bắp nước...</p>
        </div>

        <!-- Giỏ hàng -->
        <div class="cart-sidebar">
            <h3 style="color: #e71a0f; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">
                GIỎ HÀNG
            </h3>
            <div id="cart-content">
                <p style="font-size: 14px; color: #555;">Giỏ hàng đang trống.</p>
            </div>
            <div style="margin-top: 20px; border-top: 1px solid #333; padding-top: 15px;">
                <span style="color: #ccc;">Tổng cộng:</span>
                <b id="cart-total" style="color: #f5c518; float: right; font-size: 1.2em;">0đ</b>
            </div>
            <button onclick="placeOrder()" class="btn-cgv-red" style="width: 100%; margin-top: 20px;">
                THANH TOÁN NGAY
            </button>
        </div>
    </div>

    <script src="../js/food.js"></script>
</body>
</html>