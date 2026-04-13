<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm lịch làm việc</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 text-white">
    <?php include "../component/headerAd.php";?>

    <h2 class="text-2xl font-bold text-center mt-8 text-red-500">
        Thêm lịch làm việc
    </h2>

    <form id="scheduleForm"
        class="max-w-xl mx-auto mt-8 bg-slate-800 p-6 rounded-xl shadow-lg space-y-4">

        <!-- Chi nhánh -->
        <div>
            <label class="block text-sm mb-1 text-slate-300">Chi nhánh</label>
            <select id="branchId" required
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
            </select>
        </div>

        <!-- Nhân viên -->
        <div>
            <label class="block text-sm mb-1 text-slate-300">Nhân viên</label>
            <select id="accountId" required disabled
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600">
                <option>Chọn chi nhánh trước</option>
            </select>
        </div>

        <!-- Ngày -->
        <div>
            <label class="block text-sm mb-1 text-slate-300">Ngày làm</label>
            <input type="date" id="ngayLamViec" required
                class="w-full p-2 rounded-lg bg-slate-900 border border-slate-600 focus:border-sky-400 focus:ring-1 focus:ring-sky-400">
        </div>

        <!-- Ca -->
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

        <!-- Giờ -->
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

        <!-- Button -->
        <button type="submit"
            class="w-full py-3 rounded-lg bg-gradient-to-r from-red-300 to-red-500 font-semibold hover:scale-105 transition">
            Thêm lịch
        </button>

    </form>

    <p id="msg" class="text-center mt-4 font-bold"></p>

    <script src="../js/addWorkSchedule.js"></script>

</body>
</html>