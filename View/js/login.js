document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const action = document.getElementById('action').value;

        const bodyData = {
            action: action,
            email: email,
            password: password
        };

        try {
            const res = await fetch("../../Controllers/accountController.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(bodyData)
            });

            const result = await res.json();

            if (result.status === true) {
                messageDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                
                // Lấy thông tin user từ kết quả API trả về
                const user = result.user;

                // THỰC HIỆN PHÂN QUYỀN CHUYỂN TRANG
                setTimeout(() => {
                    if (user.role === "Customer") {
                        // Chuyển đến trang chủ rạp phim cho khách hàng
                        window.location.href = "home.php"; 
                    } else if (["Admin", "Manager", "Employee"].includes(user.role)) {
                        // Chuyển đến trang quản lý cho các vai trò nhân sự
                        window.location.href = "homeQL.php";
                    }
                }, 1000); // Đợi 1 giây để người dùng kịp thấy thông báo thành công

            } else {
                messageDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
            }

        } catch (err) {
            console.error("Lỗi đăng nhập:", err);
            messageDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối máy chủ!</div>`;
        }
    });
});