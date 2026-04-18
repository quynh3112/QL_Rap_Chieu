const BASE = '../../Controllers';

function getStoredUser() {
    const parseUser = (raw) => {
        if (!raw) {
            return null;
        }
        try {
            const parsed = JSON.parse(raw);
            return parsed && typeof parsed === 'object' ? parsed : null;
        } catch (e) {
            return null;
        }
    };

    const sessionUser = parseUser(sessionStorage.getItem('user'));
    if (sessionUser) {
        return sessionUser;
    }

    const localUser = parseUser(localStorage.getItem('user'));
    if (localUser) {
        sessionStorage.setItem('user', JSON.stringify(localUser));
    }

    return localUser;
}

const nvUser = getStoredUser();

// QR - sửa thông tin ngân hàng của bạn
const STK      = '1234567890';
const NGAN_HANG = 'MBBank';

const state = {
    tenKhach  : '',
    movie     : null,
    schedule  : null,
    seats     : [],
    foods     : {},
    allMovies : [],
};

document.addEventListener('DOMContentLoaded', () => {
    if (!nvUser) { window.location.href = 'login.php'; return; }
    if (nvUser.role !== 'Employee') { window.location.href = 'login.php'; return; }
    document.getElementById('nv-name').textContent = nvUser.hoTen;
    showTab('khach');
});

function nvLogout() {
    sessionStorage.removeItem('user');
    localStorage.removeItem('user');
    window.location.href = 'logout.php';
}

function showTab(name) {
    ['khach','movies','schedule','seat','food','confirm','history'].forEach(t => {
        document.getElementById('tab-' + t).style.display = 'none';
    });
    document.getElementById('tab-' + name).style.display = 'block';
    if (name === 'history') loadHistory();
    if (name === 'food')    loadFood();
    if (name === 'confirm') buildConfirm();
}

function fmt(n) { return Number(n).toLocaleString('vi-VN') + ' đ'; }

// BƯỚC 0
function goChonPhim() {
    state.tenKhach = document.getElementById('ten-khach').value.trim();
    loadMovies();
    showTab('movies');
}

// BƯỚC 1
async function loadMovies() {
    const res       = await fetch(`${BASE}/movies.php`);
    state.allMovies = await res.json();
    renderMovies(state.allMovies);
}

function filterMovies() {
    const keyword  = document.getElementById('search-movie').value.toLowerCase();
    const status   = document.getElementById('filter-status').value;
    renderMovies(state.allMovies.filter(m =>
        m.tenPhim.toLowerCase().includes(keyword) && (!status || m.trangThai === status)
    ));
}

function renderMovies(list) {
    document.getElementById('tbody-movies').innerHTML = list.map(m => `
        <tr>
            <td>${m.tenPhim}</td>
            <td>${m.daoDien || ''}</td>
            <td>${m.thoiLuong ? m.thoiLuong + ' phút' : ''}</td>
            <td>${m.trangThai}</td>
            <td><button onclick='selectMovie(${JSON.stringify(m).replace(/'/g,"&#39;")})'>Chọn</button></td>
        </tr>
    `).join('') || '<tr><td colspan="5">Không có phim</td></tr>';
}

function selectMovie(movie) {
    state.movie = movie;
    document.getElementById('title-movie').textContent = movie.tenPhim;
    loadSchedules(movie.movieId);
    showTab('schedule');
}

// BƯỚC 2
async function loadSchedules(movieId) {
    const res  = await fetch(`${BASE}/schedules.php?movieId=${movieId}`);
    const data = await res.json();
    document.getElementById('tbody-schedule').innerHTML = data.map(s => `
        <tr>
            <td>${s.tenPhong || ''}</td>
            <td>${s.ngayChieu}</td>
            <td>${s.gioChieu}</td>
            <td>${fmt(s.giaVe)}</td>
            <td><button onclick='selectSchedule(${JSON.stringify(s).replace(/'/g,"&#39;")})'>Chọn</button></td>
        </tr>
    `).join('') || '<tr><td colspan="5">Chưa có suất chiếu</td></tr>';
}

async function selectSchedule(sch) {
    state.schedule = sch;
    state.seats    = [];
    document.getElementById('title-schedule').textContent =
        `${state.movie.tenPhim} — ${sch.tenPhong || ''} — ${sch.ngayChieu} ${sch.gioChieu} — ${fmt(sch.giaVe)}/ghế`;
    await loadSeatMap(sch.roomId, sch.scheduleId);
    showTab('seat');
}

