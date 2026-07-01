let listFoodGlobal = []; 
//1. load dsach 
async function loadAdminData() {
    try {
        const res = await fetch('../../Controllers/foodController.php?action=list_all');
        const json = await res.json();
        if (json.success) {
            listFoodGlobal = json.data;
            const tbody = document.getElementById('tbody-food');
            if (tbody) {
                tbody.innerHTML = json.data.map((f, index) => {
                    const stock = Number(f.soLuongTon) || 0;
                    const status = stock > 0 ? 'Còn' : 'Hết';

                    return `
                    <tr>
                        <td>${f.tenFood}</td>
                        <td>${f.loaiFood}</td>
                        <td>${new Intl.NumberFormat().format(f.gia)}đ</td>
                        <td>${stock}</td>
                        <td style="color:${stock > 0 ? '#2ecc71' : '#e74c3c'}">${status}</td>
                        <td>
                            <button onclick="editFood(${index})" style="color:cyan; background:none; border:1px solid cyan; cursor:pointer; padding:2px 5px;">Sửa</button>
                            <button onclick="deleteFood(${f.foodId})" style="color:red; background:none; border:1px solid red; cursor:pointer; padding:2px 5px; margin-left:5px;">Xoá</button>
                        </td>
                    </tr>
                `;
                }).join('');
            }
        }
    } catch (e) { console.error("Lỗi load món:", e); }
}

