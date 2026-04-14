<?php include "../component/headerAd.php"; ?>
<h2 class="title">🎬 Quản lý suất chiếu</h2>

<!-- FILTER -->
<div class="filter-box">
    <label>Lọc theo phim:</label>
    <select id="filterMovie"></select>
</div>

<!-- FORM -->
<div class="form-box">
    <h3>Thêm / Sửa suất chiếu</h3>

    <select id="movieId"></select>
    <select id="roomId"></select>

    <input type="date" id="date">
    <input type="time" id="time">
    <input type="number" id="price" placeholder="Giá vé">

    <div class="btn-group">
        <button onclick="saveSchedule()">💾 Lưu</button>
        <button onclick="resetForm()">🔄 Reset</button>
    </div>
</div>

<!-- TABLE -->
<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Phim</th>
                <th>Phòng</th>
                <th>Ngày</th>
                <th>Giờ</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="scheduleTable"></tbody>
    </table>
</div>

<!-- STYLE -->
<style>
body {
    font-family: Arial;
    background: linear-gradient(120deg, #1f1c2c, #928dab);
    color: #333;
}
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #333;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    opacity: 0;
    transition: 0.3s;
    z-index: 999;
}

.toast.show {
    opacity: 1;
}

.toast.success { background: #28a745; }
.toast.error { background: #dc3545; }
.toast.warning { background: orange; }

/* TITLE */
.title {
    text-align: center;
    color: white;
    margin-top: 20px;
}

/* FILTER */
.filter-box {
    margin: 20px;
    color: white;
}

/* FORM */
.form-box {
    background: white;
    margin: 20px;
    padding: 20px;
    border-radius: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.form-box h3 {
    width: 100%;
}

.form-box input,
.form-box select {
    padding: 8px;
    flex: 1;
    min-width: 150px;
}

/* BUTTON */
.btn-group {
    width: 100%;
    margin-top: 10px;
}

button {
    padding: 8px 12px;
    margin-right: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background: #6a5acd;
    color: white;
}

button:hover {
    background: #483d8b;
}

/* TABLE */
.table-box {
    margin: 20px;
    background: white;
    padding: 15px;
    border-radius: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #6a5acd;
    color: white;
}

th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

/* STATUS */
.dang {
    background: green;
}

.status {
    padding: 4px 8px;
    border-radius: 6px;
    color: white;
    font-size: 12px;
}

.sap { background: orange; }
.ket { background: gray; }
.huy { background: red; }
</style>

<div id="toast" class="toast"></div>

<!-- SCRIPT -->
<script src="../js/schedule.js"></script>