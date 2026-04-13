/**
 * Hàm xử lý hủy vé gửi yêu cầu lên API (bookingController.php)
 * @param {number} bookingId - ID của đơn đặt vé cần hủy
 */
async function huyVe(bookingId) {
    // Hiển thị hộp thoại xác nhận để tránh người dùng bấm nhầm
    const xacNhan = confirm(`Bạn có chắc chắn muốn hủy đơn vé #${bookingId} không?\nLưu ý: Hành động này không thể hoàn tác!`);
    
    if (!xacNhan) {
        return; // Nếu người dùng chọn "Cancel" thì dừng lại
    }

    try {
        // Gọi API với method PUT để cập nhật trạng thái
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

        // Đọc dữ liệu trả về từ Controller
        const result = await response.json();

        // Xử lý kết quả
        if (result.status) {
            alert("Hủy vé thành công!");
            location.reload(); 
        } else {
            alert("Lỗi khi hủy vé: " + result.message);
        }
    } catch (error) {
        console.error("Lỗi kết nối:", error);
        alert("Không thể kết nối đến máy chủ. Vui lòng thử lại sau!");
    }
}