const BASE = 'http://localhost/QL_Merged/Controllers';
const adminUser = JSON.parse(sessionStorage.getItem('user') || 'null');

document.addEventListener('DOMContentLoaded', () => {
    if (!adminUser) { window.location.href = 'login.php'; return; }
    if (adminUser.role !== 'Admin' && adminUser.role !== 'Manager') {
        window.location.href = 'login.php'; return;
    }
    document.getElementById('admin-name').textContent = adminUser.hoTen + ' (' + adminUser.role + ')';
    loadBranchOptions();
    showTab('booking');
});

function adminLogout() {
    sessionStorage.removeItem('user');
    window.location.href = 'login.php';
}

function showTab(name) {
    document.querySelectorAll('.tab').forEach(t => t.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    if (name === 'booking')  loadBookings();
    if (name === 'account')  loadAccounts();
    if (name === 'food')     loadFoodAdmin();
    if (name === 'schedule') loadSchedules();
    if (name === 'doanhthu') { document.getElementById('dt-month').value = new Date().toISOString().substring(0,7); }
    if (name === 'phong')    loadScheduleSelect();
}

function fmt(n) { return Number(n).toLocaleString('vi-VN') + ' đ'; }

// ── BOOKING ────────────────────────────────────────────────
async function loadBookings() {
    const status = document.getElementById('bk-filter-status').value;
    const res    = await fetch(`${BASE}/bookingController.php`);
    let data     = await res.json();
    if (status) data = data.filter(b => b.trangThai === status);

    document.getElementById('tbody-booking').innerHTML = data.map(b => `
        <tr>
            <td>${b.bookingId}</td>
            <td>${b.hoTen}</td>
            <td>${b.tenKhach || '-'}</td>
            <td>${b.tenPhim}</td>
            <td>${b.tenPhong}</td>
            <td>${b.ngayChieu}</td>
            <td>${b.gioChieu}</td>
            <td>${b.soLuong}</td>
            <td>${b.ngayDat?.substring(0,16)}</td>
            <td><b>${b.trangThai}</b></td>
            <td>
                ${b.trangThai === 'Chờ thanh toán' ? `
                    <button onclick="xacNhanThanhToan(${b.bookingId})">Xác nhận thanh toán</button>
                    <button onclick="updateBooking(${b.bookingId},'Đã hủy')">Hủy</button>
                ` : ''}
                <button onclick="viewGhe(${b.bookingId})">Xem ghế</button>
                <button onclick="showQR(${b.bookingId},${b.soLuong},'${b.tenPhim}')">QR</button>
                <button onclick="xoaBooking(${b.bookingId})" style="background:#c0392b;color:#fff;border:none;padding:3px 8px;cursor:pointer;border-radius:3px">Xóa</button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="11">Không có dữ liệu</td></tr>';
}

async function xacNhanThanhToan(id) {
    if (!confirm('Xác nhận khách đã thanh toán?')) return;
    const res    = await fetch(`${BASE}/bookingController.php?id=${id}`, {
        method: 'PUT', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ trangThai: 'Đã xác nhận' })
    });
    const result = await res.json();
    alert(result.message);
    if (result.status) loadBookings();
}

async function updateBooking(id, trangThai) {
    if (!confirm(`Đổi trạng thái thành "${trangThai}"?`)) return;
    const res    = await fetch(`${BASE}/bookingController.php?id=${id}`, {
        method: 'PUT', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ trangThai })
    });
    const result = await res.json();
    alert(result.message);
    if (result.status) loadBookings();
}

async function viewGhe(bookingId) {
    const res  = await fetch(`${BASE}/bookingController.php?action=ghe_booking&bookingId=${bookingId}`);
    const data = await res.json();
    const info = data.map(g => `${g.tenGhe} (${g.loaiGhe})`).join(', ');
    alert(`Ghế của booking #${bookingId}:\n${info || 'Không có'}`);
}

function showQR(bookingId, soLuong, tenPhim) {
    // QR chuyển khoản tĩnh - thay STK và tên ngân hàng của bạn
    const soTien   = soLuong * 85000; // giá ước tính
    const noiDung  = `DATVE ${bookingId}`;
    const stk      = '1234567890';   // ← SỬA SỐ TÀI KHOẢN CỦA BẠN
    const nganHang = 'MBBank';       // ← SỬA TÊN NGÂN HÀNG

    document.getElementById('qr-detail').innerHTML = `
        <p><b>Booking #${bookingId}</b> — ${tenPhim}</p>
        <p>Ngân hàng: <b>${nganHang}</b></p>
        <p>Số tài khoản: <b>${stk}</b></p>
        <p>Số tiền: <b>${fmt(soTien)}</b></p>
        <p>Nội dung CK: <b>${noiDung}</b></p>
    `;
    // QR VietQR tự động sinh từ thông tin ngân hàng
    document.getElementById('qr-img').src =
        `https://img.vietqr.io/image/${nganHang}-${stk}-compact2.png?amount=${soTien}&addInfo=${noiDung}`;
    document.getElementById('dlg-qr').showModal();
}

async function xoaBooking(id) {
    if (!confirm('Xóa booking #' + id + ' khỏi hệ thống?')) return;
    const res    = await fetch(`${BASE}/bookingController.php`, {
        method: 'DELETE', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ id })
    });
    const result = await res.json();
    alert(result.message);
    if (result.status) loadBookings();
}

