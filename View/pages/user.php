<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Đặt vé xem phim </title>
    <link rel="stylesheet" href="../css/user.css"/>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>

<nav>
    <span class="brand">🎬 CGV</span>
    <span id="nav-user"></span>
    <button id="btn-history" onclick="showTab('history')">Lịch sử đặt vé</button>
    <button id="btn-logout" onclick="logout()">Đăng xuất</button>
</nav>

<!-- ── BƯỚC 1: CHỌN PHIM ──────────────────────── -->
<div id="tab-movies" class="tab">
    <h2 class="section-title">Chọn <span>phim</span></h2>
    <div class="filter-row">
        <input id="search-movie" placeholder="🔍  Tìm tên phim..." oninput="filterMovies()"/>
        <select id="filter-status" onchange="filterMovies()">
            <option value="">Tất cả trạng thái</option>
            <option value="Đang chiếu">Đang chiếu</option>
            <option value="Sắp chiếu">Sắp chiếu</option>
        </select>
    </div>
    <div id="movie-list" class="card-grid"></div>
</div>

<!-- ── BƯỚC 2: CHỌN RẠP & SUẤT CHIẾU ─────────── -->
<div id="tab-schedule" class="tab hidden">
    <button onclick="showTab('movies')">← Quay lại</button>
    <h2 class="section-title" id="title-movie"></h2>
    <h3 style="color:var(--text-dim);font-size:14px;font-weight:500;margin-bottom:16px;text-transform:uppercase;letter-spacing:.6px;">Chọn rạp và suất chiếu</h3>
    <div id="schedule-list"></div>
</div>

<!-- ── BƯỚC 3: CHỌN GHẾ ───────────────────────── -->
<div id="tab-seat" class="tab hidden">
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

<!-- ── BƯỚC 4: CHỌN ĐỒ ĂN ────────────────────── -->
<div id="tab-food" class="tab hidden">
    <button onclick="showTab('seat')">← Quay lại</button>
    <h3 class="section-title">Chọn đồ ăn <span style="color:var(--text-dim);font-size:16px;font-weight:400;">(tuỳ chọn)</span></h3>
    <div id="food-list" class="card-grid"></div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px 18px;margin-top:18px;display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--text-dim);font-size:14px;">Tổng đồ ăn</span>
        <b style="color:var(--gold);font-size:18px;" id="food-total">0 đ</b>
    </div>
    <button onclick="showTab('confirm')">Tiếp theo →</button>
</div>

<!-- ── BƯỚC 5: XÁC NHẬN ───────────────────────── -->
<div id="tab-confirm" class="tab hidden">
    <button onclick="showTab('food')">← Quay lại</button>
    <h3 class="section-title">Xác nhận <span>đặt vé</span></h3>
    <div id="confirm-detail"></div>
    <div class="confirm-total-bar">
        <span class="label">Tổng cộng</span>
        <span class="amount" id="confirm-total">0 đ</span>
    </div>
    <button onclick="submitBooking()" id="btn-submit">✔  Xác nhận đặt vé</button>
</div>

<!-- ── LỊCH SỬ ────────────────────────────────── -->
<div id="tab-history" class="tab hidden">
    <button onclick="showTab('movies')">← Quay lại phim</button>
    <h2 class="section-title">Lịch sử <span>đặt vé</span></h2>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Phim</th><th>Phòng</th>
                <th>Ngày chiếu</th><th>Giờ</th><th>Số vé</th><th>Trạng thái</th>
            </tr>
        </thead>
        <tbody id="tbody-history"></tbody>
    </table>
    </div>
</div>

<script>
localStorage.setItem('user', JSON.stringify(<?php echo isset($_SESSION['user']) ? json_encode($_SESSION['user']) : 'null'; ?>));
</script>
<script src="../js/user.js"></script>
</body>
</html>
