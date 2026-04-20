# Chi tiet Backend Food Controller

## 1. Pham vi file
- Controllers/foodController.php
- Models/foodModels.php
- Models/foodOrder.php
- Models/foodOrderDetail.php
- Models/payment.php (phan tao payment)

## 2. Tong quan controller
`foodController.php` nhan `action` tu query string, parse JSON body va tra ve JSON qua `foodRespond`.

Tat ca action:
- `list_all` (GET)
- `save` (POST)
- `delete` (GET)
- `checkout_preview` (POST)
- `place_order` (POST)

## 3. Helper parseCheckoutPayload
Ham `parseCheckoutPayload($input)` la trung tam cho preview va place_order.

### Dau vao
- `items`: mang cac mon gom `foodId`, `soLuong`.

### Validate
1. `items` phai la mang va khong rong.
2. Tung item phai la object hop le.
3. `foodId > 0`, `soLuong > 0`.
4. Gop item trung `foodId` thanh `normalizedItems`.

### Quy tac co dinh
- `phuongThuc` luon bi ep thanh `Tien mat` (khong lay bookingId, khong nhan card trong luong Food hien tai).

### Dau ra
```json
{
  "success": true,
  "phuongThuc": "Tien mat",
  "normalizedItems": {
    "1": 3,
    "2": 1
  }
}
```

## 4. Action list_all
### Quyen
- Admin, Manager, Employee, Customer.

### Xu ly
1. `checkAuth`.
2. Goi `Food::getAll()` (sap xep `foodId DESC`).
3. Tra JSON `success=true` + danh sach mon.

## 5. Action save
### Quyen
- Admin, Manager.

### Xu ly
1. Kiem tra payload khong rong.
2. Goi `Food::save($input)`.
3. Neu fail -> message gia > 0, ton kho >= 0.

### Luu y
`Food::save` tu dong set `trangThai`:
- `Con` neu `soLuongTon > 0`.
- `Het` neu `soLuongTon = 0`.

## 6. Action delete
### Quyen
- Admin.

### Xu ly
1. Doc `foodId` tu query.
2. `foodId` phai > 0.
3. Goi `Food::delete($id)`.

## 7. Action checkout_preview
### Quyen
- Customer, Admin, Manager, Employee.

### Xu ly chi tiet
1. Kiem tra session user + accountId hop le.
2. Goi `parseCheckoutPayload`.
3. Duyet tung item da normalize:
   - Query `SELECT ... FROM Food WHERE foodId = ? LIMIT 1` (khong lock).
   - Validate mon ton tai.
   - Validate ton kho > 0 va du so luong.
   - Validate gia > 0.
   - Tinh `thanhTien` moi dong.
4. Tinh tong:
   - `tongTienFood = sum(thanhTien)`
   - `tongTienThanhToan = tongTienFood`
5. Tra payload preview.

### Response thanh cong
```json
{
  "success": true,
  "message": "Lay thong tin thanh toan thanh cong.",
  "data": {
    "items": [
      {
        "foodId": 1,
        "tenFood": "Bap Caramel",
        "gia": 65000,
        "soLuong": 3,
        "thanhTien": 195000,
        "soLuongTon": 50,
        "trangThai": "Con"
      }
    ],
    "tongTienFood": 195000,
    "tongTienThanhToan": 195000,
    "phuongThuc": "Tien mat"
  }
}
```

## 8. Action place_order
### Quyen
- Customer, Admin, Manager, Employee.

### Muc tieu
Tao FoodOrder + FoodOrderDetail + Payment va tru ton kho trong cung 1 transaction.

### Trinh tu transaction
1. `BEGIN`.
2. Validate session + payload nhu preview.
3. Query tung mon bang:
   - `SELECT ... FROM Food WHERE foodId = ? FOR UPDATE`.
4. Validate ton tai/ton kho/gia va tinh `foodTotal`.
5. Tao `FoodOrder`:
   - `bookingId = NULL`
   - `trangThai = Cho xac nhan`
6. Tao tung dong `FoodOrderDetail`.
7. Tru ton kho Food:
   - `UPDATE Food SET soLuongTon = soLuongTon - ?, trangThai = CASE ... END WHERE foodId = ? AND soLuongTon >= ?`
   - Neu `affected_rows = 0` => throw loi race condition ton kho.
8. Tao `Payment`:
   - `bookingId = NULL`
   - `foodOrderId = orderId`
   - `tongTien = foodTotal`
   - `phuongThuc = Tien mat`
   - `trangThai = Cho xac nhan`
9. `COMMIT` neu tat ca OK.
10. `ROLLBACK` neu co bat ky exception nao.

### Response thanh cong
```json
{
  "success": true,
  "message": "Dat mon thanh cong. Don hang dang cho xac nhan.",
  "data": {
    "foodOrderId": 500,
    "paymentId": 900,
    "tongTienFood": 285000,
    "tongTienThanhToan": 285000,
    "phuongThuc": "Tien mat",
    "trangThaiThanhToan": "Cho xac nhan"
  }
}
```

## 9. Cac diem an toan du lieu
1. `FOR UPDATE` khi place_order de tranh 2 user mua vuot ton kho.
2. Dieu kien `soLuongTon >= ?` trong UPDATE de chong race condition.
3. Rollback toan bo neu 1 buoc that bai, tranh sinh du lieu nua vo.
4. Gia luu trong `FoodOrderDetail.giaLucMua` de co lich su gia tai thoi diem mua.

## 10. Cac thong diep loi thuong gap
- Gio hang trong hoac item sai dinh dang.
- Mon khong ton tai.
- Mon het hang hoac ton kho khong du.
- Tong tien khong hop le.
- Khong the tao FoodOrder / FoodOrderDetail / Payment.
- Phien dang nhap het han.
