<<<<<<< HEAD

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
=======
let cart = [];
const CHECKOUT_DRAFT_KEY = 'food_checkout_draft_v1';

function getApiMessage(payload, fallbackMessage) {
    if (!payload || typeof payload !== 'object') {
        return fallbackMessage;
    }

    if (typeof payload.message === 'string' && payload.message.trim() !== '') {
        return payload.message;
    }

    return fallbackMessage;
}

async function parseApiResponse(response) {
    try {
        return await response.json();
    } catch (e) {
        return null;
    }
}

function formatCurrency(value) {
    return `${new Intl.NumberFormat().format(Number(value) || 0)}đ`;
}

function saveCheckoutDraft(draft) {
    sessionStorage.setItem(CHECKOUT_DRAFT_KEY, JSON.stringify(draft));
}

function loadCheckoutDraft() {
    const raw = sessionStorage.getItem(CHECKOUT_DRAFT_KEY);
    if (!raw) {
        return null;
    }

    try {
        const parsed = JSON.parse(raw);
        if (!parsed || !Array.isArray(parsed.items)) {
            return null;
        }
        return parsed;
    } catch (e) {
        return null;
    }
}

function clearCheckoutDraft() {
    sessionStorage.removeItem(CHECKOUT_DRAFT_KEY);
}

function buildOrderPayloadFromDraft(draft) {
    return {
        bookingId: draft.bookingId ?? null,
        phuongThuc: draft.phuongThuc,
        items: draft.items.map(item => ({
            foodId: item.foodId,
            soLuong: item.soLuong
        }))
    };
}

function getBookingIdInput() {
    const bookingInput = document.getElementById('booking-id');
    if (!bookingInput) {
        return { bookingId: null, error: null };
    }

    const raw = bookingInput.value.trim();
    if (raw === '') {
        return { bookingId: null, error: null };
    }

    const parsed = Number(raw);
    if (!Number.isInteger(parsed) || parsed <= 0) {
        return { bookingId: null, error: 'Mã booking phải là số nguyên dương.' };
    }

    return { bookingId: parsed, error: null };
}

function getCheckoutDraftFromCurrentCart() {
    const bookingInfo = getBookingIdInput();
    if (bookingInfo.error) {
        return { draft: null, error: bookingInfo.error };
    }

    const methodSelect = document.getElementById('payment-method');
    const phuongThuc = methodSelect ? methodSelect.value : '';
    if (!phuongThuc) {
        return { draft: null, error: 'Vui lòng chọn phương thức thanh toán.' };
    }

    const items = cart.map(item => ({
        foodId: item.foodId,
        tenFood: item.tenFood,
        price: item.price,
        soLuong: item.soLuong
    }));

    return {
        draft: {
            bookingId: bookingInfo.bookingId,
            phuongThuc,
            items
        },
        error: null
    };
}

function setCheckoutMessage(message, type = 'info') {
    const messageEl = document.getElementById('checkout-message');
    if (!messageEl) {
        return;
    }

    messageEl.textContent = message || '';
    messageEl.className = `checkout-message ${type}`;
}

// 1. Lấy danh sách bắp nước từ Controller đổ ra HTML
async function loadFoodUser() {
    const container = document.getElementById('food-list');
    if (!container) {
        return;
    }

    try {
        const res = await fetch('/QL_Rap_Chieu/Controllers/foodController.php?action=list_all');
        const json = await parseApiResponse(res);

        if (!res.ok || !json || json.success !== true) {
            const message = getApiMessage(json, 'Không thể tải danh sách món.');

            if (res.status === 401) {
                container.innerHTML = '<p class="food-loading">Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.</p>';
                return;
            }

            throw new Error(message);
        }

        if (json.data.length === 0) {
            container.innerHTML = '<p>Hiện tại không có món nào.</p>';
            return;
        }

        container.innerHTML = json.data.map(f => `
            <div style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333; text-align: center;">
                <h3 style="color: #fff; margin: 0 0 10px 0;">${f.tenFood}</h3>
                <p style="color: #f5c518; font-size: 1.2em; font-weight: bold;">${formatCurrency(f.gia)}</p>
                <p style="color: #666; font-size: 0.8em;">Còn lại: ${f.soLuongTon}</p>
                <button onclick="addToCart(${f.foodId}, '${f.tenFood}', ${f.gia}, ${f.soLuongTon})" 
                        style="background: #e71a0f; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; font-weight: bold; border-radius: 4px; margin-top: 10px;">
                    CHỌN MUA
                </button>
            </div>
        `).join('');
    } catch (e) {
        console.error('Lỗi:', e);
        container.innerHTML = `<p class="food-loading">${e.message || 'Không thể tải danh sách món.'}</p>`;
>>>>>>> dev-food
    }
}