// BƯỚC 3
async function loadSeatMap(roomId, scheduleId) {
    const [seatRes, takenRes] = await Promise.all([
        fetch(`${BASE}/seatController.php?roomId=${roomId}`),
        fetch(`${BASE}/bookingController.php?action=ghe_da_dat&scheduleId=${scheduleId}`)
    ]);
    const seatJson = await seatRes.json();
        const allSeats = seatJson.data || seatJson;

    const takenIds = (await takenRes.json()).map(String);

    const rows = {};
    allSeats.forEach(s => {
        const r = s.tenGhe[0];
        if (!rows[r]) rows[r] = [];
        rows[r].push(s);
    });

    document.getElementById('seat-map').innerHTML = Object.keys(rows).sort().map(r => `
        <div style="display:flex;gap:4px;margin-bottom:4px;align-items:center">
            <span style="width:20px;text-align:center;font-weight:bold">${r}</span>
            ${rows[r].map(s => {
                const taken = takenIds.includes(String(s.seatId));
                const bg    = taken ? '#e74c3c' : s.loaiGhe === 'VIP' ? '#ffd700' : '#e0e0e0';
                const color = taken ? '#fff' : '#000';
                return `<button style="width:36px;height:28px;background:${bg};color:${color};border:1px solid #999;cursor:${taken?'not-allowed':'pointer'}"
                    data-ghe='${JSON.stringify(s)}' ${taken ? 'disabled' : ''} onclick="toggleSeat(this)">${s.tenGhe}</button>`;
            }).join('')}
        </div>
    `).join('');
    updateSeatSummary();
}

function toggleSeat(btn) {
    const ghe = JSON.parse(btn.dataset.ghe);
    const idx = state.seats.findIndex(s => s.seatId == ghe.seatId);
    if (idx >= 0) {
        state.seats.splice(idx, 1);
        btn.style.background = ghe.loaiGhe === 'VIP' ? '#ffd700' : '#e0e0e0';
        btn.style.color = '#000';
    } else {
        state.seats.push(ghe);
        btn.style.background = '#2ecc71';
    }
    updateSeatSummary();
}

function updateSeatSummary() {
    const total = state.seats.reduce((s, g) => s + (+g.giaGhe) + (+state.schedule?.giaVe || 0), 0);
    document.getElementById('selected-count').textContent = state.seats.length;
    document.getElementById('tmp-total').textContent      = total.toLocaleString('vi-VN');
    document.getElementById('btn-to-food').disabled       = state.seats.length === 0;
}

