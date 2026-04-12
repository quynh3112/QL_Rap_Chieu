<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Quản lí chi nhánh</title>
</head>
<body>
    <div>
        <button id="btn-dialog">Thêm Chi Nhánh</button>
        <dialog id="myDialog">
            <form id="branchForm">
                <input id="name"  type="text" required placeholder="Tên chi nhánh">
                <input id="address"  type="text" required placeholder="Địa chỉ">
                <input id="city" type="text" required placeholder="Thành phố">
                <menu>
                    <button type="button" id="cancelBtn">Hủy</button>
                    <button type="submit" id="confirmBtn">Xác nhận</button>
                </menu>
            </form>
        </dialog>
    </div>
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TÊN CHI NHÁNH</th>
                    <th>ĐỊA CHỈ</th>
                    <th>THÀNH PHỐ</th>
                </tr>
            </thead>
            <tbody id="items">

            </tbody>
        </table>

    </div>

    <script src="../js/branch.js"></script>
</body>
</html>