document.addEventListener("DOMContentLoaded", () => {
    const branchVip = document.getElementById('branches');
    const movies = document.getElementById('movies');

    // 🔹 Load danh sách chi nhánh có phòng VIP
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
                item.style.cursor = "pointer";

                item.onclick = () => {
                    renderFilms(b.branchId);
                };

                branchVip.appendChild(item);
            });

        } catch (err) {
            console.log("Lỗi loadBranchVip:", err);
        }
    }

    // 🔹 Load phim theo chi nhánh VIP
    async function renderFilms(branchId) {
        try {
            console.log("BranchId:", branchId);

            const res = await fetch(`../../Controllers/branchController.php?vip=${branchId}`);
            const data = await res.json();

            console.log("DATA:", data);

            movies.innerHTML = "";

            // ❌ nếu API trả object lỗi
            if (!Array.isArray(data)) {
                const p = document.createElement('p');
                p.textContent = data.message || "Không có dữ liệu";
                movies.appendChild(p);
                return;
            }

            // ❌ mảng rỗng
            if (data.length === 0) {
                const p = document.createElement('p');
                p.textContent = "Không có phim";
                movies.appendChild(p);
                return;
            }

            // ✅ render phim
            data.forEach((m, index) => {
                console.log("Film:", index, m);

                const item = document.createElement('div');
                item.className = "item";

                const img = document.createElement("img");
                img.src = m.img || "default.jpg"; // tránh lỗi ảnh

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