// ── TÀI KHOẢN ──────────────────────────────────────────────
async function loadAccounts() {
    const role = document.getElementById('acc-filter-role').value;
    const url  = `${BASE}/accountController.php` + (role ? `?role=${role}` : '');
    const res  = await fetch(url);
    const data = await res.json();

    document.getElementById('tbody-account').innerHTML = data
        .filter(a => a.role === 'Customer' || a.role === 'Employee')
        .map(a => `
        <tr>
            <td>${a.accountId}</td>
            <td>${a.username}</td>
            <td>${a.hoTen}</td>
            <td>${a.email}</td>
            <td>${a.sdt}</td>
            <td>${a.role}</td>
            <td>${a.ngayDangKy?.substring(0,10)}</td>
            <td>
                <button onclick='openAccDialog(${JSON.stringify(a)})'>Sửa</button>
                <button onclick="deleteAccount(${a.accountId})">Xóa</button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="8">Không có dữ liệu</td></tr>';
}

function openAccDialog(acc) {
    document.getElementById('dlg-acc-title').textContent = acc ? 'Sửa tài khoản' : 'Thêm tài khoản';
    document.getElementById('acc-id').value       = acc?.accountId || '';
    document.getElementById('acc-username').value = acc?.username  || '';
    document.getElementById('acc-hoten').value    = acc?.hoTen     || '';
    document.getElementById('acc-email').value    = acc?.email     || '';
    document.getElementById('acc-sdt').value      = acc?.sdt       || '';
    document.getElementById('acc-role').value     = acc?.role      || 'Customer';
    document.getElementById('acc-password').value = '';
    document.getElementById('acc-username').disabled = !!acc;
    document.getElementById('dlg-account').showModal();
}

document.getElementById('form-account').addEventListener('submit', async e => {
    e.preventDefault();
    const id      = document.getElementById('acc-id').value;
    const payload = {
        username : document.getElementById('acc-username').value,
        password : document.getElementById('acc-password').value,
        hoTen    : document.getElementById('acc-hoten').value,
        email    : document.getElementById('acc-email').value,
        sdt      : document.getElementById('acc-sdt').value,
        role     : document.getElementById('acc-role').value,
        branchId : null,
    };
    const method = id ? 'PUT' : 'POST';
    const url    = id ? `${BASE}/accountController.php?id=${id}` : `${BASE}/accountController.php`;
    const res    = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    const result = await res.json();
    alert(result.message);
    if (result.status) { document.getElementById('dlg-account').close(); loadAccounts(); }
});

async function deleteAccount(id) {
    if (!confirm('Xóa tài khoản này?')) return;
    const res    = await fetch(`${BASE}/accountController.php`, {
        method: 'DELETE', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ id })
    });
    const result = await res.json();
    alert(result.message);
    if (result.status) loadAccounts();
}

// ── ĐỒ ĂN ──────────────────────────────────────────────────
async function loadFoodAdmin() {
    const res  = await fetch(`${BASE}/foodController.php?all=1`);
    const data = await res.json();
    document.getElementById('tbody-food').innerHTML = data.map(f => `
        <tr>
            <td>${f.foodId}</td>
            <td>${f.tenFood}</td>
            <td>${f.loaiFood}</td>
            <td>${fmt(f.gia)}</td>
            <td>${f.soLuongTon}</td>
            <td>${f.trangThai}</td>
            <td>
                <button onclick='openFoodDialog(${JSON.stringify(f)})'>Sửa</button>
                <button onclick="deleteFood(${f.foodId})">Xóa</button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="7">Không có đồ ăn</td></tr>';
}

function openFoodDialog(food) {
    document.getElementById('dlg-food-title').textContent = food ? 'Sửa món ăn' : 'Thêm món ăn';
    document.getElementById('food-id').value        = food?.foodId    || '';
    document.getElementById('food-ten').value       = food?.tenFood   || '';
    document.getElementById('food-loai').value      = food?.loaiFood  || 'Đồ ăn';
    document.getElementById('food-gia').value       = food?.gia       || '';
    document.getElementById('food-tonkho').value    = food?.soLuongTon || 0;
    document.getElementById('food-trangthai').value = food?.trangThai || 'Còn';
    document.getElementById('dlg-food').showModal();
}

document.getElementById('form-food').addEventListener('submit', async e => {
    e.preventDefault();
    const id      = document.getElementById('food-id').value;
    const payload = {
        tenFood   : document.getElementById('food-ten').value,
        loaiFood  : document.getElementById('food-loai').value,
        gia       : document.getElementById('food-gia').value,
        soLuongTon: document.getElementById('food-tonkho').value,
        trangThai : document.getElementById('food-trangthai').value,
    };
    const method = id ? 'PUT' : 'POST';
    const url    = id ? `${BASE}/foodController.php?id=${id}` : `${BASE}/foodController.php`;
    const res    = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    const result = await res.json();
    alert(result.message);
    if (result.status) { document.getElementById('dlg-food').close(); loadFoodAdmin(); }
});

async function deleteFood(id) {
    if (!confirm('Xóa món này?')) return;
    const res    = await fetch(`${BASE}/foodController.php`, {
        method: 'DELETE', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ id })
    });
    const result = await res.json();
    alert(result.message);
    if (result.status) loadFoodAdmin();
}

