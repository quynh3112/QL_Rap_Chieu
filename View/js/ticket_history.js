// file: view/js/ticket_history.js

/**
 * Hàm xử lý hủy vé gửi yêu cầu lên API (bookingController.php)
 * @param {number} bookingId - ID của đơn đặt vé cần hủy
 */
async function huyVe(bookingId) {
    // 1. Hiển thị hộp thoại xác nhận để tránh người dùng bấm nhầm
    const xacNhan = confirm(`Bạn có chắc chắn muốn hủy đơn vé #${bookingId} không?\nLưu ý: Hành động này không thể hoàn tác!`);
    
    if (!xacNhan) {
        return; // Nếu người dùng chọn "Cancel" thì dừng lại
    }

    try {
        // 2. Gọi API với method PUT để cập nhật trạng thái
        // Lưu ý: Kiểm tra đường dẫn fetch cho khớp với thư mục của Nhi
        const response = await fetch(`../../Controllers/bookingController.php?id=${bookingId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            // Gửi dữ liệu trạng thái mới lên server
            body: JSON.stringify({
                trangThai: 'Đã hủy'
            })
        });

        // 3. Đọc dữ liệu trả về từ Controller
        const result = await response.json();

        // 4. Xử lý kết quả
        if (result.status) {
            alert("Hủy vé thành công!");
            // Tải lại trang để giao diện tự cập nhật màu sắc và trạng thái mới
            location.reload(); 
        } else {
            alert("Lỗi khi hủy vé: " + result.message);
        }
    } catch (error) {
        console.error("Lỗi kết nối:", error);
        alert("Không thể kết nối đến máy chủ. Vui lòng thử lại sau!");
    }
}