<!DOCTYPE html>
<html>
<<<<<<< HEAD
<head>
    <title>Trang chủ</title>
    <link rel="stylesheet" href="../css/header.css">
    <meta charset="UTF-8"/>
    <style>
        body { margin-top: 70px; font-family: Arial, sans-serif; }
        .hero {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            color: white;
        }
        .hero h1 { font-size: 36px; margin-bottom: 12px; }
        .hero p  { font-size: 16px; margin-bottom: 28px; color: #ddd; }
        .btn-book {
            background: #e71a0f;
            color: white;
            border: none;
            padding: 14px 36px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-book:hover { background: #ff2a1f; }
    </style>
</head>
<body>
    <?php include "../component/header.php"; ?>

    <div class="hero">
        <h1>🎬 Chào mừng đến CGV Cinemas</h1>
        <p>Trải nghiệm điện ảnh đỉnh cao - Đặt vé nhanh chóng, tiện lợi</p>
        <a href="user.php" class="btn-book">🎟 Đặt vé ngay</a>
    </div>
</body>
</html>
=======
    <head>
        <title>Trang chủ</title>
        <link rel="stylesheet" href="../css/header.css">
        <meta charset="UTF-8"/>
    </head>
    <body>
        <?php
        include "../component/header.php";
        ?>
        <div style="margin-top:60px">
            <?= include "movies_user.php";?>

        </div>
    </body>
</html>
>>>>>>> origin/dev-food
