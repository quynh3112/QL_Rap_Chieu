CREATE DATABASE ql_rap;
USE ql_rap;

-- 1. Chi nhánh
CREATE TABLE Branch (
    branchId INT AUTO_INCREMENT PRIMARY KEY,
    tenBranch VARCHAR(100) NOT NULL,
    diaChi VARCHAR(200),
    thanhPho VARCHAR(100) NOT NULL 
) ENGINE=InnoDB;

-- 2. Tài khoản
CREATE TABLE Account (
    accountId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    hoTen VARCHAR(100),
    email VARCHAR(100),
    sdt VARCHAR(20),
    role ENUM('Admin', 'Manager', 'Employee', 'Customer') DEFAULT 'Customer',
    branchId INT NULL, 
    ngayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (branchId) REFERENCES Branch(branchId) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Lịch làm việc
CREATE TABLE WorkSchedule (
    workId INT AUTO_INCREMENT PRIMARY KEY,
    accountId INT NOT NULL,
    branchId INT NOT NULL,
    ngayLamViec DATE NOT NULL,
    caLam ENUM('Ca sáng', 'Ca chiều', 'Ca tối', 'Full-time') NOT NULL,
    gioBatDau TIME,
    gioKetThuc TIME,
    FOREIGN KEY (accountId) REFERENCES Account(accountId) ON DELETE CASCADE,
    FOREIGN KEY (branchId) REFERENCES Branch(branchId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Thể loại phim
CREATE TABLE Category (
    categoryId INT AUTO_INCREMENT PRIMARY KEY,
    tenTheLoai VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 5. Phim
CREATE TABLE Movie (
    movieId INT AUTO_INCREMENT PRIMARY KEY,
    tenPhim VARCHAR(200) NOT NULL,
    thoiLuong INT,
    moTa TEXT,
    img VARCHAR(255),
    daoDien VARCHAR(100),
    dienVien TEXT,
    namSanXuat INT,
    trangThai ENUM('Sắp chiếu', 'Đang chiếu', 'Kết thúc') DEFAULT 'Sắp chiếu'
) ENGINE=InnoDB;

-- 5.1 Liên kết Phim - Thể loại
CREATE TABLE MovieCategory (
    movieId INT NOT NULL,
    categoryId INT NOT NULL,
    PRIMARY KEY (movieId, categoryId),
    FOREIGN KEY (movieId) REFERENCES Movie(movieId) ON DELETE CASCADE,
    FOREIGN KEY (categoryId) REFERENCES Category(categoryId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Phòng chiếu
CREATE TABLE Room (
    roomId INT AUTO_INCREMENT PRIMARY KEY,
    tenPhong VARCHAR(50) NOT NULL,
    loaiPhong ENUM('VIP', 'Normal') NOT NULL,
    tongGhe INT,
    branchId INT NOT NULL,
    FOREIGN KEY (branchId) REFERENCES Branch(branchId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Ghế
CREATE TABLE Seat (
    seatId INT AUTO_INCREMENT PRIMARY KEY,
    roomId INT NOT NULL,
    tenGhe VARCHAR(10) NOT NULL,
    loaiGhe ENUM('VIP', 'Normal') NOT NULL,
    giaGhe DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (roomId) REFERENCES Room(roomId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Suất chiếu
CREATE TABLE Schedule (
    scheduleId INT AUTO_INCREMENT PRIMARY KEY,
    movieId INT NOT NULL,
    roomId INT NOT NULL,
    ngayChieu DATE NOT NULL,
    gioChieu TIME NOT NULL,
    giaVe DECIMAL(10,2) NOT NULL, 
    isCancelled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (movieId) REFERENCES Movie(movieId) ON DELETE CASCADE,
    FOREIGN KEY (roomId) REFERENCES Room(roomId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Đồ ăn
CREATE TABLE Food (
    foodId INT AUTO_INCREMENT PRIMARY KEY,
    tenFood VARCHAR(100) NOT NULL,
    loaiFood VARCHAR(50),  
    gia DECIMAL(10,2) NOT NULL,
    soLuongTon INT DEFAULT 0,
    trangThai ENUM('Còn', 'Hết') DEFAULT 'Còn'
) ENGINE=InnoDB;

-- 10. Đơn đặt vé
CREATE TABLE Booking (
    bookingId INT AUTO_INCREMENT PRIMARY KEY,
    accountId INT NOT NULL,
    scheduleId INT NOT NULL,
    soLuong INT NOT NULL,
    ngayDat DATETIME DEFAULT CURRENT_TIMESTAMP,
    trangThai ENUM('Chờ thanh toán', 'Đã xác nhận', 'Đã hủy') DEFAULT 'Chờ thanh toán',
    FOREIGN KEY (accountId) REFERENCES Account(accountId) ON DELETE CASCADE,
    FOREIGN KEY (scheduleId) REFERENCES Schedule(scheduleId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Chi tiết ghế đã đặt
CREATE TABLE BookingSeat (
    bookingSeatId INT AUTO_INCREMENT PRIMARY KEY,
    bookingId INT NOT NULL,
    seatId INT NOT NULL,
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId) ON DELETE CASCADE,
    FOREIGN KEY (seatId) REFERENCES Seat(seatId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Hóa đơn đồ ăn (ĐÃ BỎ branchId)
CREATE TABLE FoodOrder (
    foodOrderId INT AUTO_INCREMENT PRIMARY KEY,
    accountId INT NOT NULL,
    bookingId INT NULL, -- Có thể liên kết với vé hoặc mua lẻ
    ngayMua DATETIME DEFAULT CURRENT_TIMESTAMP,
    tongTienFood DECIMAL(10,2) NOT NULL DEFAULT 0,
    trangThai ENUM('Chờ xác nhận', 'Đã giao', 'Đã hủy') DEFAULT 'Chờ xác nhận',
    FOREIGN KEY (accountId) REFERENCES Account(accountId) ON DELETE CASCADE,
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 12.1 Chi tiết đồ ăn
CREATE TABLE FoodOrderDetail (
    detailId INT AUTO_INCREMENT PRIMARY KEY,
    foodOrderId INT NOT NULL,
    foodId INT NOT NULL,
    soLuong INT NOT NULL,
    giaLucMua DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (foodOrderId) REFERENCES FoodOrder(foodOrderId) ON DELETE CASCADE,
    FOREIGN KEY (foodId) REFERENCES Food(foodId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 13. Thanh toán
CREATE TABLE Payment (
    paymentId INT AUTO_INCREMENT PRIMARY KEY,
    bookingId INT NULL,
    foodOrderId INT NULL,
    tongTien DECIMAL(10,2) NOT NULL,
    phuongThuc ENUM('Tiền mặt', 'Thẻ') NOT NULL, 
    ngayThanhToan DATETIME DEFAULT CURRENT_TIMESTAMP,
    trangThai ENUM('Chờ xác nhận', 'Đã duyệt', 'Đã hủy') DEFAULT 'Chờ xác nhận',
    adminId INT NULL, 
    FOREIGN KEY (bookingId) REFERENCES Booking(bookingId) ON DELETE CASCADE,
    FOREIGN KEY (foodOrderId) REFERENCES FoodOrder(foodOrderId) ON DELETE CASCADE,
    FOREIGN KEY (adminId) REFERENCES Account(accountId) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 14. Đánh giá phim
CREATE TABLE Review (
    reviewId INT AUTO_INCREMENT PRIMARY KEY,
    movieId INT NOT NULL,
    accountId INT NOT NULL,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    reviewDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movieId) REFERENCES Movie(movieId) ON DELETE CASCADE,
    FOREIGN KEY (accountId) REFERENCES Account(accountId) ON DELETE CASCADE
) ENGINE=InnoDB;