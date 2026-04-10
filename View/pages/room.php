<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Quản lí phòng</title>
</head>
<body>

    <div>
        <div>
            <button id="openBtn">Thêm Phòng</button>

            <dialog id="myDialog">
                <form id="roomForm">
                    <select name="branchId" id="branch"></select>

                    <input type="text" id="tenPhong" placeholder="Tên phòng" name="tenPhong" required>

                    <select name="loaiPhong" id="loaiPhong">
                        <option value="VIP">VIP</option>
                        <option value="Normal">Normal</option>
                    </select>

                    <input name="tongGhe" type="number" id="tongGhe" placeholder="Tổng ghế" required>

                    <menu>
                        <button type="button" id="cancelBtn">Hủy</button>
                        <button type="submit">Xác nhận</button>
                    </menu>
                </form>
            </dialog>
        </div>
        <div>
            <select name="filterBranch" id="filter">
            </select>
        </div>
        <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên phòng</th>
                <th>Loại phòng</th>
                <th>Tổng ghế</th>
               
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="roomTableBody"></tbody>
    </table>

        
    </div>

    <script src="../js/room.js"></script>

</body>
</html>