// ── LỊCH CHIẾU ─────────────────────────────────────────────
async function loadSchedules() {
    const res  = await fetch(`${BASE}/schedules.php`);
    const data = await res.json();
    document.getElementById('tbody-schedule').innerHTML = data.map(s => `
        <tr>
            <td>${s.scheduleId}</td>
            <td>${s.tenPhim}</td>
            <td>${s.tenPhong}</td>
            <td>${s.ngayChieu}</td>
            <td>${s.gioChieu}</td>
            <td>${fmt(s.giaVe)}</td>
            <td>${s.trangThai}</td>
            <td>
                <button onclick='openScheduleDialog(${JSON.stringify(s)})'>Sửa</button>
                <button onclick="deleteSchedule(${s.scheduleId})">Xóa</button>
            </td>
        </tr>
    `).join('');
}

async function openScheduleDialog(sch) {
    document.getElementById('dlg-sch-title').textContent = sch ? 'Sửa lịch chiếu' : 'Thêm lịch chiếu';
    document.getElementById('sch-id').value   = sch?.scheduleId || '';
    document.getElementById('sch-ngay').value = sch?.ngayChieu  || '';
    document.getElementById('sch-gio').value  = sch?.gioChieu   || '';
    document.getElementById('sch-gia').value  = sch?.giaVe      || '';
    const [mRes, rRes] = await Promise.all([fetch(`${BASE}/movies.php`), fetch(`${BASE}/roomController.php`)]);
    const movies   = await mRes.json();
    const roomJson = await rRes.json();
    const rooms    = roomJson.data || roomJson;
    document.getElementById('sch-movie').innerHTML = movies.map(m =>
        `<option value="${m.movieId}" ${sch?.movieId==m.movieId?'selected':''}>${m.tenPhim}</option>`).join('');
    document.getElementById('sch-room').innerHTML  = rooms.map(r =>
        `<option value="${r.roomId}" ${sch?.roomId==r.roomId?'selected':''}>${r.tenPhong}</option>`).join('');
    document.getElementById('dlg-schedule').showModal();
}

document.getElementById('form-schedule').addEventListener('submit', async e => {
    e.preventDefault();
    const id      = document.getElementById('sch-id').value;
    const payload = {
        movieId: document.getElementById('sch-movie').value,
        roomId : document.getElementById('sch-room').value,
        ngayChieu: document.getElementById('sch-ngay').value,
        gioChieu : document.getElementById('sch-gio').value,
        giaVe    : document.getElementById('sch-gia').value,
        isCancelled: 0
    };
    const method = id ? 'PUT' : 'POST';
    const url    = id ? `${BASE}/schedules.php?id=${id}` : `${BASE}/schedules.php`;
    const res    = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    const result = await res.json();
    alert(result.message);
    if (!result.message?.includes('Lỗi') && !result.message?.includes('Trùng')) {
        document.getElementById('dlg-schedule').close(); loadSchedules();
    }
});

