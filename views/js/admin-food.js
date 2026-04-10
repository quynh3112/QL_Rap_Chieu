//1. lấy dữ liệu và hiển thị danh sách
async function loadAdminData() {
    const resFood = await fetch('.../.../Controllers/adminFoodController.php?action=list_all');
    const dataFood = await resFood.json();
    document.getElementById('tbody-food').innerHTML = foodData.data.map(f => `
        <tr>
            <td>${f.tenFood}</td>
            <td>${f.loaiFood}</td>
            <td>${new Intl.NumberFormat().format(f.gia)}</td>
            <td>${f.soLuongTon}</td>
            <td><span class="status-tag ${f.trangThai=='Còn'?'green':'red'}">${f.trangThai}</span></td>
            <td>
                <button onclick='editFood(${JSON.stringify(f)})' class='btn-edit'>Sửa</button>
            </td>
        </tr>
    `).join('');

    //lấy đơn hàng
      const resOrder = await fetch('../../Controllers/adminFoodController.php?action=list_orders');
    const orderData = await resOrder.json();
    document.getElementById('tbody-order').innerHTML = orderData.data.map(o => `
        <tr>
            <td>#${o.foodOrderId}</td>
            <td>${o.tenAccount}</td>
            <td>${o.ngayMua}</td>
            <td>${new Intl.NumberFormat().format(o.tongTienFood)}đ</td>
            <td>${o.trangThai}</td>
        </tr>
    `).join('');
}
//2.chuyển tab
function switchTab(tab) {
    document.querySelectorAll('.admin-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('section-' + tab).style.display = 'block';
    event.target.classList.add('active');
}
//3.modal control
function openModal() { document.getElementById('food-modal').style.display = 'flex'; }
function closeModal() { document.getElementById('food-modal').style.display = 'none'; }

loadAdminData();