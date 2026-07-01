<?php
session_start();
include "../../Config/db.php";
include "../../Models/Booking.php";

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem lịch sử!'); window.location.href='login.php';</script>";
    exit();
}

$bookingModel = new Booking();
$accountId = $_SESSION['user']['accountId'];
$result = $bookingModel->findHistoryByUser($accountId);

$listBookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $listBookings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt vé | CGV Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/ticket_history.css">
    <style>
        .card-ticket { transition: transform 0.2s; border-left: 5px solid #e71a0f !important; }
        .card-ticket:hover { transform: translateY(-5px); }
        .img-poster { object-fit: cover; height: 100%; min-height: 150px; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-danger fw-bold"><i class="bi bi-ticket-perforated-fill"></i> LỊCH SỬ ĐẶT VÉ</h3>
        <a href="movies.php" class="btn btn-outline-danger btn-sm">Quay lại đặt vé</a>
    </div>
    
    <?php if (empty($listBookings)): ?>
        <div class="alert alert-white text-center border shadow-sm py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-3">Bạn chưa có giao dịch nào.</p>
            <a href="movies.php" class="btn btn-danger">Đặt vé ngay!</a>
        </div>
    <?php else: ?>
        <?php foreach ($listBookings as $row): ?>
            <?php 
                // XỬ LÝ DỮ LIỆU ĐỒ ĂN VÀ TÍNH TIỀN CHO TỪNG VÉ
                $tienVe = $row['tongTien']; 
                $foodList = $bookingModel->getFoodByBooking($row['bookingId']);
                $tienFood = 0;
                foreach ($foodList as $food) {
                    $tienFood += $food['thanhTien'];
                }
                $tongThanhToan = $tienVe + $tienFood;
            ?>

            <div class="card mb-4 shadow-sm border-0 overflow-hidden card-ticket">
                <div class="row g-0">
                    <div class="col-md-2">
                        <img src="../Asset/<?php echo htmlspecialchars($row['img']); ?>" class="img-fluid img-poster w-100" alt="Poster">
                    </div>
                    
                    <div class="col-md-7">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase fw-bold text-dark mb-3">
                                <?php echo htmlspecialchars($row['tenPhim']); ?>
                            </h5>
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1 text-muted small">Phòng chiếu</p>
                                    <p class="fw-bold"><i class="bi bi-door-open text-danger"></i> <?php echo htmlspecialchars($row['tenPhong']); ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1 text-muted small">Ghế ngồi</p>
                                    <p class="fw-bold"><i class="bi bi-grid-3x3-gap text-danger"></i> <?php echo htmlspecialchars($row['danhSachGhe']); ?></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="text-muted small"><i class="bi bi-calendar3"></i> <?php echo date('d/m/Y', strtotime($row['ngayChieu'])); ?></span>
                                <span class="text-muted small">| <i class="bi bi-clock"></i> <?php echo date('H:i', strtotime($row['gioChieu'])); ?></span>
                            </div>

                            <div class="mt-3">
                                <span class="h5 text-primary fw-bold"><?php echo number_format($tongThanhToan, 0, ',', '.'); ?> VNĐ</span>
                                <small class="text-muted ms-1">(<?php echo $row['soLuong']; ?> vé <?php echo count($foodList) > 0 ? '+ Bắp nước' : ''; ?>)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 d-flex flex-column justify-content-center align-items-center border-start bg-light p-3">
                        <?php 
                            $statusClass = 'bg-secondary';
                            if($row['trangThai'] == 'Đã xác nhận') $statusClass = 'bg-success';
                            if($row['trangThai'] == 'Chờ thanh toán') $statusClass = 'bg-warning text-dark';
                            if($row['trangThai'] == 'Đã hủy') $statusClass = 'bg-danger';
                        ?>
                        <div class="mb-3 text-center">
                            <span class="badge <?php echo $statusClass; ?> px-4 py-2 mb-2 shadow-sm" style="font-size: 0.9rem;">
                                <?php echo $row['trangThai']; ?>
                            </span>
                            <div class="text-muted extra-small" style="font-size: 0.75rem;">
                                Đã đặt: <?php echo date('d/m/Y H:i', strtotime($row['ngayDat'])); ?>
                            </div>
                        </div>
                        
                        <p class="text-muted mb-3 small">Mã đơn: <strong>#<?php echo $row['bookingId']; ?></strong></p>
                        
                        <button class="btn btn-dark btn-sm w-75 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#ticketDetail_<?php echo $row['bookingId']; ?>">
                            <i class="bi bi-info-circle"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="ticketDetail_<?php echo $row['bookingId']; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title"><i class="bi bi-ticket-detailed"></i> Chi tiết giao dịch #<?php echo $row['bookingId']; ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4 bg-light py-3 rounded border border-dashed">
                                <p class="text-muted mb-1 small">MÃ ĐẶT CHỖ CỦA BẠN</p>
                                <h3 class="text-danger fw-bold mb-0 tracking-widest">CGV-<?php echo $row['bookingId']; ?></h3>
                            </div>
                                                        
                            <hr class="text-muted">

                            <h6 class="fw-bold text-danger"><i class="bi bi-cup-straw"></i> Bắp & Nước</h6>
                            <ul class="list-unstyled small text-muted mb-3">
                                <?php if (empty($foodList)): ?>
                                    <li>- Không có bắp nước đi kèm.</li>
                                <?php else: ?>
                                    <?php foreach ($foodList as $f): ?>
                                        <li class="d-flex justify-content-between mb-1">
                                            <span><?php echo $f['soLuong']; ?>x <?php echo htmlspecialchars($f['tenFood']); ?></span>
                                            <span><?php echo number_format($f['thanhTien'], 0, ',', '.'); ?>đ</span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>

                            <hr class="text-muted">

                            <h6 class="fw-bold text-dark"><i class="bi bi-receipt"></i> Hóa đơn</h6>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Tiền vé (<?php echo $row['soLuong']; ?> ghế):</span>
                                <span><?php echo number_format($tienVe, 0, ',', '.'); ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Tiền bắp nước:</span>
                                <span><?php echo number_format($tienFood, 0, ',', '.'); ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold text-primary mt-2 pt-2 border-top">
                                <span>TỔNG CỘNG:</span>
                                <span class="fs-5"><?php echo number_format($tongThanhToan, 0, ',', '.'); ?>đ</span>
                            </div>
                        </div>
                        
                        <div class="modal-footer bg-light">
                            <?php if($row['trangThai'] == 'Chờ thanh toán'): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="huyVe(<?php echo $row['bookingId']; ?>)">Hủy vé</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/ticket_history.js"></script>
</body>
</html>