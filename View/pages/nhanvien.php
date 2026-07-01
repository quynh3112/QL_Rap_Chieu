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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nhân viên — Đặt vé tại quầy · CinemaX</title>
    <link rel="stylesheet" href="../css/user.css"/>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>

<nav class="nv-topbar">
    <span class="brand">🎬 CinemaX</span>
    <span class="nv-role-badge">Nhân viên</span>
    <span id="nv-name"></span>
    <button onclick="showTab('history')" class="nav-btn">Lịch sử đặt vé</button>
    <button onclick="nvLogout()" class="nav-btn danger">Đăng xuất</button>
</nav>

<!-- BƯỚC 0: THÔNG TIN KHÁCH HÀNG -->
<div id="tab-khach" style="max-width:1100px;margin:30px auto;padding:0 24px;">
    <h2 class="section-title">Đặt vé <span>tại quầy</span></h2>
    <div class="khach-box">
        <label for="ten-khach">Tên khách hàng</label>
        <input id="ten-khach" placeholder="Nhập tên khách hàng..."/>
        <small>Bỏ trống nếu khách tự đặt bằng tài khoản của họ</small>
        <button class="btn-continue" onclick="goChonPhim()">Tiếp tục chọn phim →</button>
    </div>
</div>

<!-- BƯỚC 1: CHỌN PHIM -->
<div id="tab-movies" style="display:none;max-width:1100px;margin:24px auto;padding:0 24px;">
    <button onclick="showTab('khach')">← Quay lại</button>
    <h2 class="section-title">Chọn <span>phim</span></h2>
    <div class="filter-row">
        <input id="search-movie" placeholder="🔍  Tìm tên phim..." oninput="filterMovies()"/>
        <select id="filter-status" onchange="filterMovies()">
            <option value="">Tất cả trạng thái</option>
            <option value="Đang chiếu">Đang chiếu</option>
            <option value="Sắp chiếu">Sắp chiếu</option>
        </select>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>Tên phim</th><th>Đạo diễn</th><th>Thời lượng</th><th>Trạng thái</th><th></th></tr>
        </thead>
        <tbody id="tbody-movies"></tbody>
    </table>
    </div>
</div>

<!-- BƯỚC 2: SUẤT CHIẾU -->
<div id="tab-schedule" style="display:none;max-width:1100px;margin:24px auto;padding:0 24px;">
    <button onclick="showTab('movies')">← Quay lại</button>
    <h2 class="section-title" id="title-movie"></h2>
    <h3 style="color:var(--text-dim);font-size:14px;font-weight:500;margin-bottom:16px;text-transform:uppercase;letter-spacing:.6px;">Chọn suất chiếu</h3>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>Phòng</th><th>Ngày chiếu</th><th>Giờ chiếu</th><th>Giá vé</th><th></th></tr>
        </thead>
        <tbody id="tbody-schedule"></tbody>
    </table>
    </div>
</div>

<!-- BƯỚC 3: GHẾ -->
<div id="tab-seat" style="display:none;max-width:1100px;margin:24px auto;padding:0 24px;">
    <button onclick="showTab('schedule')">← Quay lại</button>
    <h3 class="section-title" id="title-schedule" style="font-size:18px;"></h3>
    <div class="legend">
        <span><span class="seat-std"></span> Thường</span>
        <span><span class="seat-vip"></span> VIP</span>
        <span><span class="seat-taken"></span> Đã đặt</span>
        <span><span class="seat-selected"></span> Đang chọn</span>
    </div>
    <div class="seat-screen">MÀN HÌNH</div>
    <div id="seat-map"></div>
    <div class="seat-summary">
        <span>Đã chọn: <b><span id="selected-count">0</span> ghế</b></span>
        <span class="total-price" id="tmp-total">0 đ</span>
    </div>
    <button onclick="showTab('food')" id="btn-to-food" disabled>Tiếp theo →</button>
</div>

<!-- BƯỚC 4: ĐỒ ĂN -->
<div id="tab-food" style="display:none;max-width:1100px;margin:24px auto;padding:0 24px;">
    <button onclick="showTab('seat')">← Quay lại</button>
    <h3 class="section-title">Chọn đồ ăn <span style="color:var(--text-dim);font-size:16px;font-weight:400;">(tuỳ chọn)</span></h3>
    <div class="table-wrap" style="margin-bottom:0;">
    <table>
        <thead><tr><th>Tên</th><th>Loại</th><th>Giá</th><th>Số lượng</th></tr></thead>
        <tbody id="tbody-food"></tbody>
    </table>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:0 0 var(--radius) var(--radius);padding:14px 18px;display:flex;justify-content:space-between;align-items:center;border-top:none;">
        <span style="color:var(--text-dim);font-size:14px;">Tổng đồ ăn</span>
        <b style="color:var(--gold);font-size:18px;" id="food-total">0 đ</b>
    </div>
    <button onclick="showTab('confirm')">Tiếp theo →</button>
</div>

<!-- BƯỚC 5: XÁC NHẬN + THANH TOÁN -->
<div id="tab-confirm" style="display:none;max-width:1100px;margin:24px auto;padding:0 24px;">
    <button onclick="showTab('food')">← Quay lại</button>
    <h3 class="section-title">Xác nhận <span>đặt vé</span></h3>
    <div id="confirm-detail"></div>
    <div class="confirm-total-bar">
        <span class="label">Tổng cộng</span>
        <span class="amount" id="confirm-total">0 đ</span>
    </div>
    <div class="qr-box">
        <h4>Thanh toán QR chuyển khoản</h4>
        <img id="qr-img" src="" style="width:180px;height:180px" alt="QR"/>
        <p id="qr-info"></p>
    </div>
    <br/>
    <button onclick="xacNhanDatVe()" id="btn-submit">✔  Xác nhận đã thanh toán &amp; Đặt vé</button>
</div>

<!-- LỊCH SỬ -->
<div id="tab-history" style="display:none;max-width:1100px;margin:24px auto;padding:0 24px;">
    <button onclick="showTab('khach')">← Quay lại</button>
    <h2 class="section-title">Lịch sử <span>đặt vé tại quầy</span></h2>
    <div class="table-wrap">
    <table>
        <thead><tr><th>ID</th><th>Khách</th><th>Phim</th><th>Phòng</th><th>Ngày chiếu</th><th>Giờ</th><th>Số vé</th><th>Trạng thái</th></tr></thead>
        <tbody id="tbody-history"></tbody>
    </table>
    </div>
</div>

<script>
const nvSessionUser = <?php echo json_encode($employeeUser, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
sessionStorage.setItem('user', JSON.stringify(nvSessionUser));
localStorage.setItem('user', JSON.stringify(nvSessionUser));
</script>
<script src="../js/nhanvien.js"></script>
</body>
</html>
