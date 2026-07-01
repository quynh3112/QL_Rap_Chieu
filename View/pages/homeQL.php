<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>DASHBOARD</title>
        <link rel="stylesheet" href="../css/headerAdmin.css">
    </head>
    <body>
        <?php
        include "../component/headerAd.php"
        ?>
        <?php include "scheduleWork.php"?>
    </body>
</html>