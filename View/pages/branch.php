<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Quản lí chi nhánh</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white">
    <?php include "../component/headerAd.php";?>

<div class="max-w-6xl mx-auto mt-10">

    <!-- ===== TOP ===== -->
    <div class="flex justify-between mb-6">
        <button id="btn-dialog"
            class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">
            + Thêm Chi Nhánh
        </button>
    </div>

    <!-- ===== TABLE ===== -->
    <table class="w-full text-center border border-yellow-400 border-collapse">
        <thead class="bg-red-700 text-yellow-300">
            <tr>
                <th class="p-3 border border-yellow-400">ID</th>
                <th class="p-3 border border-yellow-400">Tên chi nhánh</th>
                <th class="p-3 border border-yellow-400">Địa chỉ</th>
                <th class="p-3 border border-yellow-400">Thành phố</th>
                <th class="p-3 border border-yellow-400">Thao tác</th>
            </tr>
        </thead>

        <tbody id="items"></tbody>
    </table>

</div>

<!-- ===== DIALOG ===== -->
<dialog id="myDialog"
    class="rounded-xl p-6 bg-black border border-yellow-400 text-white">

    <form id="branchForm" class="space-y-4 w-80">

        <h2 class="text-center text-yellow-400 font-bold text-lg">
            Thêm / Sửa chi nhánh
        </h2>

        <input id="name" type="text" placeholder="Tên chi nhánh" required
            class="w-full p-2 bg-black border border-yellow-400 rounded">

        <input id="address" type="text" placeholder="Địa chỉ" required
            class="w-full p-2 bg-black border border-yellow-400 rounded">

        <input id="city" type="text" placeholder="Thành phố" required
            class="w-full p-2 bg-black border border-yellow-400 rounded">

        <div class="flex justify-between">
            <button type="button" id="cancelBtn"
                class="bg-gray-600 px-4 py-2 rounded">
                Hủy
            </button>

            <button type="submit"
                class="bg-red-600 px-4 py-2 rounded">
                Lưu
            </button>
        </div>

    </form>
</dialog>

<script src="../js/branch.js"></script>

</body>
</html>