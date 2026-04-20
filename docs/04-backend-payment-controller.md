# Chi tiet Backend Payment Controller

## 1. Pham vi file
- Controllers/paymentController.php
- Models/payment.php
- Models/foodOrder.php

## 2. Tong quan action
`paymentController.php` ho tro 3 action:
- `list_pending` (GET)
- `approve` (POST)
- `cancel` (POST)

## 3. Action list_pending
### Quyen
- Admin, Manager, Employee.

### Xu ly
1. `checkAuth`.
2. Goi `Payment::getPending()` (thuc chat la `getByStatus('Cho xac nhan')`).
3. Tra ve:
   - `payments`: danh sach payment dang cho.
   - `currentRole`: role dang login, de frontend bat/tat nut thao tac.

### Luu y
- Employee duoc xem danh sach nhung khong duoc duyet/huy.

## 4. Action approve/cancel
Ca 2 action deu goi chung `handlePaymentDecision(..., $isApprove)`.

### Quyen
- Chi Admin, Manager.

### Dau vao
```json
{ "paymentId": 900 }
```

### Validate dau vao
1. `paymentId > 0`.
2. `adminId` ton tai trong session.

### Trinh tu transaction
1. `BEGIN`.
2. `Payment::getByIdForUpdate(paymentId)` de lock dong Payment.
3. Validate:
   - Payment ton tai.
   - `trangThai` hien tai phai la `Cho xac nhan`.
   - `tongTien > 0`.
4. Xac dinh trang thai dich:
   - approve:
     - Payment = `Da duyet`
     - FoodOrder = `Da giao`
     - Booking = `Da xac nhan`
   - cancel:
     - Payment = `Da huy`
     - FoodOrder = `Da huy`
     - Booking = `Da huy`
5. Update Payment qua `updateStatus(paymentId, paymentStatus, adminId)`.
6. Neu payment co `foodOrderId`: update FoodOrder.
7. Neu payment co `bookingId`: update Booking.
8. `COMMIT`.
9. Neu loi -> `ROLLBACK`.

## 5. Kich ban du lieu duoc dong bo
### Payment chi co FoodOrder (luong Food hien tai)
- Se dong bo Payment + FoodOrder.
- Khong update Booking vi `bookingId = NULL`.

### Payment co ca FoodOrder va Booking (luong lai hoac du lieu cu)
- Se dong bo ca 3 bang Payment + FoodOrder + Booking.

### Payment chi co Booking (luong dat ve)
- Se update Payment + Booking.

## 6. Dam bao tranh xu ly lap
Do co check:
- `if ($payment['trangThai'] !== 'Cho xac nhan') throw ...`
nen payment da duyet/huy truoc do se bi chan voi message `Thanh toan da duoc xu ly truoc do`.

## 7. Response mau
### approve thanh cong
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

### cancel thanh cong
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

## 8. So do xu ly approve/cancel
```mermaid
flowchart TD
    A[Nhan request approve/cancel] --> B[Validate paymentId + admin session]
    B --> C[BEGIN]
    C --> D[getByIdForUpdate(paymentId)]
    D --> E{Trang thai Cho xac nhan?}
    E -- Khong --> F[Throw da xu ly]
    E -- Co --> G[Update Payment]
    G --> H{Co foodOrderId?}
    H -- Co --> I[Update FoodOrder]
    H -- Khong --> J[Bo qua]
    I --> K{Co bookingId?}
    J --> K
    K -- Co --> L[Update Booking]
    K -- Khong --> M[Bo qua]
    L --> N[COMMIT]
    M --> N
    F --> O[ROLLBACK]
```
