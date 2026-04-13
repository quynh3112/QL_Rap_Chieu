<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Customer') {
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
    <title>Thông tin cá nhân | CGV Cinemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --cgv-red: #e71a0f; }
        body { background-color: #fdfcf0; }
        .profile-card { border: none; border-radius: 15px; overflow: hidden; }
        .profile-header { background: var(--cgv-red); color: white; padding: 30px; text-align: center; }
        .profile-avatar { width: 100px; height: 100px; background: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--cgv-red); margin-bottom: 10px; border: 4px solid rgba(255,255,255,0.3); }
        .btn-cgv { background-color: var(--cgv-red); color: white; border: none; }
        .btn-cgv:hover { background-color: #b3140b; color: white; }
        .info-label { color: #666; font-size: 0.9rem; margin-bottom: 2px; }
        .info-value { font-weight: 600; color: #333; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card profile-card shadow">
                    <div class="profile-header">
                        <div class="profile-avatar"><i class="bi bi-person-fill"></i></div>
                        <h4 class="mb-0"><?php echo $user['username']; ?></h4>
                        <small class="opacity-75">Thành viên thân thiết</small>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-12">
                                <p class="info-label">Họ và tên</p>
                                <p class="info-value"><?php echo $user['hoTen']; ?></p>
                            </div>
                            <div class="col-12">
                                <p class="info-label">Email</p>
                                <p class="info-value"><?php echo $user['email']; ?></p>
                            </div>
                            <div class="col-12">
                                <p class="info-label">Số điện thoại</p>
                                <p class="info-value"><?php echo $user['sdt'] ?: 'Chưa cập nhật'; ?></p>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-cgv" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square"></i> Chỉnh sửa thông tin
                            </button>
                            <a href="home.php" class="btn btn-outline-secondary">
                                <i class="bi bi-house-door"></i> Quay lại trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateProfileForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập nhật hồ sơ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="acc_id" value="<?php echo $user['accountId']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Tên người dùng</label>
                            <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="hoTen" value="<?php echo $user['hoTen']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="sdt" value="<?php echo $user['sdt']; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" name="password" placeholder="Để trống nếu không muốn đổi">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-cgv">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/profile_customer.js"></script>
</body>
</html>