// 2. Thêm vào giỏ
function addToCart(id, name, price, stock) {
<<<<<<< HEAD
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
    
=======
    if (stock <= 0) {
        alert('Món này đã hết hàng.');
        return;
    }

    const item = cart.find(i => i.foodId === id);
    if (item) {
        if (item.soLuong < stock) {
            item.soLuong++;
        } else {
            alert('Đã đạt giới hạn tồn kho của món này.');
        }
    } else {
        cart.push({ foodId: id, tenFood: name, price: Number(price) || 0, soLuong: 1 });
    }

    renderCart();
}

// 3. Hiển thị giỏ hàng
function renderCart() {
    const content = document.getElementById('cart-content');
    const totalEl = document.getElementById('cart-total');
    if (!content || !totalEl) {
        return;
    }

>>>>>>> dev-food
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
<<<<<<< HEAD
                    <small style="color: #888;">${new Intl.NumberFormat().format(i.price)}đ x ${i.soLuong}</small>
                </div>
                <div style="text-align: right;">
                    <div style="color: #f5c518; font-weight: bold; margin-bottom: 5px;">
                        ${new Intl.NumberFormat().format(i.price * i.soLuong)}đ
                    </div>
                    <!-- NÚT XÓA MÓN ĐÂY MÀY -->
=======
                    <small style="color: #888;">${formatCurrency(i.price)} x ${i.soLuong}</small>
                </div>
                <div style="text-align: right;">
                    <div style="color: #f5c518; font-weight: bold; margin-bottom: 5px;">
                        ${formatCurrency(i.price * i.soLuong)}
                    </div>
>>>>>>> dev-food
                    <button onclick="removeFromCart(${index})" 
                            style="background: none; border: 1px solid #444; color: #e71a0f; font-size: 10px; cursor: pointer; padding: 2px 6px; border-radius: 3px;">
                        XÓA
                    </button>
                </div>
            </div>
        `;
    }).join('');
<<<<<<< HEAD
    
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
=======

    totalEl.innerText = formatCurrency(total);
}

// Nút thanh toán ở trang giỏ: chuyển sang trang checkout
function placeOrder() {
    if (cart.length === 0) {
        alert('Giỏ hàng đang trống.');
        return;
    }

    const checkoutResult = getCheckoutDraftFromCurrentCart();
    if (checkoutResult.error) {
        alert(checkoutResult.error);
        return;
    }

    saveCheckoutDraft(checkoutResult.draft);
    window.location.href = '/QL_Rap_Chieu/View/pages/food_checkout.php';
}

// Hàm xóa món khỏi giỏ hàng
function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function restoreCartFromCheckoutDraftIfNeeded() {
    const params = new URLSearchParams(window.location.search);
    const shouldRestore = params.get('restoreCheckout') === '1';
    if (!shouldRestore) {
        return;
    }

    const draft = loadCheckoutDraft();
    if (!draft || !Array.isArray(draft.items) || draft.items.length === 0) {
        return;
    }

    cart = draft.items.map(item => ({
        foodId: Number(item.foodId) || 0,
        tenFood: item.tenFood || `Món #${item.foodId}`,
        price: Number(item.price) || 0,
        soLuong: Number(item.soLuong) || 1
    })).filter(item => item.foodId > 0 && item.soLuong > 0);

    const bookingInput = document.getElementById('booking-id');
    if (bookingInput) {
        bookingInput.value = draft.bookingId ? String(draft.bookingId) : '';
    }

    const methodSelect = document.getElementById('payment-method');
    if (methodSelect && draft.phuongThuc) {
        methodSelect.value = draft.phuongThuc;
    }

    renderCart();
}

