document.addEventListener('DOMContentLoaded', () => {
    const updateForm = document.getElementById('employeeForm');

    if (updateForm) {
        updateForm.onsubmit = async (e) => {
            e.preventDefault();

            // Thu thập dữ liệu từ form
            const data = {
                accountId: document.getElementById('emp_accountId').value,
                hoTen: document.getElementById('emp_hoTen').value,
                username: document.getElementById('emp_username').value,
                email: document.getElementById('emp_email').value,
                sdt: document.getElementById('emp_sdt').value,
                password: document.getElementById('emp_password').value,
                role: document.querySelector('.badge-role').innerText.trim()
            };

            try {
                const res = await fetch("../../Controllers/accountController.php", {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();
                
                if (result.status) {
                    alert("Cập nhật thông tin thành công!");
                    location.reload(); // Reload để cập nhật Session và hiển thị dữ liệu mới
                } else {
                    alert("Lỗi: " + result.message);
                }
            } catch (err) {
                console.error("Lỗi kết nối:", err);
                alert("Không thể kết nối đến máy chủ!");
            }
        };
    }
});