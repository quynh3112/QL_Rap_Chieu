<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Admin', 'Manager', 'Employee']);

$currentRole = $_SESSION['user']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bắp Nước - CINEMA</title>
    <link rel="stylesheet" href="../css/food.css">
</head>
<body class="food-admin-body">

    <main class="food-admin-shell">
        <section class="food-hero">
            <div>
                <h1 class="admin-title">Quản Lý Bắp Nước</h1>
                <p class="admin-subtitle">Theo dõi danh sách món và duyệt thanh toán tại một nơi.</p>
            </div>
        </section>

        <div class="admin-tabs">
            <div id="tab-food" class="tab-item active" onclick="switchTab('food')">DANH SÁCH MÓN</div>
            <div id="tab-order" class="tab-item" onclick="switchTab('order')">DUYỆT THANH TOÁN</div>
        </div>

        <div id="section-food" class="admin-section">
            <button class="btn-add" onclick="openModal()">+ THÊM MÓN MỚI</button>
            <table>
                <thead>
                    <tr>
                        <th>Tên Món</th><th>Loại</th><th>Giá</th><th>Tồn</th><th>Trạng Thái</th><th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody id="tbody-food">
                    <tr><td colspan="6">Đang tải danh sách món...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="section-order" class="admin-section" style="display:none;">
            <p class="order-note">Danh sách thanh toán đang chờ xử lý. Nhân viên chỉ có quyền xem.</p>
            <div id="order-table-container">
                <p class="loading-text">Đang tải danh sách thanh toán...</p>
            </div>
        </div>

        <div id="food-modal" class="modal">
            <div class="modal-content">
                <h3 id="modal-title" class="modal-title">THÔNG TIN MÓN</h3>
                <input type="hidden" id="input-foodId">
                <label>Tên món:</label> <input type="text" id="input-tenFood">
                <label>Loại:</label>
                <select id="input-loaiFood">
                    <option value="Bắp">Bắp</option>
                    <option value="Nước">Nước</option>
                    <option value="Combo">Combo</option>
                </select>
                <label>Giá bán:</label> <input type="number" id="input-gia">
                <label>Số lượng tồn:</label> <input type="number" id="input-soLuongTon">
                <button class="btn-save" onclick="saveFood()">LƯU LẠI</button>
                <button class="btn-cancel" onclick="closeModal()">HỦY</button>
            </div>
        </div>
    </main>

    <script>
        window.currentUserRole = "<?php echo htmlspecialchars($currentRole, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
    <script src="../js/admin-food.js"></script>
</body>
</html>