<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Admin - Quản lý</title>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>

<div>
    <strong>ADMIN</strong> &nbsp;|&nbsp;
    <span id="admin-name"></span> &nbsp;|&nbsp;
    <button onclick="showTab('booking')">Đặt vé</button>
    <button onclick="showTab('account')">Tài khoản</button>
    <button onclick="showTab('food')">Đồ ăn</button>
    <button onclick="showTab('schedule')">Lịch chiếu</button>
    <button onclick="showTab('doanhthu')">Doanh thu</button>
    <button onclick="showTab('phong')">Thống kê phòng</button>
    <button onclick="adminLogout()">Đăng xuất</button>
</div>
<hr/>

<!-- TAB BOOKING -->
<div id="tab-booking" class="tab">
    <h2>Danh sách đặt vé</h2>
    <div>
        <select id="bk-filter-status">
            <option value="">Tất cả trạng thái</option>
            <option value="Chờ thanh toán">Chờ thanh toán</option>
            <option value="Đã xác nhận">Đã xác nhận</option>
            <option value="Đã hủy">Đã hủy</option>
        </select>
        <button onclick="loadBookings()">Lọc</button>
        <button onclick="exportTable('tbl-booking','booking')">Xuất CSV</button>
    </div>
    <br/>
    <table border="1" cellpadding="6" cellspacing="0" id="tbl-booking">
        <thead>
            <tr>
                <th>ID</th><th>Tên người đặt</th><th>Tên KH</th><th>Phim</th><th>Phòng</th>
                <th>Ngày chiếu</th><th>Giờ</th><th>Số vé</th>
                <th>Ngày đặt</th><th>Trạng thái</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody id="tbody-booking"></tbody>
    </table>
</div>

<!-- TAB TÀI KHOẢN -->
<div id="tab-account" class="tab" style="display:none">
    <h2>Quản lý tài khoản</h2>
    <div>
        <select id="acc-filter-role">
            <option value="">Tất cả</option>
            <option value="Customer">Khách hàng</option>
            <option value="Employee">Nhân viên</option>
        </select>
        <button onclick="loadAccounts()">Lọc</button>
        <button onclick="openAccDialog(null)">Thêm tài khoản</button>
        <button onclick="exportTable('tbl-account','account')">Xuất CSV</button>
    </div>
    <br/>
    <table border="1" cellpadding="6" cellspacing="0" id="tbl-account">
        <thead>
            <tr>
                <th>ID</th><th>Username</th><th>Họ tên</th><th>Email</th>
                <th>SĐT</th><th>Role</th><th>Ngày đăng ký</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody id="tbody-account"></tbody>
    </table>
</div>

<!-- TAB ĐỒ ĂN -->
<div id="tab-food" class="tab" style="display:none">
    <h2>Quản lý đồ ăn / nước uống</h2>
    <div>
        <button onclick="openFoodDialog(null)">Thêm món</button>
        <button onclick="exportTable('tbl-food','food')">Xuất CSV</button>
    </div>
    <br/>
    <table border="1" cellpadding="6" cellspacing="0" id="tbl-food">
        <thead>
            <tr>
                <th>ID</th><th>Tên món</th><th>Loại</th><th>Giá</th>
                <th>Tồn kho</th><th>Trạng thái</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody id="tbody-food"></tbody>
    </table>
</div>

<!-- TAB LỊCH CHIẾU -->
<div id="tab-schedule" class="tab" style="display:none">
    <h2>Quản lý lịch chiếu</h2>
    <div>
        <button onclick="openScheduleDialog(null)">Thêm lịch chiếu</button>
        <button onclick="exportTable('tbl-schedule','schedule')">Xuất CSV</button>
    </div>
    <br/>
    <table border="1" cellpadding="6" cellspacing="0" id="tbl-schedule">
        <thead>
            <tr>
                <th>ID</th><th>Phim</th><th>Phòng</th><th>Ngày chiếu</th>
                <th>Giờ chiếu</th><th>Giá vé</th><th>Trạng thái</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody id="tbody-schedule"></tbody>
    </table>
</div>

<!-- TAB DOANH THU -->
<div id="tab-doanhthu" class="tab" style="display:none">
    <h2>Thống kê doanh thu</h2>
    <div>
        <select id="dt-loai" onchange="toggleDateInput()">
            <option value="month">Theo tháng</option>
            <option value="week">Theo tuần</option>
        </select>
        <input type="month" id="dt-month"/>
        <input type="week"  id="dt-week" style="display:none"/>
        <button onclick="loadDoanhThu()">Xem</button>
        <button onclick="exportTable('tbl-doanhthu','doanhthu')">Xuất CSV</button>
    </div>
    <br/>
    <table border="1" cellpadding="6" cellspacing="0" id="tbl-doanhthu">
        <thead>
            <tr>
                <th>Phim</th><th>Số booking</th><th>Số vé</th>
                <th>Doanh thu vé</th><th>Doanh thu đồ ăn</th><th>Tổng</th>
            </tr>
        </thead>
        <tbody id="tbody-doanhthu"></tbody>
        <tfoot><tr id="tfoot-doanhthu"></tr></tfoot>
    </table>
