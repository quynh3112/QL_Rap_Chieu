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
                tbody.innerHTML = json.data.map((f, index) => `
                    <tr>
                        <td>${f.tenFood}</td>
                        <td>${f.loaiFood}</td>
                        <td>${new Intl.NumberFormat().format(f.gia)}đ</td>
                        <td>${f.soLuongTon}</td>
                        <td style="color:${f.soLuongTon > 0 ? '#2ecc71' : '#e74c3c'}">${f.trangThai}</td>
                        <td>
                            <button onclick="editFood(${index})" style="color:cyan; background:none; border:1px solid cyan; cursor:pointer; padding:2px 5px;">Sửa</button>
                            <button onclick="deleteFood(${f.foodId})" style="color:red; background:none; border:1px solid red; cursor:pointer; padding:2px 5px; margin-left:5px;">Xoá</button>
                        </td>
                    </tr>
                `).join('');
            }
        }
    } catch (e) { console.error("Lỗi load món:", e); }
}

//2. load danh sách đơn hàng
async function loadOrders() {
    try {
        const res = await fetch('../../Controllers/foodController.php?action=list_orders');
        const json = await res.json();
        const container = document.getElementById('order-table-container');
        
        if (json.success) {
            if (json.data.length === 0) {
                container.innerHTML = '<p style="text-align:center; padding:50px; color:#666;">Chưa có đơn hàng nào.</p>';
                return;
            }

            container.innerHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>Mã Đơn</th><th>Khách Hàng</th><th>Ngày Đặt</th><th>Tổng Tiền</th><th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${json.data.map(o => `
                            <tr>
                                <td>#${o.foodOrderId}</td>
                                <td>${o.hoTen}</td>
                                <td>${o.ngayMua}</td>
                                <td style="color:gold; font-weight:bold;">${new Intl.NumberFormat().format(o.tongTienFood)}đ</td>
                                <td style="color:cyan;">${o.trangThai}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
    } catch (e) { console.error("Lỗi load đơn hàng:", e); }
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
    // 1. Phải khai báo biến 'data' trước khi dùng
    const data = {
        foodId: document.getElementById('input-foodId').value,
        tenFood: document.getElementById('input-tenFood').value,
        loaiFood: document.getElementById('input-loaiFood').value,
        gia: document.getElementById('input-gia').value,
        soLuongTon: document.getElementById('input-soLuongTon').value
    };

    // Kiểm tra nhanh xem đã nhập tên và giá chưa
    if (!data.tenFood || !data.gia) {
        return alert("Mày chưa nhập đủ tên món và giá kìa!");
    }

    try {
        // 2. Bây giờ mới dùng biến 'data' ở đây
        const res = await fetch('/QL_Rap_Chieu/Controllers/foodController.php?action=save', {
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
    document.getElementById('input-soLuongTon').value = '';
    document.getElementById('food-modal').style.display = 'flex';
}
function closeModal() { document.getElementById('food-modal').style.display = 'none'; }

window.onload = loadAdminData;