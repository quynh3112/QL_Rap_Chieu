const BASE = '../../Controllers';

// Thông tin thanh toán - SỬA STK VÀ NGÂN HÀNG CỦA BẠN
const STK       = '1234567890';
const NGAN_HANG = 'MBBank';

const state = {
    user      : JSON.parse(localStorage.getItem('user') || 'null'),
    movie     : null,
    schedule  : null,
    seats     : [],
    foods     : {},
    allMovies : [],
};

document.addEventListener('DOMContentLoaded', () => {
    if (!state.user) { window.location.href = 'login.php'; return; }
    document.getElementById('nav-user').textContent = 'Xin chào, ' + state.user.hoTen;
    loadMovies();
});

function showTab(name) {
    document.querySelectorAll('.tab').forEach(t => t.classList.add('hidden'));
    document.getElementById('tab-' + name).classList.remove('hidden');
    if (name === 'history') loadHistory();
    if (name === 'food')    loadFood();
    if (name === 'confirm') buildConfirm();
}

function fmt(n) { return Number(n).toLocaleString('vi-VN') + ' đ'; }

// ── BƯỚC 1: PHIM ────────────────────────────────────────────
async function loadMovies() {
    const res       = await fetch(`${BASE}/movies.php`);
    state.allMovies = await res.json();
    renderMovies(state.allMovies);
}

function filterMovies() {
    const keyword  = document.getElementById('search-movie').value.toLowerCase();
    const status   = document.getElementById('filter-status').value;
    const filtered = state.allMovies.filter(m =>
        m.tenPhim.toLowerCase().includes(keyword) &&
        (!status || m.trangThai === status)
    );
    renderMovies(filtered);
}

function renderMovies(list) {
    document.getElementById('movie-list').innerHTML = list.map(m => `
        <div class="card" onclick="selectMovie(${JSON.stringify(m).replace(/"/g,'&quot;')})">
            <img src="../../uploads/${m.img || ''}" onerror="this.style.display='none'" style="width:100%;height:120px;object-fit:cover"/>
            <div style="padding:6px">
                <strong>${m.tenPhim}</strong><br/>
                <small>${m.trangThai}</small><br/>
                <small>${m.daoDien || ''}</small><br/>
                <small>${m.thoiLuong ? m.thoiLuong + ' phút' : ''}</small>
            </div>
        </div>
    `).join('') || '<p>Không có phim nào.</p>';
}

function selectMovie(movie) {
    state.movie = movie;
    document.getElementById('title-movie').textContent = movie.tenPhim;
    loadSchedulesForMovie(movie.movieId);
    showTab('schedule');
}

// ── BƯỚC 2: SUẤT CHIẾU ──────────────────────────────────────
async function loadSchedulesForMovie(movieId) {
    const res  = await fetch(`${BASE}/schedules.php?movieId=${movieId}`);
    const data = await res.json();

    // 👉 LẤY THÔNG TIN PHIM từ state
    const m = state.movie;

    let html = `
        <div style="
            display:flex;
            gap:20px;
            background: rgba(255,255,255,0.1);
            padding:15px;
            border-radius:12px;
            margin-bottom:20px;
        ">
            <img src="${m.img || ''}" 
                 onerror="this.style.display='none'"
                 style="width:150px;height:200px;object-fit:cover;border-radius:10px">

            <div>
                <h2>${m.tenPhim}</h2>
                <p><b>⏱ Thời lượng:</b> ${m.thoiLuong || 'N/A'} phút</p>
                <p><b>🎬 Đạo diễn:</b> ${m.daoDien || 'N/A'}</p>
                <p><b>🎭 Diễn viên:</b> ${m.dienVien || 'N/A'}</p>
                <p><b>📅 Năm:</b> ${m.namSanXuat || 'N/A'}</p>
                <p><b>📌 Trạng thái:</b> ${m.trangThai}</p>
            </div>
        </div>

        <h3>Chọn rạp và suất chiếu</h3>
    `;

    if (!Array.isArray(data) || data.length === 0) {
        html += '<p>Chưa có suất chiếu.</p>';
        document.getElementById('schedule-list').innerHTML = html;
        return;
    }

    // 👉 group theo phòng
    const grouped = {};
    data.forEach(s => {
        const key = s.tenPhong || 'Chưa rõ phòng';
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(s);
    });

    html += Object.keys(grouped).map(phong => `
        <div>
            <h4>${phong}</h4>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                ${grouped[phong].map(s => `
                    <button onclick='selectSchedule(${JSON.stringify(s)})'>
                        ${s.ngayChieu} ${s.gioChieu}<br/>
                        <small>${fmt(s.giaVe)}</small>
                    </button>
                `).join('')}
            </div>
        </div>
    `).join('');

    document.getElementById('schedule-list').innerHTML = html;
}