// BƯỚC 4
async function loadFood() {
    const res  = await fetch(`${BASE}/foodController.php?action=list_all`);
    const payload = await res.json();
    const data = payload && payload.success ? payload.data : [];

    document.getElementById('tbody-food').innerHTML = data.map(f => `
        <tr>
            <td>${f.tenFood}</td>
            <td>${f.loaiFood}</td>
            <td>${fmt(f.gia)}</td>
            <td>
                <button onclick="changeFood(${f.foodId},-1,this)" data-food='${JSON.stringify(f)}'>-</button>
                <span id="qty-${f.foodId}">${state.foods[f.foodId]?.qty || 0}</span>
                <button onclick="changeFood(${f.foodId},1,this)" data-food='${JSON.stringify(f)}'>+</button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="4">Không có đồ ăn</td></tr>';
    updateFoodTotal();
}

function changeFood(foodId, delta, btn) {
    const food = JSON.parse(btn.dataset.food);
    if (!state.foods[foodId]) state.foods[foodId] = { data: food, qty: 0 };

    const maxStock = Number(food.soLuongTon) || 0;
    if (delta > 0 && state.foods[foodId].qty >= maxStock) {
        alert('Số lượng chọn đã đạt giới hạn tồn kho.');
        return;
    }

    state.foods[foodId].qty = Math.max(0, state.foods[foodId].qty + delta);
    document.getElementById('qty-' + foodId).textContent = state.foods[foodId].qty;
    updateFoodTotal();
}

function updateFoodTotal() {
    const total = Object.values(state.foods).reduce((s, f) => s + f.data.gia * f.qty, 0);
    document.getElementById('food-total').textContent = total.toLocaleString('vi-VN');
}

// BƯỚC 5
function buildConfirm() {
    const tongVe   = state.seats.reduce((s, g) => s + (+g.giaGhe) + (+state.schedule.giaVe), 0);
    const tongFood = Object.values(state.foods).reduce((s, f) => s + f.data.gia * f.qty, 0);
    const tongAll  = tongVe + tongFood;
    const noiDung  = `DATVE KH ${state.tenKhach || nvUser.hoTen}`;

    const gheList  = state.seats.map(s => `${s.tenGhe} (${s.loaiGhe}) — ${fmt(+s.giaGhe + +state.schedule.giaVe)}`).join('<br>');
    const foodList = Object.values(state.foods).filter(f => f.qty > 0)
        .map(f => `${f.data.tenFood} x${f.qty} — ${fmt(f.data.gia * f.qty)}`).join('<br>');

    document.getElementById('confirm-detail').innerHTML = `
        <p><b>Khách hàng:</b> ${state.tenKhach || '(Không có tên)'}</p>
        <p><b>Phim:</b> ${state.movie.tenPhim}</p>
        <p><b>Suất chiếu:</b> ${state.schedule.ngayChieu} ${state.schedule.gioChieu} — ${state.schedule.tenPhong || ''}</p>
        <p><b>Ghế:</b><br>${gheList}</p>
        ${foodList ? `<p><b>Đồ ăn:</b><br>${foodList}</p>` : ''}
        <p><b>Tổng tiền vé:</b> ${fmt(tongVe)}</p>
        <p><b>Tổng đồ ăn:</b> ${fmt(tongFood)}</p>
    `;
    document.getElementById('confirm-total').textContent = tongAll.toLocaleString('vi-VN');

    // Hiện QR
    document.getElementById('qr-info').innerHTML =
        `Ngân hàng: <b>${NGAN_HANG}</b><br>STK: <b>${STK}</b><br>Nội dung: <b>${noiDung}</b>`;
    document.getElementById('qr-img').src =
        `https://img.vietqr.io/image/${NGAN_HANG}-${STK}-compact2.png?amount=${tongAll}&addInfo=${encodeURIComponent(noiDung)}`;
}

async function xacNhanDatVe() {
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.textContent = 'Đang xử lý...';
    try {
        const bRes = await fetch(`${BASE}/bookingController.php`, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body   : JSON.stringify({
                accountId  : nvUser.accountId,
                scheduleId : state.schedule.scheduleId,
                seatIds    : state.seats.map(s => s.seatId),
                tenKhach   : state.tenKhach || null,
                trangThai  : 'Đã xác nhận'   // nhân viên xác nhận luôn
            })
        });
        const bResult = await bRes.json();
        if (!bResult.status) {
            if (bResult.gheTrung) {
                alert(`Ghế ${bResult.gheTrung.join(', ')} vừa bị đặt!\nVui lòng chọn lại.`);
                state.seats = [];
                await loadSeatMap(state.schedule.roomId, state.schedule.scheduleId);
                showTab('seat');
            } else { alert(bResult.message); }
            return;
        }
        const bookingId = bResult.bookingId;
        const foodItems = Object.values(state.foods).filter(f => f.qty > 0);
        let foodWarn = '';

        if (foodItems.length > 0) {
            const foodRes = await fetch(`${BASE}/foodController.php?action=place_order`, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body   : JSON.stringify({
                    items: foodItems.map(f => ({
                        foodId: f.data.foodId,
                        soLuong: f.qty
                    }))
                })
            });

            const foodResult = await foodRes.json();
            if (!foodRes.ok || !foodResult || foodResult.success !== true) {
                foodWarn = foodResult && foodResult.message
                    ? `\nTuy nhiên đặt đồ ăn thất bại: ${foodResult.message}`
                    : '\nTuy nhiên đặt đồ ăn thất bại.';
            }
        }

        alert(`Đặt vé thành công! Mã booking: #${bookingId}${foodWarn}`);
        state.tenKhach = ''; state.seats = []; state.foods = {};
        document.getElementById('ten-khach').value = '';
        showTab('khach');
    } catch (err) {
        alert('Lỗi kết nối server.');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Xác nhận đã thanh toán & Đặt vé';
    }
}

// LỊCH SỬ
async function loadHistory() {
    const res  = await fetch(`${BASE}/bookingController.php?accountId=${nvUser.accountId}`);
    const data = await res.json();
    document.getElementById('tbody-history').innerHTML = data.map(b => `
        <tr>
            <td>${b.bookingId}</td>
            <td>${b.tenKhach || '-'}</td>
            <td>${b.tenPhim}</td>
            <td>${b.tenPhong}</td>
            <td>${b.ngayChieu}</td>
            <td>${b.gioChieu}</td>
            <td>${b.soLuong}</td>
            <td>${b.trangThai}</td>
        </tr>
    `).join('') || '<tr><td colspan="8">Chưa có dữ liệu</td></tr>';
}
