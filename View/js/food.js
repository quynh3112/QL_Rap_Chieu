
let cart = [];

// 1. Lấy danh sách bắp nước từ Controller đổ ra HTML
async function loadFoodUser() {
    try {
        // Gọi đến Controller quản lý Food 
        const res = await fetch('/QL_Rap_Chieu/Controllers/foodController.php?action=list_all');
        const json = await res.json();
        
        if (json.success) {
            const container = document.getElementById('food-list');
            if (json.data.length === 0) {
                container.innerHTML = "<p>Hiện tại không có món nào.</p>";
                return;
            }

            container.innerHTML = json.data.map(f => `
                <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333; text-align: center;">
                    <h3 style="color: #fff; margin: 0 0 10px 0;">${f.tenFood}</h3>
                    <p style="color: #f5c518; font-size: 1.2em; font-weight: bold;">${new Intl.NumberFormat().format(f.gia)}đ</p>
                    <p style="color: #666; font-size: 0.8em;">Còn lại: ${f.soLuongTon}</p>
                    <button onclick="addToCart(${f.foodId}, '${f.tenFood}', ${f.gia}, ${f.soLuongTon})" 
                            style="background: #e71a0f; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; font-weight: bold; border-radius: 4px; margin-top: 10px;">
                        CHỌN MUA
                    </button>
                </div>
            `).join('');
        }
    } catch (e) {
        console.error("Lỗi:", e);
    }
}

// 2. Thêm vào giỏ
function addToCart(id, name, price, stock) {
    if (stock <= 0) return alert("Hết hàng!");
    
    const item = cart.find(i => i.foodId === id);
    if (item) {
        if (item.soLuong < stock) item.soLuong++;
        else alert("Đã đạt giới hạn kho!");
    } else {
        cart.push({ foodId: id, tenFood: name, price: price, soLuong: 1 });
    }
    renderCart();
}

// 3. Hiển thị giỏ hàng bên tay phải
function renderCart() {
    const content = document.getElementById('cart-content');
    const totalEl = document.getElementById('cart-total');
    
    if (cart.length === 0) {
        content.innerHTML = '<p style="font-size: 14px; color: #555;">Giỏ hàng đang trống.</p>';
        totalEl.innerText = '0đ';
        return;
    }

    let total = 0;
    content.innerHTML = cart.map((i, index) => {
        total += i.price * i.soLuong;
        return `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #222; padding-bottom: 8px;">
                <div style="flex: 1;">
                    <b style="font-size: 14px; color: #fff;">${i.tenFood}</b><br>
                    <small style="color: #888;">${new Intl.NumberFormat().format(i.price)}đ x ${i.soLuong}</small>
                </div>
                <div style="text-align: right;">
                    <div style="color: #f5c518; font-weight: bold; margin-bottom: 5px;">
                        ${new Intl.NumberFormat().format(i.price * i.soLuong)}đ
                    </div>
                    <!-- NÚT XÓA MÓN ĐÂY MÀY -->
                    <button onclick="removeFromCart(${index})" 
                            style="background: none; border: 1px solid #444; color: #e71a0f; font-size: 10px; cursor: pointer; padding: 2px 6px; border-radius: 3px;">
                        XÓA
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    totalEl.innerText = new Intl.NumberFormat().format(total) + 'đ';
}

// 4. Thanh toán (Gửi đơn hàng đi)
async function placeOrder() {
    console.log("Đã bấm nút Thanh toán"); 

    if (cart.length === 0) {
        alert("Giỏ hàng đang trống mày ơi!");
        return;
    }
    const orderData = {
        bookingId: null, 
        items: cart      
    };

    try {
        const response = await fetch('/QL_Rap_Chieu/Controllers/foodController.php?action=place_order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });

        // Đọc kết quả từ server
        const result = await response.json();
        console.log("Kết quả từ server:", result);

        if (result.success) {
            alert("ĐẶT MÓN THÀNH CÔNG! Đơn hàng đã được lưu.");
            // Xoá giỏ hàng sau khi xong
            cart = [];
            renderCart();
            loadFoodUser(); // Load lại để cập nhật số lượng tồn mới nhất
        } else {
            alert("Lỗi từ server: " + result.message);
        }
    } catch (error) {
        console.error("Lỗi kết nối:", error);
        alert("Không thể kết nối đến server. Kiểm tra lại Controller!");
    }
}

window.onload = loadFoodUser;

// Hàm xóa món khỏi giỏ hàng
function removeFromCart(index) {
    cart.splice(index, 1);
    
    renderCart();
    console.log("Đã xóa món, giỏ hàng hiện tại:", cart);
}