function renderCheckoutPreview(draft, previewData) {
    const ticketCard = document.getElementById('checkout-ticket-card');
    const ticketInfo = document.getElementById('checkout-ticket-info');
    const itemsWrap = document.getElementById('checkout-food-items');
    const foodTotalEl = document.getElementById('checkout-food-total');
    const ticketTotalEl = document.getElementById('checkout-ticket-total');
    const paymentTotalEl = document.getElementById('checkout-payment-total');
    const bookingInput = document.getElementById('checkout-booking-id');

    if (!itemsWrap || !foodTotalEl || !ticketTotalEl || !paymentTotalEl) {
        return;
    }

    const items = previewData && Array.isArray(previewData.items) ? previewData.items : [];
    if (items.length === 0) {
        itemsWrap.innerHTML = '<p class="checkout-empty">Không có món nào trong giỏ hàng.</p>';
    } else {
        itemsWrap.innerHTML = items.map(item => `
            <div class="checkout-item-row">
                <div>
                    <p class="checkout-item-name">${item.tenFood}</p>
                    <p class="checkout-item-sub">${formatCurrency(item.gia)} x ${item.soLuong}</p>
                </div>
                <p class="checkout-item-total">${formatCurrency(item.thanhTien)}</p>
            </div>
        `).join('');
    }

    const booking = previewData ? previewData.booking : null;
    if (ticketCard && ticketInfo) {
        if (booking) {
            ticketCard.style.display = 'block';
            ticketInfo.innerHTML = `
                <p><b>Mã booking:</b> #${booking.bookingId}</p>
                <p><b>Số lượng vé:</b> ${booking.soLuongVe}</p>
                <p><b>Giá vé:</b> ${formatCurrency(booking.giaVe)}</p>
                <p><b>Trạng thái booking:</b> ${booking.trangThai}</p>
            `;
        } else {
            ticketCard.style.display = 'none';
            ticketInfo.innerHTML = '';
        }
    }

    if (bookingInput) {
        bookingInput.value = draft.bookingId ? String(draft.bookingId) : 'Không có';
    }

    foodTotalEl.textContent = formatCurrency(previewData ? previewData.tongTienFood : 0);
    ticketTotalEl.textContent = formatCurrency(previewData ? previewData.tongTienVe : 0);
    paymentTotalEl.textContent = formatCurrency(previewData ? previewData.tongTienThanhToan : 0);
}

