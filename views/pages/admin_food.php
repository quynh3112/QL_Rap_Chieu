<?php
session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>CGV Admin - Quản Lý Bắp Nước</title>
        <link rel="stylesheet" href=".../css/food.css">
    </head>
    <body class="food-body">
        <div class="admin-container">
            <h1 class="cgv-title">HỆ THỐNG QUẢN TRỊ BẮP NƯỚC</h1>

            <!-- Tab chuyển đổi -->
             <div class="admin-tabs">
                <button class="tab-btn active" onclick="switchTab('food-list')">Danh Sách Món</button>
                <button class="tab-btn" onclick="switchTab('order-list')">DANH SÁCH ĐƠN HÀNG</button>
             </div>

             <!-- phần 1: Quản lý món ăn -->
              <div id="section-food-list" class="admin-section">
                <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                    <button class="btn-cgv-red" onclick="openModal()">+ THÊM MÓN MỚI</button>
                </div>
                <table class="admin-food-table">
                    <thead>
                        <tr>
                            <th>Tên Món</th>
                            <th>Loại</th>
                            <th>Giá (VNĐ)</th>
                            <th>Số Lượng Tồn</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-food"><</tbody>
                </table>
              </div>

              <!-- phần 2: Quản lý đơn hàng -->
                <div id="section-order-list" class="admin-section" style="display: none;">
                    <table class="admin-food-table">
                        <thead>
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Khách Hàng</th>
                                <th>Ngày Đặt</th>
                                <th>Tổng Tiền</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-orders"></tbody>
                    </table>
                </div>
        </div>
        <!-- Modal thêm/sửa món ăn -->
         <div id="food-modal" class="cgv-modal" style="display:none;">
            <div class="modal-content">
                <h2 id="modal-title">THÊM MÓN ĂN</h2>
                <form id="food-form">
                    <input type="hidden" id="foodId">
                    <div class="form-group">
                        <label>Tên món:</label>
                        <input type="text" id="tenFood" required>
                    </div>
                    <div class="form-group">
                        <label>Loại món:</label>
                        <select id="loaiFood" required>
                            <option value="Bắp">Bắp</option>
                            <option value="Nước">Nước</option>
                            <optin value="Combo">Combo</option>
                        </select>
            </div>
            <div class="form-group">
                <label>Giá (VNĐ):</label>
                <input type="number" id="gia" required>
         </div>
         <div class="form-group">
             <label>Số lượng tồn:</label>
             <input type="number" id="soLuongTon" required>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-cancel">HUỶ</button>
                <button type="submit" class="btn-cgv-red">LƯU DỮ LIỆU</button>
            </div>
</form>
</div>
</div>
<script src="../js/admin-food.js"></script>
</body>
</html>