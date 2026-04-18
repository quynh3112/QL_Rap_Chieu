<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$employeeUser = $_SESSION['user'] ?? null;
if (!$employeeUser || ($employeeUser['role'] ?? '') !== 'Employee') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Nhân viên - Đặt vé tại quầy</title>
    <link rel="stylesheet" href="../css/user.css"/>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>

<div>
    <strong>NHÂN VIÊN</strong> &nbsp;|&nbsp;
    <span id="nv-name"></span> &nbsp;|&nbsp;
    <button onclick="showTab('history')">Lịch sử đặt vé</button>
    <button onclick="nvLogout()">Đăng xuất</button>
</div>
<hr/>

<!-- BƯỚC 0: THÔNG TIN KHÁCH HÀNG -->
<div id="tab-khach">
    <h3>Thông tin khách hàng (đặt tại quầy)</h3>
    <div>
        <label>Tên khách hàng: <input id="ten-khach" placeholder="Nhập tên khách..." style="width:250px"/></label>
        &nbsp;
        <button onclick="goChonPhim()">Tiếp tục chọn phim →</button>
    </div>
    <p><small>Bỏ trống nếu khách tự đặt bằng tài khoản của họ</small></p>
</div>

<!-- BƯỚC 1: CHỌN PHIM -->
<div id="tab-movies" style="display:none">
    <button onclick="showTab('khach')">← Quay lại</button>
    <h3>Chọn phim</h3>
    <input id="search-movie" placeholder="Tìm tên phim..." oninput="filterMovies()"/>
    <select id="filter-status" onchange="filterMovies()">
        <option value="">Tất cả</option>
        <option value="Đang chiếu">Đang chiếu</option>
        <option value="Sắp chiếu">Sắp chiếu</option>
    </select>
    <br/><br/>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead><tr><th>Tên phim</th><th>Đạo diễn</th><th>Thời lượng</th><th>Trạng thái</th><th></th></tr></thead>
        <tbody id="tbody-movies"></tbody>
    </table>
</div>

<!-- BƯỚC 2: SUẤT CHIẾU -->
<div id="tab-schedule" style="display:none">
    <button onclick="showTab('movies')">← Quay lại</button>
    <h3 id="title-movie"></h3>
    <h4>Chọn suất chiếu</h4>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead><tr><th>Phòng</th><th>Ngày chiếu</th><th>Giờ chiếu</th><th>Giá vé</th><th></th></tr></thead>
        <tbody id="tbody-schedule"></tbody>
    </table>
</div>

<!-- BƯỚC 3: GHẾ -->
<div id="tab-seat" style="display:none">
    <button onclick="showTab('schedule')">← Quay lại</button>
    <h3 id="title-schedule"></h3>
    <div>
        <span style="background:#e0e0e0;padding:2px 8px">■</span> Thường &nbsp;
        <span style="background:#ffd700;padding:2px 8px">■</span> VIP &nbsp;
        <span style="background:#e74c3c;color:#fff;padding:2px 8px">■</span> Đã đặt &nbsp;
        <span style="background:#2ecc71;padding:2px 8px">■</span> Đang chọn
    </div>
    <br/>
    <div id="seat-map"></div>
    <br/>
    <p>Đã chọn: <b id="selected-count">0</b> ghế &nbsp;|&nbsp; Tạm tính: <b id="tmp-total">0</b> đ</p>
    <button onclick="showTab('food')" id="btn-to-food" disabled>Tiếp theo →</button>
</div>

<!-- BƯỚC 4: ĐỒ ĂN -->
<div id="tab-food" style="display:none">
    <button onclick="showTab('seat')">← Quay lại</button>
    <h3>Chọn đồ ăn / nước uống (tuỳ chọn)</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead><tr><th>Tên</th><th>Loại</th><th>Giá</th><th>Số lượng</th></tr></thead>
        <tbody id="tbody-food"></tbody>
    </table>
    <br/>
    <p>Tổng đồ ăn: <b id="food-total">0</b> đ</p>
    <button onclick="showTab('confirm')">Tiếp theo →</button>
</div>

<!-- BƯỚC 5: XÁC NHẬN + THANH TOÁN -->
<div id="tab-confirm" style="display:none">
    <button onclick="showTab('food')">← Quay lại</button>
    <h3>Xác nhận đặt vé</h3>
    <div id="confirm-detail"></div>
    <br/>
    <p><strong>Tổng cộng: <span id="confirm-total">0</span> đ</strong></p>
    <br/>
    <div style="border:1px solid #ccc;padding:12px;display:inline-block">
        <h4>Thanh toán QR chuyển khoản</h4>
        <img id="qr-img" src="" style="width:180px;height:180px" alt="QR"/>
        <p id="qr-info"></p>
    </div>
    <br/><br/>
    <button onclick="xacNhanDatVe()" id="btn-submit">Xác nhận đã thanh toán &amp; Đặt vé</button>
</div>

<!-- LỊCH SỬ -->
<div id="tab-history" style="display:none">
    <button onclick="showTab('khach')">← Quay lại</button>
    <h2>Lịch sử đặt vé tại quầy</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead><tr><th>ID</th><th>Khách</th><th>Phim</th><th>Phòng</th><th>Ngày chiếu</th><th>Giờ</th><th>Số vé</th><th>Trạng thái</th></tr></thead>
        <tbody id="tbody-history"></tbody>
    </table>
</div>

<script>
const nvSessionUser = <?php echo json_encode($employeeUser, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
sessionStorage.setItem('user', JSON.stringify(nvSessionUser));
localStorage.setItem('user', JSON.stringify(nvSessionUser));
</script>
<script src="../js/nhanvien.js"></script>
</body>
</html>
