<?php
@session_start();
?>

<header>
    <div class="img">
        <a href="home.php"><img src="../asset/cgv.png" alt=""></a>
    </div>

    <div>
        <ul class="list">
            <li class="container">
                <h2>Phim</h2>
                <div class="dropdown">
                    <a href="">Phim đang chiếu</a>
                    <a href="">Phim sắp chiếu</a>
                </div>
            </li>

            <li class="container">
                <h2>Rạp CGV</h2>
                <div class="dropdown">
                    <a href="selectCinemas.php">Tất cả các rạp</a>
                    <a href="">Rạp 3D</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="account">
      <?php if(isset($_SESSION['user']) && !empty($_SESSION['user']['hoTen'])): ?>
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