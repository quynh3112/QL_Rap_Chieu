document.addEventListener("DOMContentLoaded", () => {

    const dialog = document.getElementById("myDialog");
    const openBtn = document.getElementById("openBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("roomForm");
    const branch = document.getElementById("branch");
    const table = document.getElementById("roomTableBody");
    const filter = document.getElementById("filter");

    let currentId = null;

    openBtn.onclick = () => {
        form.reset();
        currentId = null;
        dialog.showModal();
    };

    cancelBtn.onclick = () => dialog.close();

    // LOAD BRANCH
    async function loadBranch() {
        const res = await fetch("../../Controllers/branchController.php");
        const data = await res.json();

        branch.innerHTML = "<option value=''>Chọn chi nhánh</option>";
        filter.innerHTML = "<option value=''>Tất cả</option>";

        data.forEach(b => {
            branch.innerHTML += `<option value="${b.branchId}">${b.tenBranch}</option>`;
            filter.innerHTML += `<option value="${b.branchId}">${b.tenBranch}</option>`;
        });
    }

    // LOAD ROOM
    async function loadRoom(branchId = "") {
        let url = "../../Controllers/roomController.php";
        if (branchId) url += `?branchId=${branchId}`;

        const res = await fetch(url);
        const data = await res.json();

        table.innerHTML = "";

        data.forEach(r => {
            const tr = document.createElement("tr");

            tr.innerHTML = `
            <td class="p-3 border border-yellow-400">${r.roomId}</td>
            <td class="p-3 border border-yellow-400 font-semibold">${r.tenPhong}</td>
            <td class="p-3 border border-yellow-400">
                <span class="bg-yellow-400 text-black px-2 py-1 rounded">
                    ${r.loaiPhong}
                </span>
            </td>
            <td class="p-3 border border-yellow-400">${r.tongGhe}</td>
            <td class="p-3 border border-yellow-400 space-x-2">
                <button class="edit bg-yellow-400 text-black px-2 py-1 rounded">Sửa</button>
                <button class="delete bg-red-600 px-2 py-1 rounded">Xóa</button>
            </td>
            `;

            // EDIT
            tr.querySelector(".edit").onclick = () => {
                currentId = r.roomId;
                form.tenPhong.value = r.tenPhong;
                form.loaiPhong.value = r.loaiPhong;
                form.tongGhe.value = r.tongGhe;
                branch.value = r.branchId;
                dialog.showModal();
            };

            // DELETE
            tr.querySelector(".delete").onclick = async () => {
                if (!confirm("Xóa phòng?")) return;

                await fetch("../../Controllers/roomController.php", {
                    method: "DELETE",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({roomId: r.roomId})
                });

                loadRoom(filter.value);
            };

            table.appendChild(tr);
        });
    }

    // FILTER
    filter.onchange = () => loadRoom(filter.value);

    // SUBMIT
   form.onsubmit = async (e) => {
    e.preventDefault();

    const tongGhe = parseInt(form.tongGhe.value);

    // 🔥 check số ghế
    if (isNaN(tongGhe) || tongGhe <= 0) {
        alert("Số ghế phải > 0");
        return;
    }

    if (!branch.value) {
        alert("Chọn chi nhánh!");
        return;
    }

    const data = {
        tenPhong: form.tenPhong.value,
        loaiPhong: form.loaiPhong.value,
        tongGhe: tongGhe,
        branchId: branch.value
    };

    if (currentId) data.roomId = currentId;

    try {
        const res = await fetch("../../Controllers/roomController.php", {
            method: currentId ? "PUT" : "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (result.error) {
            alert(result.error);
            return;
        }

        alert("Thành công!");

        dialog.close();
        loadRoom(filter.value);

    } catch (err) {
        alert("Lỗi kết nối server!");
        console.error(err);
    }
};
    loadBranch();
    loadRoom();
});