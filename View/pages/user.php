<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Đặt vé xem phim</title>
    <link rel="stylesheet" href="../css/user.css"/>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>

<nav>
    <span>ĐẶT VÉ</span>
    <span id="nav-user"></span>
    <button id="btn-history" onclick="showTab('history')">Lịch sử đặt vé</button>
    <button id="btn-logout" onclick="logout()">Đăng xuất</button>
</nav>

<!-- ── BƯỚC 1: CHỌN PHIM ──────────────────────── -->
<div id="tab-movies" class="tab">
    <h2>Chọn phim</h2>
    <div class="filter-row">
        <input id="search-movie" placeholder="Tìm tên phim..." oninput="filterMovies()"/>
        <select id="filter-status" onchange="filterMovies()">
            <option value="">Tất cả</option>
            <option value="Đang chiếu">Đang chiếu</option>
            <option value="Sắp chiếu">Sắp chiếu</option>
        </select>
    </div>
    <div id="movie-list" class="card-grid"></div>
</div>

<!-- ── BƯỚC 2: CHỌN RẠP & SUẤT CHIẾU ─────────── -->
<div id="tab-schedule" class="tab hidden">
    <button onclick="showTab('movies')">← Quay lại</button>
    <h2 id="title-movie"></h2>
    <h3>Chọn rạp và suất chiếu</h3>
    <div id="schedule-list"></div>
</div>

<!-- ── BƯỚC 3: CHỌN GHẾ ───────────────────────── -->
<div id="tab-seat" class="tab hidden">
    <button onclick="showTab('schedule')">← Quay lại</button>
    <h3 id="title-schedule"></h3>
    <div class="legend">
        <span class="seat-std">■</span> Thường
        <span class="seat-vip">■</span> VIP
        <span class="seat-taken">■</span> Đã đặt
        <span class="seat-selected">■</span> Đang chọn
    </div>
    <div id="seat-map"></div>
    <p>Đã chọn: <span id="selected-count">0</span> ghế — Tạm tính: <span id="tmp-total">0</span> đ</p>
    <button onclick="showTab('food')" id="btn-to-food" disabled>Tiếp theo →</button>
</div>

<!-- ── BƯỚC 4: CHỌN ĐỒ ĂN ────────────────────── -->
<div id="tab-food" class="tab hidden">
    <button onclick="showTab('seat')">← Quay lại</button>
    <h3>Chọn đồ ăn / nước uống (tuỳ chọn)</h3>
    <div id="food-list" class="card-grid"></div>
    <p>Tổng đồ ăn: <span id="food-total">0</span> đ</p>
    <button onclick="showTab('confirm')">Tiếp theo →</button>
</div>

<!-- ── BƯỚC 5: XÁC NHẬN ───────────────────────── -->
<div id="tab-confirm" class="tab hidden">
    <button onclick="showTab('food')">← Quay lại</button>
    <h3>Xác nhận đặt vé</h3>
    <div id="confirm-detail"></div>
    <p><strong>Tổng cộng: <span id="confirm-total">0</span> đ</strong></p>
    <button onclick="submitBooking()" id="btn-submit">Xác nhận đặt vé</button>
</div>

<!-- ── LỊCH SỬ ────────────────────────────────── -->
<div id="tab-history" class="tab hidden">
    <button onclick="showTab('movies')">← Quay lại phim</button>
    <h2>Lịch sử đặt vé</h2>
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

<script src="../js/user.js"></script>
</body>
</html>