//2. load danh sách đơn hàng
async function loadOrders() {
    try {

        const res = await fetch('../../Controllers/paymentController.php?action=list_pending');
        const json = await res.json();
        const container = document.getElementById('order-table-container');
        
        if (!json.success) {
            container.innerHTML = `<p style="text-align:center; padding:50px; color:#e74c3c;">${json.message || 'Không thể tải danh sách thanh toán.'}</p>`;
            return;
        }

        const payload = json.data || {};
        const payments = Array.isArray(payload.payments) ? payload.payments : [];
        const currentRole = payload.currentRole || window.currentUserRole || '';
        const canReview = currentRole === 'Admin' || currentRole === 'Manager';

        if (payments.length === 0) {
            container.innerHTML = '<p style="text-align:center; padding:50px; color:#666;">Không có thanh toán chờ xác nhận.</p>';
            return;
        }

        container.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>Mã TT</th>
                        <th>Khách Hàng</th>
                        <th>Mã Đồ Ăn</th>
                        <th>Mã Booking</th>
                        <th>Phương Thức</th>
                        <th>Tổng Tiền</th>
                        <th>TT Thanh Toán</th>
                        <th>TT Đồ Ăn</th>
                        <th>TT Booking</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    ${payments.map(o => `
                        <tr>
                            <td>#${o.paymentId}</td>
                            <td>${o.customerName || 'Không rõ'}</td>
                            <td>${o.foodOrderId ? '#' + o.foodOrderId : '-'}</td>
                            <td>${o.bookingId ? '#' + o.bookingId : '-'}</td>
                            <td>${o.phuongThuc || '-'}</td>
                            <td style="color:gold; font-weight:bold;">${new Intl.NumberFormat().format(o.tongTien || 0)}đ</td>
                            <td style="color:#f5c518;">${o.paymentStatus || '-'}</td>
                            <td style="color:cyan;">${o.foodOrderStatus || '-'}</td>
                            <td style="color:#9b59b6;">${o.bookingStatus || '-'}</td>
                            <td>
                                ${o.paymentStatus === 'Chờ xác nhận' && canReview ? `
                                    <button onclick="approvePayment(${o.paymentId})" style="color:#2ecc71; background:none; border:1px solid #2ecc71; cursor:pointer; padding:2px 6px; margin-right:5px;">Duyệt</button>
                                    <button onclick="cancelPayment(${o.paymentId})" style="color:#e74c3c; background:none; border:1px solid #e74c3c; cursor:pointer; padding:2px 6px;">Hủy</button>
                                ` : '<span style="color:#888;">-</span>'}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    } catch (e) { console.error("Lỗi load đơn hàng:", e); }
}

async function approvePayment(paymentId) {
    await submitPaymentAction('approve', paymentId, 'duyệt');
}

async function cancelPayment(paymentId) {
    await submitPaymentAction('cancel', paymentId, 'hủy');
}

async function submitPaymentAction(action, paymentId, actionText) {
    if (!confirm(`Bạn có chắc muốn ${actionText} thanh toán #${paymentId}?`)) {
        return;
    }

    try {
        const res = await fetch(`../../Controllers/paymentController.php?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ paymentId })
        });

        const result = await res.json();
        alert(result.message || (result.success ? 'Thao tác thành công.' : 'Thao tác thất bại.'));

        if (result.success) {
            loadOrders();
        }
    } catch (e) {
        console.error('Lỗi cập nhật trạng thái thanh toán:', e);
        alert('Không thể kết nối đến server để cập nhật trạng thái thanh toán.');
    }
}

// 3. chuyển tab
function switchTab(tab) {
    // ẩn/ hiện Section
    document.querySelectorAll('.admin-section').forEach(s => s.style.display = 'none');
    document.getElementById('section-' + tab).style.display = 'block';
    
    // đổi màu nút Tab
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');

    // Load dữ liệu tương ứng
    if (tab === 'order') {
        loadOrders();
    } else {
        loadAdminData();
    }
}

//4. thao tác món ăn
async function deleteFood(id) {
    if (confirm("Xoá món này là không lấy lại được đâu mày?")) {
        const res = await fetch(`/QL_Rap_Chieu/Controllers/foodController.php?action=delete&foodId=${id}`);
        const result = await res.json();
        
        if (result.success) { 
            alert("Đã xoá!"); 
            loadAdminData(); 
        } else {
            //thông báo phân quyền lỗi
            alert("LỖI: " + (result.message || result.status)); 
        }
    }
}

function editFood(index) {
    const f = listFoodGlobal[index];
    document.getElementById('modal-title').innerText = "CẬP NHẬT MÓN";
    document.getElementById('food-modal').style.display = 'flex';
    document.getElementById('input-foodId').value = f.foodId;
    document.getElementById('input-tenFood').value = f.tenFood;
    document.getElementById('input-loaiFood').value = f.loaiFood;
    document.getElementById('input-gia').value = f.gia;
    document.getElementById('input-soLuongTon').value = f.soLuongTon;
}

async function saveFood() {
    const data = {
        foodId: document.getElementById('input-foodId').value,
        tenFood: document.getElementById('input-tenFood').value,
        loaiFood: document.getElementById('input-loaiFood').value,
        gia: document.getElementById('input-gia').value,
        soLuongTon: document.getElementById('input-soLuongTon').value
    };

    const tenFood = (data.tenFood || '').trim();
    const gia = Number(data.gia);
    const soLuongTon = Number(data.soLuongTon);

    if (!tenFood) {
        return alert("Vui lòng nhập tên món.");
    }

    if (!Number.isFinite(gia) || gia <= 0) {
        return alert("Giá món phải lớn hơn 0.");
    }

    if (!Number.isInteger(soLuongTon) || soLuongTon < 0) {
        return alert("Số lượng tồn phải là số nguyên từ 0 trở lên.");
    }

    data.tenFood = tenFood;
    data.gia = gia;
    data.soLuongTon = soLuongTon;

    try {
        const res = await fetch('../../Controllers/foodController.php?action=save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data) 
        });

        const result = await res.json();
        if (result.success) {
            alert("Thành công rồi nhé!");
            closeModal();
            loadAdminData(); 
        } else {
            alert("Lỗi từ server: " + result.message);
        }
    } catch (e) {
        console.error("Lỗi fetch:", e);
        alert("Có lỗi xảy ra khi kết nối server!");
    }
}

function openModal() {
    document.getElementById('modal-title').innerText = "THÊM MÓN MỚI";
    document.getElementById('input-foodId').value = '';
    document.getElementById('input-tenFood').value = '';
    document.getElementById('input-gia').value = '';
    document.getElementById('input-soLuongTon').value = '0';
    document.getElementById('food-modal').style.display = 'flex';
}
function closeModal() { document.getElementById('food-modal').style.display = 'none'; }

window.onload = loadAdminData;