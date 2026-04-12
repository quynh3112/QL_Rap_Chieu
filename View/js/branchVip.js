document.addEventListener("DOMContentLoaded", () => {
    const branchVip = document.getElementById('branches');
    const movies = document.getElementById('movies');

<<<<<<< HEAD
=======
    // 🔹 Load danh sách chi nhánh có phòng VIP
>>>>>>> dev-food
    async function loadBranchVip() {
        try {
            const res = await fetch('../../Controllers/branchVipController.php');
            const data = await res.json();

            branchVip.innerHTML = "";

            if (!Array.isArray(data)) {
                branchVip.innerHTML = "<p>Lỗi load chi nhánh</p>";
                return;
            }

            data.forEach(b => {
                const item = document.createElement('p');
                item.textContent = b.tenBranch;
<<<<<<< HEAD
                item.className="chiNhanh"
                item.style.cursor = "pointer";

                item.onclick = () => {
                    document.querySelectorAll('.chiNhanh').forEach(i=>{
                        i.classList.remove('active');
                    })
                    item.classList.add('active')
=======
                item.style.cursor = "pointer";

                item.onclick = () => {
>>>>>>> dev-food
                    renderFilms(b.branchId);
                };

                branchVip.appendChild(item);
            });

        } catch (err) {
            console.log("Lỗi loadBranchVip:", err);
        }
    }

<<<<<<< HEAD
=======
    // 🔹 Load phim theo chi nhánh VIP
>>>>>>> dev-food
    async function renderFilms(branchId) {
        try {
            console.log("BranchId:", branchId);

            const res = await fetch(`../../Controllers/branchController.php?vip=${branchId}`);
            const data = await res.json();

<<<<<<< HEAD

            movies.innerHTML = "";

            if (!Array.isArray(data)) {
                alert(data.message || "Không có dữ liệu");
                return;
            }

            if (data.length === 0) {
                const p = document.createElement('p');
                p.textContent = "Không có phim";
                p.style.textAlign="center"
=======
            console.log("DATA:", data);

            movies.innerHTML = "";

            // ❌ nếu API trả object lỗi
            if (!Array.isArray(data)) {
                const p = document.createElement('p');
                p.textContent = data.message || "Không có dữ liệu";
>>>>>>> dev-food
                movies.appendChild(p);
                return;
            }

<<<<<<< HEAD
=======
            // ❌ mảng rỗng
            if (data.length === 0) {
                const p = document.createElement('p');
                p.textContent = "Không có phim";
                movies.appendChild(p);
                return;
            }

            // ✅ render phim
>>>>>>> dev-food
            data.forEach((m, index) => {
                console.log("Film:", index, m);

                const item = document.createElement('div');
                item.className = "item";

                const img = document.createElement("img");
<<<<<<< HEAD
                img.src = m.img ; 
=======
                img.src = m.img || "default.jpg"; // tránh lỗi ảnh
>>>>>>> dev-food

                const title = document.createElement('h3');
                title.textContent = m.tenPhim;

                const duration = document.createElement('h5');
                duration.textContent = m.thoiLuong + " phút";

                item.appendChild(img);
                item.appendChild(title);
                item.appendChild(duration);

                movies.appendChild(item);
            });

        } catch (err) {
            console.log("Lỗi renderFilms:", err);
        }
    }

    loadBranchVip();
});