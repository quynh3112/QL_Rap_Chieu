<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Customer', 'Admin', 'Manager', 'Employee']);

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
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/food.css">
</head>
<body class="food-body" data-page="food-list">

    <?php 
        if (file_exists($headerPath) && filesize($headerPath) > 0) {
            include $headerPath;
        } else {
            echo '<div class="food-fallback-header">
                    <h1>CINEMA</h1>
                  </div>';
        }
    ?>

    <main class="food-page-shell">
        <section class="food-hero">
            <div>
                <h1 class="food-title">Bắp Nước Tại Rạp</h1>
                <p class="food-subtitle">Chọn món và phương thức thanh toán để mua đồ ăn riêng tại rạp.</p>
            </div>
        </section>

        <div class="cgv-container">
            <div id="food-list" class="food-grid">
                <p class="food-loading">Đang tải danh sách bắp nước...</p>
            </div>

            <div class="cart-sidebar">
                <h3 class="cart-title">Giỏ Hàng</h3>
                <div id="cart-content"><p>Giỏ hàng đang trống.</p></div>

                <div class="checkout-field">
                    <label for="payment-method">Phương thức thanh toán</label>
                    <select id="payment-method">
                        <option value="Tiền mặt">Tiền mặt</option>
                    </select>
                </div>

                <div class="cart-total-row">
                    <span class="cart-total-label">Tổng cộng:</span>
                    <b id="cart-total">0đ</b>
                </div>

                <button onclick="placeOrder()" class="btn-cgv-red checkout-btn">THANH TOÁN NGAY</button>
            </div>
        </div>
    </main>

    <script src="../js/food.js"></script>
</body>
</html>