async function loadCheckoutPreview() {
    const draft = loadCheckoutDraft();
    if (!draft || !Array.isArray(draft.items) || draft.items.length === 0) {
        setCheckoutMessage('Không tìm thấy dữ liệu thanh toán. Vui lòng quay lại giỏ hàng.', 'error');
        const confirmBtn = document.getElementById('confirm-checkout-btn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
        }
        renderCheckoutPreview({ bookingId: null }, null);
        return;
    }

    const payload = buildOrderPayloadFromDraft(draft);

    try {
        setCheckoutMessage('Đang cập nhật thông tin thanh toán...', 'info');
        const response = await fetch('/QL_Rap_Chieu/Controllers/foodController.php?action=checkout_preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await parseApiResponse(response);
        if (!response.ok || !result || result.success !== true) {
            const message = getApiMessage(result, 'Không thể lấy thông tin thanh toán.');

            if (response.status === 401) {
                setCheckoutMessage('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 'error');
                window.location.href = '/QL_Rap_Chieu/View/pages/login.php';
                return;
            }

            setCheckoutMessage(message, 'error');
            renderCheckoutPreview(draft, null);
            return;
        }

        draft.phuongThuc = result.data.phuongThuc || draft.phuongThuc;
        saveCheckoutDraft(draft);

        const methodSelect = document.getElementById('checkout-method');
        if (methodSelect && draft.phuongThuc) {
            methodSelect.value = draft.phuongThuc;
        }

        renderCheckoutPreview(draft, result.data);
        setCheckoutMessage(getApiMessage(result, 'Thông tin thanh toán đã sẵn sàng.'), 'success');
    } catch (error) {
        console.error('Lỗi preview checkout:', error);
        setCheckoutMessage('Không thể kết nối tới máy chủ. Vui lòng thử lại.', 'error');
    }
}

async function submitCheckoutOrder() {
    const draft = loadCheckoutDraft();
    if (!draft || !Array.isArray(draft.items) || draft.items.length === 0) {
        setCheckoutMessage('Không có dữ liệu thanh toán để xử lý.', 'error');
        return;
    }

    const methodSelect = document.getElementById('checkout-method');
    if (methodSelect) {
        draft.phuongThuc = methodSelect.value;
        saveCheckoutDraft(draft);
    }

    const confirmBtn = document.getElementById('confirm-checkout-btn');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'ĐANG XỬ LÝ...';
    }
>>>>>>> dev-food

    try {
        const response = await fetch('/QL_Rap_Chieu/Controllers/foodController.php?action=place_order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
<<<<<<< HEAD
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
=======
            body: JSON.stringify(buildOrderPayloadFromDraft(draft))
        });

        const result = await parseApiResponse(response);
        if (!response.ok || !result || result.success !== true) {
            const errorMessage = getApiMessage(result, 'Thanh toán thất bại. Vui lòng thử lại.');

            if (response.status === 401) {
                setCheckoutMessage('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 'error');
                window.location.href = '/QL_Rap_Chieu/View/pages/login.php';
                return;
            }

            setCheckoutMessage(errorMessage, 'error');
            return;
        }

        const total = result.data ? result.data.tongTienThanhToan : null;
        const paymentId = result.data ? result.data.paymentId : null;
        let successMsg = getApiMessage(result, 'Đặt món thành công.');
        if (paymentId) {
            successMsg += ` Mã thanh toán: #${paymentId}.`;
        }
        if (total !== null && total !== undefined) {
            successMsg += ` Tổng thanh toán: ${formatCurrency(total)}.`;
        }

        clearCheckoutDraft();
        alert(successMsg);
        window.location.href = '/QL_Rap_Chieu/View/pages/home.php';
    } catch (error) {
        console.error('Lỗi checkout:', error);
        setCheckoutMessage('Không thể kết nối tới máy chủ. Vui lòng thử lại.', 'error');
    } finally {
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'XÁC NHẬN THANH TOÁN';
        }
    }
}

function initializeFoodPage() {
    const params = new URLSearchParams(window.location.search);
    const bookingIdFromUrl = params.get('bookingId');
    const bookingInput = document.getElementById('booking-id');

    if (bookingInput && bookingIdFromUrl && /^\d+$/.test(bookingIdFromUrl)) {
        bookingInput.value = bookingIdFromUrl;
    }

    restoreCartFromCheckoutDraftIfNeeded();
    renderCart();
    loadFoodUser();
}

function initializeCheckoutPage() {
    const draft = loadCheckoutDraft();
    const methodSelect = document.getElementById('checkout-method');
    if (methodSelect && draft && draft.phuongThuc) {
        methodSelect.value = draft.phuongThuc;
    }

    if (methodSelect) {
        methodSelect.addEventListener('change', () => {
            const changedDraft = loadCheckoutDraft();
            if (!changedDraft) {
                return;
            }
            changedDraft.phuongThuc = methodSelect.value;
            saveCheckoutDraft(changedDraft);
            loadCheckoutPreview();
        });
    }

    loadCheckoutPreview();
}

window.onload = () => {
    const isCheckoutPage = document.body && document.body.getAttribute('data-page') === 'food-checkout';
    if (isCheckoutPage) {
        initializeCheckoutPage();
        return;
    }

    initializeFoodPage();
};
>>>>>>> dev-food
