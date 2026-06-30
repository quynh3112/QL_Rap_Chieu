<?php
include "config/db.php";

if ($conn) {
    echo "Kết nối DB OK";
} else {
    echo "Kết nối DB FAIL";
}
?>