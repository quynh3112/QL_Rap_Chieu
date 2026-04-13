<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Quản lý phòng</title>

    <link rel="stylesheet" href="../css/branch.css">
</head>

<body>

    <div class="main">

       

            <button id="openBtn"> Thêm Phòng</button>

            <select name="filterBranch" id="filter">
                <option value="">-- Lọc chi nhánh --</option>
            </select>

        

        <dialog id="myDialog">
            <form id="roomForm">

                <select name="branchId" id="branch" required></select>

                <input 
                    type="text" 
                    id="tenPhong" 
                    name="tenPhong" 
                    placeholder="Tên phòng" 
                    required
                >

                <select name="loaiPhong" id="loaiPhong">
                    <option value="VIP">VIP</option>
                    <option value="Normal">Normal</option>
                </select>

                <input 
                    type="number" 
                    id="tongGhe" 
                    name="tongGhe" 
                    placeholder="Tổng ghế" 
                    required
                >

                <!-- Buttons -->
                <menu>
                    <button type="button" id="cancelBtn">Hủy</button>
                    <button type="submit">Xác nhận</button>
                </menu>

            </form>

        </dialog>

     
        <table class="table">

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