</div>

<!-- TAB THỐNG KÊ PHÒNG -->
<div id="tab-phong" class="tab" style="display:none">
    <h2>Thống kê phòng theo suất chiếu</h2>
    <div>
        <select id="phong-schedule"></select>
        <button onclick="loadThongKePhong()">Xem</button>
        <button onclick="exportTable('tbl-phong','phong')">Xuất CSV</button>
    </div>
    <br/>
    <table border="1" cellpadding="6" cellspacing="0" id="tbl-phong">
        <thead>
            <tr>
                <th>Phòng</th><th>Tổng ghế</th><th>Ghế đã đặt</th>
                <th>Còn trống</th><th>Số booking</th>
                <th>DT vé</th><th>DT đồ ăn</th><th>Tổng</th>
            </tr>
        </thead>
        <tbody id="tbody-phong"></tbody>
    </table>
</div>

<!-- DIALOG TÀI KHOẢN -->
<dialog id="dlg-account">
    <form id="form-account">
        <h3 id="dlg-acc-title">Thêm tài khoản</h3>
        <input type="hidden" id="acc-id"/>
        <div><label>Username: <input id="acc-username" required/></label></div>
        <div><label>Mật khẩu: <input id="acc-password" type="password" placeholder="Để trống = giữ cũ"/></label></div>
        <div><label>Họ tên: <input id="acc-hoten" required/></label></div>
        <div><label>Email: <input id="acc-email"/></label></div>
        <div><label>SĐT: <input id="acc-sdt"/></label></div>
        <div><label>Role:
            <select id="acc-role">
                <option value="Customer">Khách hàng</option>
                <option value="Employee">Nhân viên</option>
            </select>
        </label></div>
        <div>
            <button type="button" onclick="document.getElementById('dlg-account').close()">Hủy</button>
            <button type="submit">Lưu</button>
        </div>
    </form>
</dialog>

<!-- DIALOG ĐỒ ĂN -->
<dialog id="dlg-food">
    <form id="form-food">
        <h3 id="dlg-food-title">Thêm món ăn</h3>
        <input type="hidden" id="food-id"/>
        <div><label>Tên món: <input id="food-ten" required/></label></div>
        <div><label>Loại:
            <select id="food-loai">
                <option value="Đồ ăn">Đồ ăn</option>
                <option value="Đồ uống">Đồ uống</option>
                <option value="Combo">Combo</option>
            </select>
        </label></div>
        <div><label>Giá (VND): <input id="food-gia" type="number" required/></label></div>
        <div><label>Tồn kho: <input id="food-tonkho" type="number" value="0"/></label></div>
        <div><label>Trạng thái:
            <select id="food-trangthai">
                <option value="Còn">Còn</option>
                <option value="Hết">Hết</option>
            </select>
        </label></div>
        <div>
            <button type="button" onclick="document.getElementById('dlg-food').close()">Hủy</button>
            <button type="submit">Lưu</button>
        </div>
    </form>
</dialog>

<!-- DIALOG LỊCH CHIẾU -->
<dialog id="dlg-schedule">
    <form id="form-schedule">
        <h3 id="dlg-sch-title">Thêm lịch chiếu</h3>
        <input type="hidden" id="sch-id"/>
        <div><label>Phim: <select id="sch-movie" required></select></label></div>
        <div><label>Phòng: <select id="sch-room" required></select></label></div>
        <div><label>Ngày chiếu: <input type="date" id="sch-ngay" required/></label></div>
        <div><label>Giờ chiếu: <input type="time" id="sch-gio" required/></label></div>
        <div><label>Giá vé: <input type="number" id="sch-gia" required/></label></div>
        <div>
            <button type="button" onclick="document.getElementById('dlg-schedule').close()">Hủy</button>
            <button type="submit">Lưu</button>
        </div>
    </form>
</dialog>

<!-- DIALOG QR THANH TOÁN -->
<dialog id="dlg-qr">
    <h3>Thông tin thanh toán</h3>
    <div id="qr-detail"></div>
    <br/>
    <img id="qr-img" src="" style="width:200px;height:200px;" alt="QR thanh toán"/>
    <br/><br/>
    <button onclick="document.getElementById('dlg-qr').close()">Đóng</button>
</dialog>
<script>
     localStorage.setItem('user', JSON.stringify({
    id: <?= $_SESSION['user']['accountId'] ?? 'null' ?>,
    hoTen: "<?= $_SESSION['user']['hoTen'] ?? '' ?>",
    role: "<?= $_SESSION['user']['role'] ?? '' ?>"
  }));
</script>

<script src="../js/admin.js"></script>
</body>
</html>
