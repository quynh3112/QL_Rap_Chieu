document.addEventListener("DOMContentLoaded", () => {
    const branchVip = document.getElementById('branches');
    const movies = document.getElementById('movies');

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
                item.className="chiNhanh"
                item.style.cursor = "pointer";

                item.onclick = () => {
                    document.querySelectorAll('.chiNhanh').forEach(i=>{
                        i.classList.remove('active');
                    })
                    item.classList.add('active')
                    renderFilms(b.branchId);
                };

                branchVip.appendChild(item);
            });

        } catch (err) {
            console.log("Lỗi loadBranchVip:", err);
        }
    }

    async function renderFilms(branchId) {
        try {
            console.log("BranchId:", branchId);

            const res = await fetch(`../../Controllers/branchController.php?vip=${branchId}`);
            const data = await res.json();


            movies.innerHTML = "";

            if (!Array.isArray(data)) {
              alert( data.message || "Không có dữ liệu")
                return;
            }

            if (data.length === 0) {
                const p = document.createElement('p');
                p.textContent = "Không có phim";
                p.style.textAlign="center"
                movies.appendChild(p);
                return;
            }

            data.forEach((m, index) => {
                console.log("Film:", index, m);

                const item = document.createElement('div');
                item.className = "item";

                const img = document.createElement("img");
                 img.src=`../../uploads/${m.img}`

                const title = document.createElement('h3');
                title.textContent = m.tenPhim;

                const duration = document.createElement('h5');
                duration.textContent = m.thoiLuong + " phút";

                item.appendChild(img);
                item.appendChild(title);
                item.appendChild(duration);
                item.onclick=()=>{
                    window.location.href = `movies_detail.php?id=${m.movieId}`;
                }

                movies.appendChild(item);
            });

        } catch (err) {
            console.log("Lỗi renderFilms:", err);
        }
    }

    loadBranchVip();
});