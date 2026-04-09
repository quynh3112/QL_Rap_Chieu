document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.getElementById('myDialog');
    const openBtn = document.getElementById('btn-dialog');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('branchForm');
    const tbody = document.getElementById('items');

    let currentId = null;

    openBtn.addEventListener('click', () => {
        form.reset();
        currentId = null;
        dialog.showModal();
    });

    cancelBtn.addEventListener('click', () => dialog.close());

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const address = document.getElementById('address').value.trim();
        const city = document.getElementById('city').value.trim();

        if (!name || !address || !city) {
            alert("Vui lòng nhập đầy đủ thông tin!");
            return;
        }

        const bodyData = {
            tenBranch: name,
            diaChi: address,
            thanhPho: city
        };

        let method = 'POST';
        if (currentId) {
            method = 'PUT';
            bodyData.id = currentId; 
        }

        try {
            const res = await fetch("http://mncinema.test/Controllers/branchController.php", {
                method: method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(bodyData)
            });

            const result = await res.json();
            alert(result.message || (currentId ? "Cập nhật thành công" : "Thêm thành công"));

            if (result.status !== false) {
                form.reset();
                currentId = null;
                dialog.close();
                loadBranches(); 
            }

        } catch (err) {
            console.error(err);
            alert("Lỗi server, vui lòng thử lại.");
        }
    });

    window.editBranch = function(branch) {
        currentId = branch.branchId;
        document.getElementById('name').value = branch.tenBranch;
        document.getElementById('address').value = branch.diaChi;
        document.getElementById('city').value = branch.thanhPho;
        dialog.showModal();
    }

    window.deleteBranch = async function(id) {
        if (!confirm("Bạn có chắc muốn xóa chi nhánh này?")) return;

        try {
            const res = await fetch("http://mncinema.test/Controllers/branchController.php", {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            });
            const result = await res.json();
            alert(result.message);
            if (result.status) loadBranches(); // reload table
        } catch (err) {
            console.error(err);
            alert("Lỗi server khi xóa chi nhánh!");
        }
    }

    async function loadBranches() {
        try {
            const res = await fetch("http://mncinema.test/Controllers/branchController.php");
            const data = await res.json();

            // Tạo tất cả row bằng innerHTML
            tbody.innerHTML = data.map(branch => `
                <tr>
                    <td>${branch.branchId}</td>
                    <td>${branch.tenBranch}</td>
                    <td>${branch.diaChi}</td>
                    <td>${branch.thanhPho}</td>
                    <td>
                        <button onclick='editBranch(${JSON.stringify(branch)})'>Sửa</button>
                        <button onclick='deleteBranch(${branch.branchId})'>Xóa</button>
                    </td>
                </tr>
            `).join("");

        } catch (err) {
            console.error(err);
            alert("Lỗi tải danh sách chi nhánh!");
        }
    }

    loadBranches();
});