async function selectSchedule(sch) {
    state.schedule = sch;
    state.seats    = [];
    document.getElementById('title-schedule').textContent =
        `${state.movie.tenPhim} — ${sch.tenPhong || ''} — ${sch.ngayChieu} ${sch.gioChieu} — ${fmt(sch.giaVe)}/ghế`;
    await loadSeatMap(sch.roomId, sch.scheduleId);
    showTab('seat');
}

// ── BƯỚC 3: GHẾ ─────────────────────────────────────────────
async function loadSeatMap(roomId, scheduleId) {
    const [seatRes, takenRes] = await Promise.all([
        fetch(`${BASE}/seatController.php?roomId=${roomId}`),
        fetch(`${BASE}/bookingController.php?action=ghe_da_dat&scheduleId=${scheduleId}`)
    ]);

    const seatJson = await seatRes.json();

    const allSeats = seatJson.data || seatJson;
    const takenRaw = await takenRes.json();
    // Ép kiểu về string để so sánh chắc chắn
    const takenIds = (Array.isArray(takenRaw) ? takenRaw : []).map(String);

    const rows = {};
    allSeats.forEach(s => {
        const row = s.tenGhe[0];
        if (!rows[row]) rows[row] = [];
        rows[row].push(s);
    });

    document.getElementById('seat-map').innerHTML = Object.keys(rows).sort().map(row => `
        <div class="seat-row">
            <span class="row-label">${row}</span>
            ${rows[row].map(s => {
                const taken = takenIds.includes(String(s.seatId));
                const cls   = taken ? 'seat-taken' : s.loaiGhe === 'VIP' ? 'seat-vip' : 'seat-std';
                return `<button class="seat ${cls}" data-ghe='${JSON.stringify(s)}'
                            ${taken ? 'disabled' : ''} onclick="toggleSeat(this)">${s.tenGhe}</button>`;
            }).join('')}
        </div>
    `).join('');

    updateSeatSummary();
}

function toggleSeat(btn) {
    if (btn.disabled) return;
    const ghe = JSON.parse(btn.dataset.ghe);
    const idx = state.seats.findIndex(s => s.seatId == ghe.seatId);
    if (idx >= 0) {
        state.seats.splice(idx, 1);
        btn.classList.remove('seat-selected');
        btn.classList.add(ghe.loaiGhe === 'VIP' ? 'seat-vip' : 'seat-std');
    } else {
        state.seats.push(ghe);
        btn.classList.remove('seat-vip', 'seat-std');
        btn.classList.add('seat-selected');
    }
    updateSeatSummary();
}

function updateSeatSummary() {
    const total = state.seats.reduce((s, g) => s + (+g.giaGhe) + (+state.schedule?.giaVe || 0), 0);
    document.getElementById('selected-count').textContent = state.seats.length;
    document.getElementById('tmp-total').textContent      = total.toLocaleString('vi-VN');
    document.getElementById('btn-to-food').disabled       = state.seats.length === 0;
}

// ── BƯỚC 4: ĐỒ ĂN ───────────────────────────────────────────
async function loadFood() {
    const res  = await fetch(`${BASE}/foodController.php?action=list_all`);
    const data = await res.json();

    console.log("FOOD:", data);

    const list = data.data || []; // 👈 vì backend trả { success, data }

    document.getElementById('food-list').innerHTML = list.map(f => `
        <div class="card">
            <div style="padding:6px">
                <strong>${f.tenFood}</strong><br/>
                <small>${f.loaiFood}</small><br/>
                <span>${fmt(f.gia)}</span>
                <div class="qty-row">
                    <button onclick="changeFood(${f.foodId},-1,this)" data-food='${encodeURIComponent(JSON.stringify(f))}'>-</button>
                    <span id="qty-${f.foodId}">${state.foods[f.foodId]?.qty || 0}</span>
                    <button onclick="changeFood(${f.foodId},1,this)" data-food='${encodeURIComponent(JSON.stringify(f))}'>+</button>
                </div>
            </div>
        </div>
    `).join('') || '<p>Không có đồ ăn.</p>';

    updateFoodTotal();
}

function changeFood(foodId, delta, btn) {
    const raw = btn.dataset.food;

    const food = JSON.parse(decodeURIComponent(raw)); // 👈 FIX

    if (!state.foods[foodId]) state.foods[foodId] = { data: food, qty: 0 };
    state.foods[foodId].qty = Math.max(0, state.foods[foodId].qty + delta);

    document.getElementById('qty-' + foodId).textContent = state.foods[foodId].qty;
    updateFoodTotal();
}

