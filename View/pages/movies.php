<?php include "../component/header.php"; ?>
<div class="container">
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
                <input id="img" placeholder="Ảnh">
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
                <input id="editImg" placeholder="Ảnh">
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
<script src="../js/movies.js"></script>