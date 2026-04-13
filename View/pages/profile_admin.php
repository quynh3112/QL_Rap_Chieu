<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'Admin' && $_SESSION['user']['role'] !== 'Employee' && $_SESSION['user']['role'] !== 'Manager')) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ quản trị | CGV Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --admin-blue: #0d6efd; --admin-dark: #212529; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Sidebar giả lập hoặc Header Admin */
        .admin-header { background: var(--admin-dark); color: white; padding: 20px 0; margin-bottom: 30px; border-bottom: 4px solid var(--admin-blue); }
        
        .profile-card { border: none; border-radius: 10px; }
        .profile-img-sidebar { 
            width: 120px; height: 120px; 
            background: #e9ecef; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            font-size: 4rem; color: var(--admin-blue);
            margin: 0 auto 15px;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .badge-role { font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; }
        .info-box { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--admin-blue); }
        .label-custom { color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        .value-custom { font-size: 1.1rem; color: #333; margin-top: 5px; }
        .admin-logo { height: 45px; width: auto; object-fit: contain; display: block;}
        .admin-header { padding: 8px 0 !important; }
    </style>
</head>
<body>

    <div class="admin-header py-2"> 
        <div class="container d-flex justify-content-between align-items-center">
            <a href="homeQL.php"> <img src="../Asset/cgv.png" alt="CGV Admin Logo" class="admin-logo"></a>
            <a href="homeQL.php" class="btn btn-outline-light btn-sm">Quay lại Dashboard</a>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card profile-card shadow-sm text-center p-4 mb-4">
                    <div class="profile-img-sidebar">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <h4><?php echo $user['username']; ?></h4>
                    <div class="mb-3">
                        <span class="badge bg-primary badge-role"><?php echo $user['role']; ?></span>
                    </div>
                    <hr>
                    <p class="text-muted small">ID nhân viên: #<?php echo $user['accountId']; ?></p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card profile-card shadow-sm p-4">
                    <h5 class="mb-4 text-primary">Thông tin tài khoản</h5>
                    
                    <div class="row">
                        <div class="col-md-6 info-box">
                            <div class="label-custom">Họ và tên</div>
                            <div class="value-custom"><?php echo $user['hoTen']; ?></div>
                        </div>
                        <div class="col-md-6 info-box">
                            <div class="label-custom">Email liên hệ</div>
                            <div class="value-custom"><?php echo $user['email']; ?></div>
                        </div>
                        <div class="col-md-6 info-box">
                            <div class="label-custom">Số điện thoại</div>
                            <div class="value-custom"><?php echo $user['sdt'] ?: '---'; ?></div>
                        </div>
                        <div class="col-md-6 info-box">
                            <div class="label-custom">Chi nhánh làm việc</div>
                            <div class="value-custom">
                                <?php echo ($user['branchId'] && $user['branchId'] != 'NULL') ? "Chi nhánh ID: " . $user['branchId'] : "Toàn hệ thống"; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAdminModal">
                            <i class="bi bi-gear-fill"></i> Sửa đổi thông tin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="employeeForm">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalTitle">Thông tin cá nhân</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="emp_accountId" value="<?php echo $user['accountId']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="emp_hoTen" value="<?php echo $user['hoTen']; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="emp_username" value="<?php echo $user['username']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="emp_sdt" value="<?php echo $user['sdt']; ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="emp_email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Mật khẩu</label>
                            <input type="password" class="form-control" id="emp_password" placeholder="Nhập mật khẩu mới (để trống nếu không muốn đổi)">
                            <small class="text-muted" id="passwordNote"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu dữ liệu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/profile_admin.js"></script> 
</body>
</html>