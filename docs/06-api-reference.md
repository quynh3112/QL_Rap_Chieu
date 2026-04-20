# API Reference Food va Payment

## 1. Tong quan endpoint

| API | Method | Quyen | Mo ta |
|---|---|---|---|
| /Controllers/foodController.php?action=list_all | GET | Admin, Manager, Employee, Customer | Lay danh sach mon |
| /Controllers/foodController.php?action=save | POST | Admin, Manager | Them/sua mon |
| /Controllers/foodController.php?action=delete&foodId={id} | GET | Admin | Xoa mon |
| /Controllers/foodController.php?action=checkout_preview | POST | Admin, Manager, Employee, Customer | Preview thanh toan food |
| /Controllers/foodController.php?action=place_order | POST | Admin, Manager, Employee, Customer | Tao FoodOrder, FoodOrderDetail, Payment |
| /Controllers/paymentController.php?action=list_pending | GET | Admin, Manager, Employee | Lay payment cho xac nhan |
| /Controllers/paymentController.php?action=approve | POST | Admin, Manager | Duyet payment |
| /Controllers/paymentController.php?action=cancel | POST | Admin, Manager | Huy payment |

## 2. Food API chi tiet

## 2.1 list_all
### Request
```http
GET /Controllers/foodController.php?action=list_all
```

### Response thanh cong
```json
{
  "success": true,
  "message": "Lay danh sach mon thanh cong",
  "data": [
    {
      "foodId": 2,
      "tenFood": "Coca",
      "loaiFood": "Nuoc",
      "gia": "30000.00",
      "soLuongTon": 100,
      "trangThai": "Con"
    }
  ]
}
```

## 2.2 save
### Request
```http
POST /Controllers/foodController.php?action=save
Content-Type: application/json
```

```json
{
  "foodId": 2,
  "tenFood": "Coca Zero",
  "loaiFood": "Nuoc",
  "gia": 32000,
  "soLuongTon": 80
}
```

### Response thanh cong
```json
{ "success": true, "message": "Luu mon thanh cong" }
```

### Response loi
```json
{ "success": false, "message": "Khong the luu mon. Gia phai lon hon 0 va ton kho phai tu 0 tro len." }
```

## 2.3 delete
### Request
```http
GET /Controllers/foodController.php?action=delete&foodId=2
```

### Response thanh cong
```json
{ "success": true, "message": "Xoa mon thanh cong" }
```

## 2.4 checkout_preview
### Request
```http
POST /Controllers/foodController.php?action=checkout_preview
Content-Type: application/json
```

```json
{
  "phuongThuc": "Tien mat",
  "items": [
    { "foodId": 1, "soLuong": 2 },
    { "foodId": 2, "soLuong": 3 },
    { "foodId": 1, "soLuong": 1 }
  ]
}
```

### Ghi chu
- Backend se gop item trung foodId.
- Backend ep `phuongThuc` thanh `Tien mat`.
- Luong hien tai khong xu ly bookingId trong payload Food.

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
      },
      {
        "foodId": 2,
        "tenFood": "Coca",
        "gia": 30000,
        "soLuong": 3,
        "thanhTien": 90000,
        "soLuongTon": 100,
        "trangThai": "Con"
      }
    ],
    "tongTienFood": 285000,
    "tongTienThanhToan": 285000,
    "phuongThuc": "Tien mat"
  }
}
```

### Response loi vi ton kho
```json
{ "success": false, "message": "So luong ton khong du cho mon: Bap Caramel" }
```

## 2.5 place_order
### Request
```http
POST /Controllers/foodController.php?action=place_order
Content-Type: application/json
```

```json
{
  "phuongThuc": "Tien mat",
  "items": [
    { "foodId": 1, "soLuong": 3 },
    { "foodId": 2, "soLuong": 3 }
  ]
}
```

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

## 3. Payment API chi tiet

## 3.1 list_pending
### Request
```http
GET /Controllers/paymentController.php?action=list_pending
```

### Response thanh cong
```json
{
  "success": true,
  "message": "Lay danh sach thanh toan cho xac nhan thanh cong",
  "data": {
    "currentRole": "Manager",
    "payments": [
      {
        "paymentId": 900,
        "bookingId": null,
        "foodOrderId": 500,
        "tongTien": "285000.00",
        "phuongThuc": "Tien mat",
        "ngayThanhToan": "2026-04-20 15:10:00",
        "paymentStatus": "Cho xac nhan",
        "tongTienFood": "285000.00",
        "foodOrderStatus": "Cho xac nhan",
        "bookingStatus": null,
        "customerName": "Nguyen Van A"
      }
    ]
  }
}
```

## 3.2 approve
### Request
```http
POST /Controllers/paymentController.php?action=approve
Content-Type: application/json
```

```json
{ "paymentId": 900 }
```

### Response thanh cong
```json
{
  "success": true,
  "message": "Duyet thanh toan thanh cong",
  "data": {
    "paymentId": 900,
    "paymentStatus": "Da duyet",
    "foodOrderId": 500,
    "bookingId": null
  }
}
```

## 3.3 cancel
### Request
```http
POST /Controllers/paymentController.php?action=cancel
Content-Type: application/json
```

```json
{ "paymentId": 901 }
```

### Response thanh cong
```json
{
  "success": true,
  "message": "Huy thanh toan thanh cong",
  "data": {
    "paymentId": 901,
    "paymentStatus": "Da huy",
    "foodOrderId": 501,
    "bookingId": null
  }
}
```

## 4. HTTP code va loi phan quyen
## 4.1 401 Unauthorized
Tu `checkAuth` khi chua login:
```json
{
  "status": false,
  "message": "Unauthorized - Vui long dang nhap."
}
```

## 4.2 403 Forbidden
Tu `checkAuth` khi sai role:
```json
{
  "status": false,
  "message": "Forbidden - Ban khong co quyen truy cap!"
}
```

## 5. Loi nghiep vu thuong gap
- `Gio hang trong hoac du lieu mon khong hop le.`
- `Mon da het hang: {tenFood}`
- `So luong ton khong du cho mon: {tenFood}`
- `Tong thanh toan khong hop le.`
- `Thanh toan da duoc xu ly truoc do`
