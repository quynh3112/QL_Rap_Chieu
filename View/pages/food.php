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
<<<<<<< HEAD
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
=======
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/food.css">
</head>
<body class="food-body" data-page="food-list">
>>>>>>> origin/dev-food

    <?php 
        if (file_exists($headerPath) && filesize($headerPath) > 0) {
            include $headerPath;
        } else {
<<<<<<< HEAD
            echo '<div style="background: #111; padding: 25px; text-align: center; border-bottom: 2px solid #e71a0f;">
                    <h1 style="color: #e71a0f; margin: 0; letter-spacing: 2px;">CINEMA</h1>
=======
            echo '<div class="food-fallback-header">
                    <h1>CINEMA</h1>
>>>>>>> origin/dev-food
                  </div>';
        }
    ?>

<<<<<<< HEAD
    <div class="cgv-container">
        <div id="food-list" style="flex: 3; display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px;">
            <p style="color: #666;">Đang tải danh sách bắp nước...</p>
        </div>

        <div class="cart-sidebar">
            <h3 style="color: #e71a0f; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">GIỎ HÀNG</h3>
            <div id="cart-content"><p style="font-size: 14px; color: #555;">Giỏ hàng đang trống.</p></div>
            <div style="margin-top: 20px; border-top: 1px solid #333; padding-top: 15px;">
                <span style="color: #ccc;">Tổng cộng:</span>
                <b id="cart-total" style="color: #f5c518; float: right; font-size: 1.2em;">0đ</b>
            </div>
            <button onclick="placeOrder()" class="btn-cgv-red" style="width: 100%; margin-top: 20px;">THANH TOÁN NGAY</button>
        </div>
    </div>
=======
    <main class="food-page-shell">
        <section class="food-hero">
            <div>
                <h1 class="food-title">Bắp Nước Tại Rạp</h1>
                <p class="food-subtitle">Chọn món và phương thức thanh toán, sau đó chuyển sang trang xác nhận đơn.</p>
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
                    <label for="booking-id">Mã booking (để trống nếu mua lẻ)</label>
                    <input type="number" id="booking-id" min="1" placeholder="Ví dụ: 12">
                </div>

                <div class="checkout-field">
                    <label for="payment-method">Phương thức thanh toán</label>
                    <select id="payment-method">
                        <option value="Tiền mặt">Tiền mặt</option>
                        <option value="Thẻ">Thẻ</option>
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
>>>>>>> origin/dev-food

    <script src="../js/food.js"></script>
</body>
</html>