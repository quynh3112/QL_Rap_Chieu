<?php
@session_start();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/header.css">


<header>
    <div class="img">
        <a href="hehe.php"><img src="../asset/cgv.png" alt=""></a>
    </div>

    <div>
        <ul class="list">
            <li class="container">
                <h2><i class="fa-solid fa-film"></i> Phim</h2>
                <div class="dropdown">
                    <a href="movies_user.php">Phim đang chiếu</a>
                    <a href="">Phim sắp chiếu</a>
                </div>
            </li>

            <li class="container">
                <h2><i class="fa-solid fa-building"></i> Rạp CGV</h2>
                <div class="dropdown">
                    <a href="selectCinemas.php">Tất cả các rạp</a>
                    <a href="cinemasVip.php">Rạp 3D</a>
                </div>
            </li>
            <li class="container">
                <h2 onclick="window.location.href='food.php'"><i class="fa-solid fa-utensils"></i> Đồ ăn</h2>
            </li>
        </ul>
    </div>

    <div class="account">
      <?php if(isset($_SESSION['user']) && !empty($_SESSION['user']['hoTen'])): ?>
    <div class='container'>
        <button><?php echo htmlspecialchars($_SESSION['user']['hoTen']); ?></button>
        <div class="dropdown">
            <a href="profile_customer.php">Thông tin cá nhân</a>
            <a href="logout.php">Đăng xuất</a>
        </div>
    </div>
<?php else: ?>
    <button onclick="window.location.href='login.php'">Đăng Nhập</button>
<?php endif; ?>
    </div>
</header>