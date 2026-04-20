# Test checklist va tinh huong quan trong

## 1. Muc tieu test
- Xac nhan luong Food moi hoat dong dung tu frontend -> backend -> DB.
- Xac nhan transaction + lock tranh du lieu lech khi dat dong thoi.
- Xac nhan role Employee chi xem duoc payment cho xu ly.

## 2. Du lieu seed toi thieu
```sql
INSERT INTO Account (accountId, username, password, hoTen, role)
VALUES
  (2, 'manager1', 'hashed', 'Tran Thi Quan Ly', 'Manager'),
  (3, 'employee1', 'hashed', 'Le Van Nhan Vien', 'Employee'),
  (10, 'customer1', 'hashed', 'Nguyen Van A', 'Customer');

INSERT INTO Food (foodId, tenFood, loaiFood, gia, soLuongTon, trangThai)
VALUES
  (1, 'Bap Caramel', 'Bap', 65000, 50, 'Con'),
  (2, 'Coca', 'Nuoc', 30000, 100, 'Con');
```

## 3. Checklist smoke test nhanh
1. Mo food.php, lay duoc danh sach mon.
2. Them mon vao gio, khong cho them qua ton kho.
3. Chuyen qua food_checkout.php, preview hien dung tong tien.
4. Xac nhan thanh toan thanh cong, tra ve paymentId.
5. Ton kho Food bi tru dung theo tong so luong da mua.
6. Payment va FoodOrder deu o trang thai `Cho xac nhan` sau place_order.
7. Manager duyet payment thanh cong.
8. Sau duyet: Payment = `Da duyet`, FoodOrder = `Da giao`.
9. Employee xem duoc list_pending nhung khong co nut Duyet/Huy.

## 4. Tinh huong test chi tiet

## 4.1 Dat mon thanh cong (happy path)
### Buoc
1. Dang nhap Customer.
2. Chon 3 Bap Caramel + 3 Coca.
3. Bam thanh toan.

### Ky vong
- Response success.
- `tongTienFood = 3*65000 + 3*30000 = 285000`.
- `tongTienThanhToan = 285000`.
- Tao 1 FoodOrder + 2 dong FoodOrderDetail + 1 Payment.

## 4.2 Payload co item trung foodId
### Input
```json
{
  "items": [
    { "foodId": 1, "soLuong": 2 },
    { "foodId": 1, "soLuong": 1 }
  ]
}
```

### Ky vong
- Backend gop thanh foodId=1, soLuong=3.
- Tong tien tinh theo soLuong da gop.

## 4.3 Ton kho khong du
### Setup
- set `Food.foodId=1.soLuongTon = 2`.

### Buoc
- Gui order soLuong=3.

### Ky vong
- API fail voi message ton kho khong du.
- Khong tao FoodOrder/Detail/Payment moi.
- Khong co thay doi ton kho.

## 4.4 Mon het hang
### Setup
- set `Food.foodId=2.soLuongTon = 0`, `trangThai='Het'`.

### Buoc
- Gui preview/place_order co foodId=2.

### Ky vong
- API tra loi `Mon da het hang`.

## 4.5 Duyet payment 2 lan lien tiep
### Buoc
1. Manager approve paymentId X (dang cho xac nhan).
2. Goi approve tiep paymentId X.

### Ky vong
- Lan 1 thanh cong.
- Lan 2 fail voi message `Thanh toan da duoc xu ly truoc do`.

## 4.6 Huy payment
### Buoc
- Manager cancel paymentId Y dang cho xac nhan.

### Ky vong
- Payment Y -> `Da huy`.
- FoodOrder lien quan -> `Da huy`.
- Booking lien quan (neu co) -> `Da huy`.

## 4.7 Test quyen frontend admin_food
### Buoc
1. Dang nhap Employee.
2. Mo tab `DUYET THANH TOAN`.

### Ky vong
- Co thay danh sach.
- Khong hien nut Duyet/Huy.

## 4.8 Test race condition ton kho
### Setup
- Mon A con ton = 1.
- Mo 2 phien dat hang dong thoi, moi phien dat soLuong=1.

### Ky vong
- 1 request thanh cong.
- 1 request fail voi message ton kho vua thay doi/khong du.
- Ton cuoi cung khong am.

## 5. SQL doi soat nhanh sau place_order
### FoodOrder
```sql
SELECT * FROM FoodOrder ORDER BY foodOrderId DESC LIMIT 1;
```

### FoodOrderDetail
```sql
SELECT * FROM FoodOrderDetail WHERE foodOrderId = ?;
```

### Payment
```sql
SELECT * FROM Payment ORDER BY paymentId DESC LIMIT 1;
```

### Ton kho Food
```sql
SELECT foodId, tenFood, soLuongTon, trangThai FROM Food WHERE foodId IN (1,2);
```

## 6. Regression can theo doi
1. Khong de bookingId xuat hien lai trong payload flow Food neu khong co yeu cau moi.
2. Neu mo rong phuong thuc thanh toan, can cap nhat ca frontend select va parseCheckoutPayload.
3. Neu them khuyen mai/phi dich vu, can cap nhat cong thuc `tongTienThanhToan` trong preview/place_order.
