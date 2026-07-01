<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Admin']);
$currentRole = $_SESSION['user']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý phòng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white">
    <?php include "../component/headerAd.php";?>

<div class="max-w-6xl mx-auto mt-10">

    <div class="flex justify-between mb-6">
        <button id="openBtn"
            class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">
            + Thêm phòng
        </button>

        <select id="filter"
            class="bg-black border border-yellow-400 text-yellow-300 px-3 py-2 rounded-lg">
        </select>
    </div>

    <!-- TABLE -->
    <table class="w-full text-center border border-yellow-400 border-collapse">
        <thead class="bg-red-700 text-yellow-300">
            <tr>
                <th class="p-3 border border-yellow-400">ID</th>
                <th class="p-3 border border-yellow-400">Tên phòng</th>
                <th class="p-3 border border-yellow-400">Loại</th>
                <th class="p-3 border border-yellow-400">Ghế</th>
                <th class="p-3 border border-yellow-400">Hành động</th>
            </tr>
        </thead>

        <tbody id="roomTableBody"></tbody>
    </table>

</div>

<!-- DIALOG -->
<dialog id="myDialog"
    class="rounded-xl p-6 bg-black border border-yellow-400 text-white">

    <form id="roomForm" class="space-y-4 w-80">

        <h2 class="text-center text-yellow-400 font-bold text-lg">
            Thêm / Sửa phòng
        </h2>

        <select id="branch"
            class="w-full p-2 bg-black border border-yellow-400 rounded"></select>

        <input id="tenPhong" placeholder="Tên phòng"
            class="w-full p-2 bg-black border border-yellow-400 rounded">

        <select id="loaiPhong"
            class="w-full p-2 bg-black border border-yellow-400 rounded">
            <option value="VIP">VIP</option>
            <option value="Normal">Normal</option>
        </select>

        <input type="number" id="tongGhe" placeholder="Tổng ghế"
            class="w-full p-2 bg-black border border-yellow-400 rounded">

        <div class="flex justify-between">
            <button type="button" id="cancelBtn"
                class="bg-gray-600 px-4 py-2 rounded">Hủy</button>

            <button type="submit"
                class="bg-red-600 px-4 py-2 rounded">Lưu</button>
        </div>

    </form>
</dialog>

<script src="../js/room.js"></script>

</body>
</html>