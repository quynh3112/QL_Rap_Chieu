document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const messageDiv = document.getElementById('message');

    registerForm.addEventListener('submit', async (e) => {
        // Ngăn chặn form tự động load lại trang
        e.preventDefault();

        // 1. Lấy dữ liệu từ các ô input
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const hoTen = document.getElementById('hoTen').value.trim();
        const email = document.getElementById('email').value.trim();
        const sdt = document.getElementById('sdt').value.trim();
        const role = document.getElementById('role').value;
        const branchId = document.getElementById('branchId').value;

        // 2. Đóng gói dữ liệu thành JSON
        const bodyData = {
            action: 'register', // Để phân biệt với login ở Controller
            username: username,
            password: password,
            hoTen: hoTen,
            email: email,
            sdt: sdt,
            role: role,
            branchId: branchId === "null" ? null : parseInt(branchId)
        };

        try {
            // 3. Gọi API (Lưu ý sửa lại đường dẫn cho đúng với project của bạn)
            const res = await fetch("../../Controllers/accountController.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(bodyData)
            });

            const result = await res.json();

            // 4. Xử lý hiển thị thông báo
            if (result.status === true) {
                messageDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                registerForm.reset(); // Xóa trắng form sau khi thành công
            } else {
                messageDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
            }

        } catch (err) {
            console.error("Lỗi khi đăng ký:", err);
            messageDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối đến máy chủ! Vui lòng thử lại.</div>`;
        }
    });
});