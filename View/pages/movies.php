<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../Controllers/auth.php';

checkAuth(['Admin']);
$currentRole = $_SESSION['user']['role'] ?? '';



?>

<?php include "../component/headerAd.php"; ?>

<div class="movie-page">
    <div class="search-box">
        <input id="searchName" placeholder="Tìm tên phim...">
        <button onclick="searchMovie()">Tìm</button>
        <select id="filterStatus">
        <option value="">Tất cả trạng thái</option>
        <option value="Sắp chiếu">Sắp chiếu</option>
        <option value="Đang chiếu">Đang chiếu</option>
        <option value="Kết thúc">Kết thúc</option>
    </select>
    </div>
    <div class="form-area">
        <div class="box">
            <h3>Thêm phim</h3>
            <div class="form-row">
                <input id="tenPhim" placeholder="Tên phim">
                <div class="error" id="errTenPhim"></div>
            </div>
            <div class="form-row">
                <input id="thoiLuong" placeholder="Thời lượng">
                <div class="error" id="errThoiLuong"></div>
            </div>
            <div class="form-row">
                <input id="moTa" placeholder="Mô tả">
                <label class="file-box" id="fileLabel">
        <input type="file" id="img" accept="image/*">
    </label>
                <input id="daoDien" placeholder="Đạo diễn">
                <div class="error" id="errDaoDien"></div>
            </div>
            <div class="form-row">
                <input id="dienVien" placeholder="Diễn viên">
                <div class="error" id="errDienVien"></div>
            </div>
            <div class="form-row">
                <input id="nam" placeholder="Năm">
                <div class="error" id="errNam"></div>
            </div>
            <div class="form-row">
                <select id="trangThai">
                    <option value="Sắp chiếu">Sắp chiếu</option>
                    <option value="Đang chiếu">Đang chiếu</option>
                    <option value="Kết thúc">Kết thúc</option>
                </select>
                <button onclick="addMovie()">Thêm</button>
            </div>
        </div>
        <div class="box" id="editBox" style="display:none;">
            <h3>Sửa phim</h3>
            <input type="hidden" id="editId">
            <div class="form-row">
                <input id="editTenPhim" placeholder="Tên phim">
                <div class="error" id="errEditTenPhim"></div>
            </div>
            <div class="form-row">
                <input id="editThoiLuong" placeholder="Thời lượng">
                <div class="error" id="errEditThoiLuong"></div>
            </div>
            <div class="form-row">
                <input id="editMoTa" placeholder="Mô tả">
                <input type="file" id="editImgFile">
                <input type="hidden" id="editImg">
                <input id="editDaoDien" placeholder="Đạo diễn">
                <div class="error" id="errEditDaoDien"></div>
            </div>
            <div class="form-row">
                <input id="editDienVien" placeholder="Diễn viên">
                <div class="error" id="errEditDienVien"></div>
            </div>
            <div class="form-row">
                <input id="editNam" placeholder="Năm">
                <div class="error" id="errEditNam"></div>
            </div>
            <div class="form-row">
                <select id="editTrangThai">
                    <option value="Sắp chiếu">Sắp chiếu</option>
                    <option value="Đang chiếu">Đang chiếu</option>
                    <option value="Kết thúc">Kết thúc</option>
                </select>
                <button onclick="updateMovie()">Cập nhật</button>
                <button onclick="cancelEdit()">Hủy</button>
            </div>
        </div>
    </div>
    <div class="table-area">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Thời lượng</th>
                    <th>Mô tả</th>
                    <th>Ảnh</th>
                    <th>Đạo diễn</th>
                    <th>Diễn viên</th>
                    <th>Năm</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="movieTable"></tbody>
        </table>
    </div>
</div>
<style>
body {
    font-family: Arial;
    background: linear-gradient(120deg, #1f1c2c, #928dab);
    color: #333;
}

/* TITLE */
.title {
    text-align: center;
    color: white;
    margin-top: 20px;
}

/* SEARCH */
.search-box {
    margin: 20px;
    display: flex;
    gap: 10px;
}

.search-box input,
.search-box select {
    padding: 8px;
    border-radius: 6px;
    border: none;
}

/* FORM */
.form-area {
    display: flex;
    gap: 20px;
    margin: 20px;
}

.box {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 10px;
    color: black;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}

.form-row input,
.form-row select {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* BUTTON */
button {
    padding: 8px 12px;
    margin-top: 5px;
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
.table-area {
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
.status {
    padding: 4px 8px;
    border-radius: 6px;
    color: white;
    font-size: 12px;
}

.sap { background: orange; }
.dang { background: green; }
.ket { background: gray; }

/* IMAGE */
img {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

/* ERROR */
.error {
    color: red;
    font-size: 12px;
}
</style>

<script src="../js/movies.js"></script>