<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lịch làm việc</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body class="bg-gray-900 text-white">
<div class="container mx-auto p-6">
  <h1 class="text-3xl font-bold mb-6 text-center">Lịch Làm Việc (Tuần)</h1>

  <div class="flex justify-between mb-4">
    <button onclick="prevWeek()" class="bg-gray-700 px-4 py-2 rounded-xl"><i class="fa-solid fa-angle-left"></i></button>
    <button onclick="nextWeek()" class="bg-gray-700 px-4 py-2 rounded-xl"><i class="fa-solid fa-angle-right"></i></button>
  </div>

  <!-- Thanh tuần -->
  <div id="weekBar" class="grid grid-cols-7 gap-2 mb-6"></div>

  <!-- Danh sách ca làm -->
  <div id="schedule" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
</div>
<script>
  const accountId = <?= $_SESSION['user']['accountId'] ?? 'null' ?>;
</script>

<script src="../js/scheduleWork.js"></script>

</body>
</html>