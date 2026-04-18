<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Customer', 'Admin', 'Manager', 'Employee']);

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
    <title>Thanh Toán Đồ Ăn - CINEMA</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/food.css">
</head>
<body class="food-body" data-page="food-checkout">

    <?php
        if (file_exists($headerPath) && filesize($headerPath) > 0) {
            include $headerPath;
        } else {
            echo '<div class="food-fallback-header"><h1>CINEMA</h1></div>';
        }
    ?>

    <main class="food-page-shell checkout-page-shell">
        <section class="food-hero">
            <div>
                <h1 class="food-title">Xác Nhận Thanh Toán</h1>
                <p class="food-subtitle">Kiểm tra thông tin món ăn trước khi gửi thanh toán.</p>
            </div>
        </section>

        <div class="checkout-layout">
            <section class="checkout-main-card">
                <div class="checkout-food-card">
                    <h3>Thông Tin Đồ Ăn</h3>
                    <div id="checkout-food-items" class="checkout-food-items">
                        <p class="checkout-empty">Đang tải thông tin đồ ăn...</p>
                    </div>
                </div>
            </section>

            <aside class="checkout-side-card">
                <div class="checkout-field">
                    <label for="checkout-method">Phương thức thanh toán</label>
                    <select id="checkout-method">
                        <option value="Tiền mặt">Tiền mặt</option>
                    </select>
                </div>

                <div class="checkout-total-list">
                    <div class="checkout-total-row">
                        <span>Tiền đồ ăn</span>
                        <b id="checkout-food-total">0đ</b>
                    </div>
                    <div class="checkout-total-row grand">
                        <span>Tổng thanh toán</span>
                        <b id="checkout-payment-total">0đ</b>
                    </div>
                </div>

                <p id="checkout-message" class="checkout-message info"></p>

                <button id="confirm-checkout-btn" class="btn-cgv-red checkout-btn" onclick="submitCheckoutOrder()">
                    XÁC NHẬN THANH TOÁN
                </button>

                <a class="checkout-back-link" href="/QL_Rap_Chieu/View/pages/food.php?restoreCheckout=1">
                    ← Quay lại giỏ hàng
                </a>
            </aside>
        </div>
    </main>

    <script src="../js/food.js"></script>
</body>
</html>