function updateFoodTotal() {
    const total = Object.values(state.foods).reduce((s, f) => s + f.data.gia * f.qty, 0);
    document.getElementById('food-total').textContent = total.toLocaleString('vi-VN');
}

// ── BƯỚC 5: XÁC NHẬN ────────────────────────────────────────
function buildConfirm() {
    const tongVe   = state.seats.reduce((s, g) => s + (+g.giaGhe) + (+state.schedule.giaVe), 0);
    const tongFood = Object.values(state.foods).reduce((s, f) => s + f.data.gia * f.qty, 0);

    const gheList  = state.seats.map(s =>
        `${s.tenGhe} (${s.loaiGhe}) — ${fmt(+s.giaGhe + +state.schedule.giaVe)}`
    ).join('<br>');
    const foodList = Object.values(state.foods).filter(f => f.qty > 0)
        .map(f => `${f.data.tenFood} x${f.qty} — ${fmt(f.data.gia * f.qty)}`).join('<br>');

    document.getElementById('confirm-detail').innerHTML = `
        <p><strong>Phim:</strong> ${state.movie.tenPhim}</p>
        <p><strong>Suất chiếu:</strong> ${state.schedule.ngayChieu} ${state.schedule.gioChieu} — ${state.schedule.tenPhong || ''}</p>
        <p><strong>Ghế đã chọn:</strong><br>${gheList}</p>
        ${foodList ? `<p><strong>Đồ ăn:</strong><br>${foodList}</p>` : ''}
        <p><strong>Tổng tiền vé:</strong> ${fmt(tongVe)}</p>
        <p><strong>Tổng tiền đồ ăn:</strong> ${fmt(tongFood)}</p>
    `;
    document.getElementById('confirm-total').textContent = (tongVe + tongFood).toLocaleString('vi-VN');


}

async function submitBooking() {
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.textContent = 'Đang xử lý...';

    try {
        const res = await fetch(`${BASE}/bookingController.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accountId: state.user?.accountId,
                scheduleId: state.schedule?.scheduleId,
                seatIds: state.seats.map(s => s.seatId),
                trangThai: 'Đã xác nhận'
            })
        });

        // 🔥 đọc text trước để debug
        const text = await res.text();
        console.log("BOOKING RAW:", text);

        let bResult;
        try {
            bResult = JSON.parse(text);
        } catch (e) {
            alert("Server trả về lỗi (không phải JSON)");
            console.error("JSON parse lỗi:", e);
            return;
        }

        // ❌ check HTTP status
        if (!res.ok) {
            alert("Server lỗi HTTP: " + res.status);
            console.error(bResult);
            return;
        }

        if (!bResult.status) {
            if (bResult.gheTrung) {
                alert(`Ghế ${bResult.gheTrung.join(', ')} vừa bị người khác đặt!`);
                state.seats = [];
                await loadSeatMap(state.schedule.roomId, state.schedule.scheduleId);
                showTab('seat');
            } else {
                alert(bResult.message || "Đặt vé thất bại");
                console.error(bResult);
            }
            return;
        }

        const bookingId = bResult.bookingId;

        // ── ĐẶT ĐỒ ĂN ─────────────────────
        const foodItems = Object.values(state.foods).filter(f => f.qty > 0);

        if (foodItems.length > 0) {
            const foodRes = await fetch(`${BASE}/foodController.php?action=place_order`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    items: foodItems.map(f => ({
                        foodId: f.data.foodId,
                        soLuong: f.qty
                    }))
                })
            });

            const foodText = await foodRes.text();
            console.log("FOOD RAW:", foodText);
        }

        alert(`Đặt vé thành công! Mã booking: #${bookingId}`);

        state.seats = [];
        state.foods = {};
        showTab('movies');

    } catch (err) {
        console.error("FETCH ERROR:", err);
        alert("Không kết nối được server");
    } finally {
        btn.disabled = false;
        btn.textContent = 'Xác nhận đặt vé';
    }
}
// ── LỊCH SỬ ─────────────────────────────────────────────────
async function loadHistory() {
    const res  = await fetch(`${BASE}/bookingController.php?accountId=${state.user.accountId}`);
    const data = await res.json();
    document.getElementById('tbody-history').innerHTML = data.map(b => `
        <tr>
            <td>${b.bookingId}</td>
            <td>${b.tenPhim}</td>
            <td>${b.tenPhong}</td>
            <td>${b.ngayChieu}</td>
            <td>${b.gioChieu}</td>
            <td>${b.soLuong}</td>
            <td>${b.trangThai}</td>
        </tr>
    `).join('') || '<tr><td colspan="7">Chưa có lịch sử đặt vé</td></tr>';
}

function logout() {
   localStorage.removeItem('user');
    window.location.href = 'login.php';
}
