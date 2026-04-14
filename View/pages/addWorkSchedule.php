<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm lịch làm việc</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white">
    <?php include "../component/headerAd.php";?>

    <h2 class="text-2xl font-bold text-center mt-8 text-red-500">
        Thêm lịch làm việc
    </h2>

    <form id="scheduleForm"
        class="max-w-xl mx-auto mt-8 bg-slate-800 p-6 rounded-xl shadow-lg space-y-4">

        <div>
            <label class="block text-sm mb-1 text-slate-300">Chi nhánh</label>
            <select id="branchId" required
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1 text-slate-300">Nhân viên</label>
            <select id="accountId" required disabled
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600">
                <option>Chọn chi nhánh trước</option>
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1 text-slate-300">Ngày làm</label>
            <input type="date" id="ngayLamViec" required
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
        </div>

        <div>
            <label class="block text-sm mb-1 text-slate-300">Ca làm</label>
            <select id="caLam"
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
                <option value="Ca sáng">Ca sáng</option>
                <option value="Ca Chiều">Ca chiều</option>
                <option value="Ca tối">Ca tối</option>
                <option value="Full-time">Full-time</option>
                
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1 text-slate-300">Giờ bắt đầu</label>
                <input type="time" id="gioBatDau" required
                    class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
            </div>

            <div>
                <label class="block text-sm mb-1 text-slate-300">Giờ kết thúc</label>
                <input type="time" id="gioKetThuc" required
                    class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
            </div>
        </div>

        <button type="submit"
            class="w-full py-3 rounded-lg bg-gradient-to-r from-red-300 to-red-500 font-semibold hover:scale-105 transition">
            Thêm lịch
        </button>

    </form>
<div class="max-w-5xl mx-auto mt-10 bg-slate-800 p-4 rounded-xl">

    <h3 class="text-red-400 mb-3">Tìm lịch</h3>

    <div class="grid grid-cols-3 gap-4">
        <input type="date" id="searchDate"
            class="p-2 bg-slate-900 border border-slate-600 rounded">

        <select id="searchBranch"
            class="p-2 bg-slate-900 border border-slate-600 rounded">
            <option value="">Chọn chi nhánh</option>
        </select>

        <button id="searchBtn"
            class="bg-red-500 rounded px-4 py-2">
            Tìm
        </button>
    </div>
</div>

<table class="w-full max-w-5xl mx-auto mt-6 text-center border border-slate-600">
    <thead class="bg-slate-700">
        <tr>
            <th class="p-2">Nhân viên</th>
            <th class="p-2">Chi nhánh</th>
            <th class="p-2">Ngày</th>
            <th class="p-2">Ca</th>
            <th class="p-2">Giờ</th>
        </tr>
    </thead>
    <tbody id="scheduleTable"></tbody>
</table>

    

    <script src="../js/addWorkSchedule.js"></script>

</body>
</html>