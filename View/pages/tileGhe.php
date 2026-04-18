<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Admin']);
$currentRole = $_SESSION['user']['role'] ?? '';

include "../component/headerAd.php";


?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tỉ lệ lấp đầy theo phim</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white ">

<div class="max-w-3xl mx-auto">

    <h2 class="text-2xl font-bold mb-4"> Tỉ lệ lấp đầy ghế theo phim</h2>

    <!-- LIST -->
    <div id="list" class="space-y-3"></div>

</div>

<script>
const BASE = "../../Controllers";

async function loadTiLePhim() {
    try {
        const res = await fetch(`${BASE}/seatController.php?tile_phim=1`);
        const data = await res.json();

        renderList(data);

    } catch (err) {
        console.error(err);
    }
}

function renderList(data) {
    const container = document.getElementById('list');

    container.innerHTML = data.map(d => {
        let color = "bg-red-500";
        if (d.tiLeLapDay >= 80) color = "bg-green-500";
        else if (d.tiLeLapDay >= 50) color = "bg-yellow-500";

        return `
            <div class="bg-slate-800 p-4 rounded-xl shadow">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">${d.tenPhim}</span>
                    <span>${d.tiLeLapDay}%</span>
                </div>

                <!-- progress bar -->
                <div class="w-full bg-slate-600 h-3 rounded">
                    <div class="${color} h-3 rounded" style="width:${d.tiLeLapDay}%"></div>
                </div>
            </div>
        `;
    }).join('');
}

document.addEventListener("DOMContentLoaded", loadTiLePhim);
</script>

</body>
</html>