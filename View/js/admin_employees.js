let allEmployees = []; 
const empModalElement = document.getElementById('employeeModal');
const employeeModal = empModalElement ? new bootstrap.Modal(empModalElement) : null;
const employeeForm = document.getElementById('employeeForm');

document.addEventListener('DOMContentLoaded', () => {
    loadEmployees();
});

// Lấy dữ liệu từ Backend
async function loadEmployees() {
    try {
        const res = await fetch("../../Controllers/accountController.php");
        const data = await res.json();
        allEmployees = data;
        renderTable(allEmployees);
    } catch (err) {
        console.error("Lỗi: ", err);
    }
}

// Hiển thị bảng dữ liệu
function renderTable(data) {
    let html = '';
    data.forEach(emp => {
        html += `
            <tr>
                <td>${emp.accountId}</td>
                <td><strong>${emp.hoTen}</strong></td>
                <td>${emp.username}</td>
                <td>
                    <div class="small">${emp.email}</div>
                    <div class="small text-muted">${emp.sdt || ''}</div>
                </td>
                <td><span class="badge ${getRoleBadge(emp.role)}">${emp.role}</span></td>
                <td>${emp.branchId || 'Trống'}</td>
                
                ${USER_IS_ADMIN ? `
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick='editEmployee(${JSON.stringify(emp)})'>Sửa</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEmployee(${emp.accountId})">Xóa</button>
                    </div>
                </td>` : ''}
            </tr>
        `;
    });
    document.getElementById('employeeTableBody').innerHTML = html;
}

// Hàm tìm kiếm
function filterEmployees() {
    const s = document.getElementById('searchName').value.toLowerCase();
    const r = document.getElementById('filterRole').value;

    const filtered = allEmployees.filter(emp => {
        const matchText = emp.hoTen.toLowerCase().includes(s) || emp.username.toLowerCase().includes(s);
        const matchRole = r === "" || emp.role === r;
        return matchText && matchRole;
    });
    renderTable(filtered);
}

function resetFilter() {
    document.getElementById('searchName').value = '';
    document.getElementById('filterRole').value = '';
    renderTable(allEmployees);
}

function getRoleBadge(role) {
    if(role === 'Admin') return 'bg-danger';
    if(role === 'Manager') return 'bg-success';
    return 'bg-primary';
}

// Các chức năng dành riêng cho Admin
function openAddModal() {
    if(!employeeForm) return;
    employeeForm.reset();
    document.getElementById('emp_accountId').value = '';
    document.getElementById('modalTitle').innerText = "Thêm nhân viên mới";

    // Thay đổi thông báo cho trường hợp THÊM
    const passwordInput = document.getElementById('emp_password');
    const passwordNote = document.getElementById('passwordNote');
    
    passwordInput.placeholder = "Nhập mật khẩu (bắt buộc)";
    passwordNote.innerText = "Vui lòng đặt mật khẩu khởi tạo cho nhân viên.";
    passwordInput.required = true; // Bắt buộc phải nhập khi thêm mới
    employeeModal.show();
}

function editEmployee(emp) {
    if(!employeeModal) return;
    document.getElementById('modalTitle').innerText = "Cập nhật thông tin & Mật khẩu";

    document.getElementById('modalTitle').innerText = "Cập nhật thông tin & Mật khẩu";
    document.getElementById('emp_accountId').value = emp.accountId;
    document.getElementById('emp_hoTen').value = emp.hoTen;
    document.getElementById('emp_username').value = emp.username;
    document.getElementById('emp_email').value = emp.email;
    document.getElementById('emp_sdt').value = emp.sdt;
    document.getElementById('emp_role').value = emp.role;
    document.getElementById('emp_branchId').value = emp.branchId;

    // Thay đổi thông báo cho trường hợp SỬA
    const passwordInput = document.getElementById('emp_password');
    const passwordNote = document.getElementById('passwordNote');

    passwordInput.value = ''; // Luôn để trống ô mật khẩu khi mở form sửa
    passwordInput.placeholder = "Nhập mật khẩu mới (để trống nếu không muốn đổi)";
    passwordNote.innerText = "Để trống nếu không muốn thay đổi mật khẩu hiện tại.";
    passwordInput.required = false; // Không bắt buộc khi sửa
    
    employeeModal.show();
}

async function deleteEmployee(id) {
    if(confirm('Bạn có chắc chắn muốn xóa không?')) {
        const res = await fetch("../../Controllers/accountController.php", {
            method: 'DELETE',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ accountId: id })
        });
        const result = await res.json();
        alert(result.message);
        loadEmployees();
    }
}

if (employeeForm) {
    employeeForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Ngăn trang web bị load lại

        const id = document.getElementById('emp_accountId').value;
        const password = document.getElementById('emp_password').value;

        // Chuẩn bị dữ liệu gửi đi
        const bodyData = {
            action: id ? 'update' : 'create',
            accountId: id,
            username: document.getElementById('emp_username').value,
            hoTen: document.getElementById('emp_hoTen').value,
            email: document.getElementById('emp_email').value,
            sdt: document.getElementById('emp_sdt').value,
            role: document.getElementById('emp_role').value,
            branchId: document.getElementById('emp_branchId').value,
            password: password // Gửi mật khẩu (có thể trống nếu là update)
        };

        try {
            const method = id ? 'PUT' : 'POST';

            const res = await fetch("../../Controllers/accountController.php", {
                method: method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(bodyData)
            });

            const result = await res.json();
            
            if (result.status) {
                alert(result.message);
                employeeModal.hide(); // Đóng cửa sổ nhập liệu
                loadEmployees();      // Tải lại bảng danh sách
            } else {
                alert("Lỗi: " + result.message);
            }
        } catch (err) {
            console.error("Lỗi khi gửi dữ liệu:", err);
            alert("Không thể kết nối đến máy chủ.");
        }
    });
}