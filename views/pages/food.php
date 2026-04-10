<?php
session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>CGV - Bắp Nước</title>
        <link rel="stylesheet" href="../css/food.css">
    </head>
    <body class="food-body">
        <?php include '../components/header.php'; ?>
        <div class="cgv-container" style="display: flex; max-width: 1200px; margin: 30px auto; gap: 20px;">
        <div id="food-list" style="flex: 3; display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px;">

        <!-- JS đồ món ăn -->
        </div>
        <div class="cart-sidebar" style="flex: 1; background: #1a1a1a; padding: 20px; border-radius: 5px; height: fit-content;">
            <h3 style="color: #e71a0f; margin-bottom: 15px;">GIỎ HÀNG</h3>
            <div id="cart-content"></div>
            <p style="margin-top: 15px; border-top: 1px solid #333; padding-top: 10px;">Tổng cộng: <b id="cart-total" style="color: #f5c518; float: right;">0đ</b></p>
            <button onclick="placeOrder()" class="btn-cgv-red" style="width: 100%; margin-top: 15px;">THANH TOÁN</button>
        </div>
    </div>
    <script src="../js/food.js"></script>
    </body>
</html>