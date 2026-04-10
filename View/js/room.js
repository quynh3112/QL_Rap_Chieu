document.addEventListener("DOMContentLoaded", () => {
    const dialog = document.getElementById("myDialog");
    const openBtn = document.getElementById("openBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("roomForm");
    const selectBranch = document.getElementById("branch");
    const roomTable = document.getElementById("roomTableBody");
    const filterBranch=document.getElementById('filter')

    let currentId = null;


    openBtn.addEventListener("click", () => {
        form.reset();
        currentId = null;
        dialog.showModal();
    });

  
    cancelBtn.addEventListener("click", () => {
        dialog.close();
    });
   
   
    async function loadBranches() {
        try {
            const res = await fetch("http://mncinema.test/Controllers/branchController.php");
            const branches = await res.json();

            selectBranch.innerHTML = `<option value="">-- Chọn chi nhánh --</option>`;
            filterBranch.innerHTML=`<option value=>-- Chọn chi nhánh --</option>`

            branches.forEach(branch => {
                const option1 = document.createElement("option");
                option1.value = branch.branchId;
                option1.textContent = branch.tenBranch;
                selectBranch.appendChild(option1);

                const option2=document.createElement('option')
                option2.value=branch.branchId
                option2.textContent=branch.tenBranch
                filterBranch.appendChild(option2)
            });

        } catch (error) {
            console.error("Lỗi load branch:", error);
        }
    }
    filterBranch.addEventListener('change',()=>{
        loadRooms(filterBranch.value)


    })
    async function loadRooms(branchId = "") {
    try {
        let url = "http://mncinema.test/Controllers/roomController.php";

        if (branchId) {
            url += `?branchId=${branchId}`;
        }

        const res = await fetch(url);
        const rooms = await res.json();

        roomTable.innerHTML = "";

        rooms.forEach(room => {
            const tr = document.createElement("tr");

            tr.innerHTML = `
                <td>${room.roomId}</td>
                <td>${room.tenPhong}</td>
                <td>${room.loaiPhong}</td>
                <td>${room.tongGhe}</td>
                
                <td>
                    <button class="editBtn">Sửa</button>
                    <button class="deleteBtn">Xóa</button>
                </td>
            `;

            
            tr.querySelector(".editBtn").addEventListener("click", () => {
                currentId = room.roomId;

                form.tenPhong.value = room.tenPhong;
                form.loaiPhong.value = room.loaiPhong;
                form.tongGhe.value = room.tongGhe;
                selectBranch.value = room.branchId;

                dialog.showModal();
            });

            
            tr.querySelector(".deleteBtn").addEventListener("click", async () => {
                if (!confirm("Bạn có chắc muốn xóa?")) return;

                try {
                    const res = await fetch("http://mncinema.test/Controllers/roomController.php", {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            roomId: room.roomId
                        })
                    });

                    const result = await res.json();
                    alert(result.message);

                    loadRooms(filterBranch.value);

                } catch (error) {
                    console.error("Lỗi xóa:", error);
                }
            });

            roomTable.appendChild(tr);
        });

    } catch (error) {
        console.error("Lỗi load room:", error);
    }
}



    
   form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const isEdit = currentId !== null && currentId !== undefined;

    const data = {
        tenPhong: form.tenPhong.value,
        tongGhe: form.tongGhe.value,
        loaiPhong: form.loaiPhong.value,
        branchId: selectBranch.value
    };

    if (isEdit) {
        data.roomId = currentId; 
    }

    const method = isEdit ? "PUT" : "POST";

    console.log("METHOD:", method);
    console.log("DATA:", data);

    try {
        const res = await fetch("http://mncinema.test/Controllers/roomController.php", {
            method: method,
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();
        console.log("RESULT:", result);

        alert(result.message);

        dialog.close();
        loadRooms();

        currentId = null;

    } catch (error) {
        console.error("Lỗi submit:", error);
    }
});
   
    loadBranches();
    loadRooms();
});