document.addEventListener('DOMContentLoaded', () => {
    const updateForm = document.getElementById('updateProfileForm');

    if (updateForm) {
        updateForm.onsubmit = async (e) => {
            e.preventDefault();

            // Lấy dữ liệu từ Form
            const formData = new FormData(updateForm);
            const data = Object.fromEntries(formData.entries());
            
            // Lấy accountId từ input hidden
            data.accountId = document.getElementById('acc_id').value;

            // Xử lý logic vai trò (Role)
            // Vì file profile_customer dành cho khách hàng, ta gán cứng role là 'customer' 
            // để khớp với hàm update($accountId, ..., $role) trong Model
            data.role = 'customer';

            try {
                // Gửi yêu cầu cập nhật thông tin chung
                const res = await fetch("/QL_Rap_Chieu/Controllers/accountController.php", {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();
                
                if (result.status) {
                    alert(result.message);
                    // Load lại trang để PHP cập nhật lại thông tin mới từ Database vào giao diện
                    location.reload(); 
                } else {
                    alert("Thất bại: " + result.message);
                }
            } catch (err) {
                console.error("Lỗi kết nối:", err);
                alert("Không kết nối được với máy chủ!");
            }
        };
    }
});