async function deleteSchedule(id) {
    if (!confirm('Xóa suất chiếu này?')) return;
    const res    = await fetch(`${BASE}/schedules.php?id=${id}`, { method: 'DELETE' });
    const result = await res.json();
    alert(result.message);
    if (result.message?.includes('thành công')) loadSchedules();
}

// ── DOANH THU ──────────────────────────────────────────────
function toggleDateInput() {
    const loai = document.getElementById('dt-loai').value;
    document.getElementById('dt-month').style.display = loai === 'month' ? '' : 'none';
    document.getElementById('dt-week').style.display  = loai === 'week'  ? '' : 'none';
}

async function loadDoanhThu() {
    const loai   = document.getElementById('dt-loai').value;
    const giaTri = loai === 'month'
        ? document.getElementById('dt-month').value
        : document.getElementById('dt-week').value.replace('W','');
    const res    = await fetch(`${BASE}/bookingController.php?action=doanhthu&loai=${loai}&gia_tri=${giaTri}`);
    const data   = await res.json();
    let tongVe = 0, tongFood = 0, tongAll = 0;
    document.getElementById('tbody-doanhthu').innerHTML = data.map(d => {
        tongVe += +d.doanhThuVe; tongFood += +d.doanhThuFood; tongAll += +d.tongDoanh;
        return `<tr>
            <td>${d.tenPhim}</td><td>${d.soBooking}</td><td>${d.soVe}</td>
            <td>${fmt(d.doanhThuVe)}</td><td>${fmt(d.doanhThuFood)}</td>
            <td><b>${fmt(d.tongDoanh)}</b></td>
        </tr>`;
    }).join('') || '<tr><td colspan="6">Không có dữ liệu</td></tr>';
    document.getElementById('tfoot-doanhthu').innerHTML =
        `<td><b>Tổng</b></td><td></td><td></td>
         <td><b>${fmt(tongVe)}</b></td><td><b>${fmt(tongFood)}</b></td><td><b>${fmt(tongAll)}</b></td>`;
}

// ── THỐNG KÊ PHÒNG ─────────────────────────────────────────
async function loadScheduleSelect() {
    const res  = await fetch(`${BASE}/schedules.php`);
    const data = await res.json();
    document.getElementById('phong-schedule').innerHTML = data.map(s =>
        `<option value="${s.scheduleId}">${s.tenPhim} — ${s.ngayChieu} ${s.gioChieu}</option>`).join('');
}

async function loadThongKePhong() {
    const scheduleId = document.getElementById('phong-schedule').value;
    if (!scheduleId) return;
    const res  = await fetch(`${BASE}/bookingController.php?action=thongke_phong&scheduleId=${scheduleId}`);
    const data = await res.json();
    document.getElementById('tbody-phong').innerHTML = data.map(p => `
        <tr>
            <td>${p.tenPhong}</td><td>${p.tongGhe}</td><td>${p.soGheDaDat}</td>
            <td>${p.tongGhe - p.soGheDaDat}</td><td>${p.soBooking}</td>
            <td>${fmt(p.doanhThuVe)}</td><td>${fmt(p.doanhThuFood)}</td>
            <td><b>${fmt(p.tongDoanhPhong)}</b></td>
        </tr>
    `).join('') || '<tr><td colspan="8">Không có dữ liệu</td></tr>';
}

// ── XUẤT EXCEL ─────────────────────────────────────────────
function exportTable(tableId, name) {
    const table = document.getElementById(tableId);
    const clone = table.cloneNode(true);
    // Xóa cột hành động (cột cuối)
    clone.querySelectorAll('tr').forEach(row => {
        const cells = row.querySelectorAll('th,td');
        if (cells.length > 0) cells[cells.length - 1].remove();
    });
    const html = `<html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8">
        <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>
        <x:ExcelWorksheet><x:Name>${name}</x:Name>
        <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
        </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        </head><body>${clone.outerHTML}</body></html>`;
    const blob = new Blob(['﻿' + html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const a    = Object.assign(document.createElement('a'), {
        href: URL.createObjectURL(blob), download: `${name}_${Date.now()}.xls`
    });
    a.click();
}

async function loadBranchOptions() {
    const res  = await fetch(`${BASE}/branchController.php`);
    const data = await res.json();
    // dùng trong dialog nếu cần
}
