

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/headerAdmin.css">

<header >
    <div class="bar">
        <h2><i class="fa-solid fa-bars"></i></h2>
        <div class="items">
            <p><i class="fa-solid fa-x"></i></p>
           
            <a href="admin_food.php">Quản lý đồ ăn</a>
            <a href="nhanvien.php" style="<?= $_SESSION['user']['role'] === 'Employee' ? '' : 'display:none' ?>">Đặt vé hộ khách</a>
            
            <a href="schedule.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lý lịch chiếu</a>
            <a href="movies.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lý phim</a>
            <a href="admin.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lý booking</a>

            <a href="branch.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lý chi nhánh</a>
            <a href="room.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lý phòng </a>
            <a href="addWorkSchedule.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lí ca làm việc</a>
            <a href="admin_employees.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Quản lý nhân viên</a>
            <a href="tileGhe.php" style="<?= $_SESSION['user']['role'] === 'Admin' ? '' : 'display:none' ?>">Tỉ lệ lấp đầy ghế theo phim</a>
        </div>

    </div>
    <div class="logo">
       <a href="homeQL.php"> <img src="../asset/cgv.png" alt=""></a>
    </div>
    <div class="account">
        <?php if(
            isset($_SESSION['user']) &&
            in_array($_SESSION['user']['role'], ['Admin', 'Manager','Employee']) 
        ): ?>
            <div class='container'>
                <button><?php echo htmlspecialchars($_SESSION['user']['hoTen']); ?></button>
                <div class="dropdown">
                    <a href="profile_admin.php">Thông tin cá nhân</a>
                    <a href="logout.php">Đăng xuất</a>

                </div>
            </div>
        <?php else: ?>
            <button onclick="window.location.href='login.php'">Đăng Nhập</button>
        <?php endif; ?>
    </div>

</header>
<script>
const bar = document.querySelector(".bar h2");
const menu = document.querySelector(".items");
const closeBtn = document.querySelector(".items p");

bar.addEventListener("click", () => {
    menu.classList.toggle("active");
});

closeBtn.addEventListener("click", () => {
    menu.classList.remove("active");
});
</script>
