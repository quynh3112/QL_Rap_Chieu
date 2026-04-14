document.addEventListener("DOMContentLoaded", () => {

    const branch = document.getElementById("branchId");
    const account = document.getElementById("accountId");
    const form = document.getElementById("scheduleForm");

    const searchDate = document.getElementById("searchDate");
    const searchBranch = document.getElementById("searchBranch");
    const searchBtn = document.getElementById("searchBtn");

    const table = document.getElementById("scheduleTable");

    // ===== LOAD CHI NHÁNH =====
    async function loadBranch() {
        const res = await fetch("../../Controllers/branchController.php");
        const data = await res.json();

        branch.innerHTML = "<option value=''>Chọn chi nhánh</option>";
        searchBranch.innerHTML = "<option value=''>Chọn chi nhánh</option>";

        data.forEach(b => {
            branch.innerHTML += `<option value="${b.branchId}">${b.tenBranch}</option>`;
            searchBranch.innerHTML += `<option value="${b.branchId}">${b.tenBranch}</option>`;
        });
    }

    // ===== LOAD NHÂN VIÊN =====
    branch.addEventListener("change", async () => {
        const branchId = branch.value;

        if (!branchId) return;

        const res = await fetch(`../../Controllers/accountController.php?branchId=${branchId}`);
        const data = await res.json();

        account.innerHTML = "";
        account.disabled = false;

        data.forEach(a => {
            account.innerHTML += `<option value="${a.accountId}">${a.hoTen}</option>`;
        });
    });

    // ===== RENDER TABLE =====
    function render(data) {
        table.innerHTML = "";

        if (!Array.isArray(data) || data.length === 0) {
            table.innerHTML = "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
            return;
        }

        data.forEach(d => {
            table.innerHTML += `
            <tr class="border-t border-slate-600 hover:bg-slate-700">
                <td class="p-2">${d.hoTen || d.accountId}</td>
                <td class="p-2">${d.branchName || d.branchId}</td>
                <td class="p-2">${d.ngayLamViec}</td>
                <td class="p-2">${d.caLam}</td>
                <td class="p-2">${d.gioBatDau} - ${d.gioKetThuc}</td>
            </tr>`;
        });
    }

    // ===== TÌM =====
    searchBtn.addEventListener("click", async () => {
        const date = searchDate.value;
        const branchId = searchBranch.value;

        if (!date || !branchId) {
            alert("Chọn ngày và chi nhánh!");
            return;
        }

        const res = await fetch(`../../Controllers/workSchedule.php?ngayLamViec=${date}&branchId=${branchId}`);
        const data = await res.json();

        render(data);
    });

    // ===== SUBMIT =====
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const data = {
            accountId: account.value,
            branchId: branch.value,
            ngayLamViec: document.getElementById("ngayLamViec").value,
            caLam: document.getElementById("caLam").value,
            gioBatDau: document.getElementById("gioBatDau").value,
            gioKetThuc: document.getElementById("gioKetThuc").value
        };

        // check giờ
        if (data.gioBatDau >= data.gioKetThuc) {
            alert("Giờ không hợp lệ!");
            return;
        }

        const res = await fetch("../../Controllers/workSchedule.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data)
        });

        const result = await res.json();
        alert(result.message);

        // ===== RESET FORM =====
        if (result.status) {
            const currentBranch = branch.value; // giữ chi nhánh

            form.reset();

            branch.value = currentBranch;

            account.innerHTML = "<option>Chọn chi nhánh trước</option>";
            account.disabled = true;
        }

        // reload bảng nếu đang search
        if (searchDate.value && searchBranch.value) {
            searchBtn.click();
        }
    });

    // INIT
    loadBranch();

});