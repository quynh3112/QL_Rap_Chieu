<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Admin', 'Manager', 'Employee']);
<<<<<<< HEAD
=======

$currentRole = $_SESSION['user']['role'] ?? '';
>>>>>>> dev-food
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Quản Lý Bắp Nước - CINEMA</title>
    <style>
        body { background: #000; color: #fff; font-family: Arial; padding: 20px; }
        .admin-tabs { display: flex; gap: 20px; border-bottom: 2px solid #333; margin-bottom: 20px; }
        .tab-item { padding: 10px 20px; cursor: pointer; color: #888; font-weight: bold; }
        .tab-item.active { color: #e71a0f; border-bottom: 3px solid #e71a0f; }
        table { width: 100%; border-collapse: collapse; background: #111; margin-top: 10px; }
        th { background: #222; color: gold; padding: 12px; border: 1px solid #333; text-transform: uppercase; font-size: 13px; }
        td { padding: 10px; border: 1px solid #222; text-align: center; }
        .btn-add { background: red; color: white; border: none; padding: 10px 20px; cursor: pointer; float: right; margin-bottom: 10px; font-weight: bold; border-radius: 4px; }
        .modal { position: fixed; inset: 0; background: rgba(0,0,0,0.8); display: none; justify-content: center; align-items: center; z-index: 100; }
        .modal-content { background: #222; padding: 20px; width: 350px; border-radius: 8px; border: 1px solid #444; }
        input, select { width: 100%; padding: 10px; margin: 5px 0 15px 0; background: #000; color: #fff; border: 1px solid #444; box-sizing: border-box; }
        .btn-save { background: red; color: white; width: 100%; padding: 12px; border: none; cursor: pointer; font-weight: bold; }
        .btn-cancel { background: #444; color: white; width: 100%; padding: 10px; border: none; cursor: pointer; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="admin-tabs">
        <div id="tab-food" class="tab-item active" onclick="switchTab('food')">DANH SÁCH MÓN</div>
        <div id="tab-order" class="tab-item" onclick="switchTab('order')">DANH SÁCH ĐƠN HÀNG</div>
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
        <div id="order-table-container">
            <p style="text-align:center; padding:50px;">Đang tải danh sách đơn hàng...</p>
        </div>
    </div>

    <div id="food-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title" style="color:gold;">THÔNG TIN MÓN</h3>
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

=======
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
>>>>>>> dev-food
    <script src="../js/admin-food.js"></script>
</body>
</html>