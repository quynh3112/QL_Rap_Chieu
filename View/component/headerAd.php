<?php
session_start();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/headerAdmin.css">

<header >
    <div class="bar">
        <h2><i class="fa-solid fa-bars"></i></h2>
        <div class="items">
            <p><i class="fa-solid fa-x"></i></p>
            <a href="">Quản lý phim</a>
            <a href="">Quản lý xuất chiếu </a>
            <a href="">Quản Lý hóa đơn</a>
            <a href="">Quản lý chi nhánh</a>
            <a href="">Quản lý phòng </a>
            <a href="">Quản lí nhân viên</a>
            <a href="">Quản lí ca làm việc</a>
            <a href="">Doanh thu theo từng rạp</a>
        </div>

    </div>
    <div class="logo">
        <img src="../asset/cgv.png" alt="">
    </div>
    <div class="account">
        <?php if(
            isset($_SESSION['user']) &&
            in_array($_SESSION['user']['role'], ['Admin', 'Manager'])
        ): ?>
            <div class='container'>
                <button><?php echo htmlspecialchars($_SESSION['user']['hoTen']); ?></button>
                <div class="dropdown">
                    <a href="">Thông tin cá nhân</a>
                    <a href="">Đăng xuất</a>

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
