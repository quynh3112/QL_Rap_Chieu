document.addEventListener("DOMContentLoaded", () => {

    const branchSelect = document.getElementById("branchId");
    const accountSelect = document.getElementById("accountId");
    const form = document.getElementById("scheduleForm");
    const msg = document.getElementById("msg");

    async function loadBranches() {
        const res = await fetch("../../Controllers/branchController.php");
        const data = await res.json();

        data.forEach(b => {
            const opt = document.createElement("option");
            opt.value = b.branchId;
            opt.textContent = b.tenBranch;
            branchSelect.appendChild(opt);
        });
    }

    loadBranches();

    branchSelect.addEventListener("change", async () => {
        const branchId = branchSelect.value;

        const res = await fetch(`../../Controllers/accountController.php?branchId=${branchId}`);
        const data = await res.json();

        accountSelect.innerHTML = "";
        accountSelect.disabled = false;

        data.forEach(acc => {
            const opt = document.createElement("option");
            opt.value = acc.accountId;
            opt.textContent = acc.hoTen;
            accountSelect.appendChild(opt);
        });
    });

    // submit
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const data = {
            accountId: accountSelect.value,
            branchId: branchSelect.value,
            ngayLamViec: document.getElementById("ngayLamViec").value,
            caLam: document.getElementById("caLam").value,
            gioBatDau: document.getElementById("gioBatDau").value,
            gioKetThuc: document.getElementById("gioKetThuc").value
        };

        if (data.gioBatDau >= data.gioKetThuc) {
            msg.textContent = "Giờ không hợp lệ!";
            msg.className = "text-center mt-4 text-red-400";
            return;
        }

        const res = await fetch("../../Controllers/workSchedule.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data)
        });

        const result = await res.json();

        msg.textContent = result.message;
        msg.className = "text-center mt-4 text-green-400";
    });

});