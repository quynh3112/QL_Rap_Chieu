<?php
session_start();
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = ($isLoggedIn && $_SESSION['user']['role'] === 'Admin');
// Test khi là Admin:
// $isLoggedIn = true; 
// $isAdmin = true;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .search-box { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px; border: 1px solid #dee2e6; }
    </style>
</head>
<body class="bg-black text-white">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-red fw-bold"><i class="bi bi-person-badge-fill"></i> QUẢN LÝ TÀI KHOẢN</h2>
            
            <?php if ($isAdmin): ?>
            <button class="btn bg-red btn-success shadow-sm" onclick="openAddModal()">
                <i class="bi bi-plus-lg"></i> Thêm nhân viên mới
            </button>
            <?php endif; ?>
        </div>

        <div class="search-box">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tìm kiếm nhanh</label>
                    <input type="text" id="searchName" class="form-control" placeholder="Nhập tên, username hoặc email..." onkeyup="filterEmployees()">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Lọc theo chức vụ</label>
                    <select id="filterRole" class="form-select" onchange="filterEmployees()">
                        <option value="">-- Tất cả chức vụ --</option>
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="Employee">Employee</option>
                        <option value="Customer">Customer</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilter()">Làm mới</button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Họ và Tên</th>
                            <th>Username</th>
                            <th>Liên hệ</th>
                            <th>Chức vụ</th>
                            <th>Chi nhánh</th>
                            <?php if ($isAdmin): ?>
                            <th class="text-center">Thao tác</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="employeeForm">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTitle">Thông tin nhân viên</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="emp_accountId">
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="emp_hoTen" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="emp_username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="emp_sdt">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="emp_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Mật khẩu</label>
                            <input type="password" class="form-control" id="emp_password" placeholder="Nhập mật khẩu mới (để trống nếu không muốn đổi)">
                            <small class="text-muted" id="passwordNote"></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chức vụ</label>
                                <select class="form-select" id="emp_role">
                                    <option value="Admin">Admin</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Employee">Employee</option>
                                    <option value="Customer">Customer</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chi nhánh ID</label>
                                <input type="number" class="form-control" id="emp_branchId">
                            </div>
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
    <?php endif; ?>

    <script>
        // Truyền biến PHP sang JS để xử lý ẩn/hiện nút trong bảng
        const USER_IS_ADMIN = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin_employees.js"></script>
</body>
</html>