const API = "../../Controllers/movies.php";
function setError(id, msg) {
    const el = document.getElementById(id);
    if (el) el.innerText = msg;
}
function clearErrors(selector = ".error") {
    document.querySelectorAll(selector).forEach(e => e.innerText = "");
}
function loadMovies() {
    fetch(API)
        .then(res => res.json())
        .then(data => {
            let html = "";
            data.forEach(m => {
                html += `
                <tr>
                    <td>${m.movieId}</td>
                    <td>${m.tenPhim}</td>
                    <td>${m.thoiLuong}</td>
                    <td>${m.moTa ?? ""}</td>
                    <td>${m.img ? `<img src="../../uploads/${m.img}" width="60">` : "Không có"}</td>
                    <td>${m.daoDien ?? ""}</td>
                    <td>${m.dienVien ?? ""}</td>
                    <td>${m.namSanXuat}</td>
                    <td>${m.trangThai}</td>
                    <td>
                        <button onclick="editMovie(${m.movieId})">Sửa</button>
                        <button onclick="deleteMovie(${m.movieId})">Xóa</button>
                    </td>
                </tr>`;
            });
            document.getElementById("movieTable").innerHTML = html;
        });
}
function addMovie() {
    clearErrors("#addBox .error, .error");
    const tenPhim = document.getElementById("tenPhim").value.trim();
    const thoiLuong = document.getElementById("thoiLuong").value.trim();
    const daoDien = document.getElementById("daoDien").value.trim();
    const dienVien = document.getElementById("dienVien").value.trim();
    const nam = document.getElementById("nam").value.trim();
    const img = document.getElementById("img").files[0];
    let ok = true;
    if (!tenPhim) {
        setError("errTenPhim", "Tên phim không được trống");
        ok = false;
    }
    if (!thoiLuong || isNaN(thoiLuong) || thoiLuong <= 0) {
        setError("errThoiLuong", "Thời lượng phải > 0");
        ok = false;
    }
    if (!daoDien) {
        setError("errDaoDien", "Nhập đạo diễn");
        ok = false;
    }
    if (!dienVien) {
        setError("errDienVien", "Nhập diễn viên");
        ok = false;
    }
    if (!nam || isNaN(nam) || nam < 1900) {
        setError("errNam", "Năm không hợp lệ");
        ok = false;
    }
    if (!ok) return;
    const formData = new FormData();
    formData.append("tenPhim", tenPhim);
    formData.append("thoiLuong", thoiLuong);
    formData.append("moTa", document.getElementById("moTa").value);
    formData.append("daoDien", daoDien);
    formData.append("dienVien", dienVien);
    formData.append("namSanXuat", nam);
    formData.append("trangThai", document.getElementById("trangThai").value);

    if (img) {
    formData.append("img", img);
    }
    fetch(API, {
        method: "POST",
        body: formData
    })
        .then(() => {
            loadMovies();
            clearAddForm();
        });
}
function deleteMovie(id) {
    if (confirm("Xóa phim này?")) {
        fetch(API + "?id=" + id, { method: "DELETE" })
            .then(() => loadMovies());
    }
}
function editMovie(id) {
    fetch(API + "?id=" + id)
        .then(res => res.json())
        .then(m => {
            document.getElementById("editBox").style.display = "block";

            document.getElementById("editId").value = m.movieId;
            document.getElementById("editTenPhim").value = m.tenPhim || "";
            document.getElementById("editThoiLuong").value = m.thoiLuong || "";
            document.getElementById("editMoTa").value = m.moTa || "";
            document.getElementById("editImg").value = m.img || "";
            document.getElementById("editDaoDien").value = m.daoDien || "";
            document.getElementById("editDienVien").value = m.dienVien || "";
            document.getElementById("editNam").value = m.namSanXuat || "";
            document.getElementById("editTrangThai").value = m.trangThai;
        });
}
function updateMovie() {
    clearErrors("#editBox .error");

    const id = document.getElementById("editId").value;
    const tenPhim = document.getElementById("editTenPhim").value.trim();
    const thoiLuong = document.getElementById("editThoiLuong").value.trim();
    const daoDien = document.getElementById("editDaoDien").value.trim();
    const dienVien = document.getElementById("editDienVien").value.trim();
    const nam = document.getElementById("editNam").value.trim();

    let ok = true;

    if (!tenPhim) {
        setError("errEditTenPhim", "Tên phim không được trống");
        ok = false;
    }
    if (!thoiLuong || isNaN(thoiLuong) || thoiLuong <= 0) {
        setError("errEditThoiLuong", "Thời lượng phải > 0");
        ok = false;
    }
    if (!daoDien) {
        setError("errEditDaoDien", "Nhập đạo diễn");
        ok = false;
    }
    if (!dienVien) {
        setError("errEditDienVien", "Nhập diễn viên");
        ok = false;
    }
    if (!nam || isNaN(nam) || nam < 1900) {
        setError("errEditNam", "Năm không hợp lệ");
        ok = false;
    }
    if (!ok) return;
    const formData = new FormData();
    formData.append("tenPhim", tenPhim);
    formData.append("thoiLuong", thoiLuong);
    formData.append("moTa", document.getElementById("editMoTa").value);
    formData.append("daoDien", daoDien);
    formData.append("dienVien", dienVien);
    formData.append("namSanXuat", nam);
    formData.append("trangThai", document.getElementById("editTrangThai").value);

    const newImg = document.getElementById("editImgFile")?.files[0];
    const oldImg = document.getElementById("editImg").value;

    if (newImg) {
        formData.append("img", newImg); 
    } else {
        formData.append("oldImg", oldImg); 
    }

    fetch(API + "?id=" + id, {
        method: "POST", 
        body: formData
    })
    .then(() => {
        loadMovies();
        cancelEdit();
    });
}
function cancelEdit() {
    document.getElementById("editBox").style.display = "none";
}
function clearAddForm() {
    document.getElementById("tenPhim").value = "";
    document.getElementById("thoiLuong").value = "";
    document.getElementById("moTa").value = "";
    document.getElementById("img").value = "";
    document.getElementById("daoDien").value = "";
    document.getElementById("dienVien").value = "";
    document.getElementById("nam").value = "";
}
function searchMovie() {
    const name = document.getElementById("searchName").value;
    const status = document.getElementById("filterStatus").value;
    fetch(API + "?name=" + name + "&status=" + status)
        .then(res => res.json())
        .then(data => {
            let html = "";
            data.forEach(m => {
                html += `
                <tr>
                    <td>${m.movieId}</td>
                    <td>${m.tenPhim}</td>
                    <td>${m.thoiLuong}</td>
                    <td>${m.moTa ?? ""}</td>
                    <td>${m.img ? `<img src="../../uploads/${m.img}" width="60">` : "Không có"}</td>
                    <td>${m.daoDien ?? ""}</td>
                    <td>${m.dienVien ?? ""}</td>
                    <td>${m.namSanXuat}</td>
                    <td>${m.trangThai}</td>
                    <td>
                        <button onclick="editMovie(${m.movieId})">Sửa</button>
                        <button onclick="deleteMovie(${m.movieId})">Xóa</button>
                    </td>
                </tr>`;
            });
            document.getElementById("movieTable").innerHTML = html;
        });
}
function autoFilter() {
    const name = document.getElementById("searchName").value.trim();
    const status = document.getElementById("filterStatus").value;

    const params = new URLSearchParams();

    if (name) params.append("name", name);
    if (status) params.append("status", status);

    fetch(API + "?" + params.toString())
        .then(res => res.json())
        .then(data => {
            let html = "";
            data.forEach(m => {
                html += `
                <tr>
                    <td>${m.movieId}</td>
                    <td>${m.tenPhim}</td>
                    <td>${m.thoiLuong}</td>
                    <td>${m.moTa ?? ""}</td>
                    <td>${m.img ? `<img src="../../uploads/${m.img}" width="60">` : "Không có"}</td>
                    <td>${m.daoDien ?? ""}</td>
                    <td>${m.dienVien ?? ""}</td>
                    <td>${m.namSanXuat}</td>
                    <td>${m.trangThai}</td>
                    <td>
                        <button onclick="editMovie(${m.movieId})">Sửa</button>
                        <button onclick="deleteMovie(${m.movieId})">Xóa</button>
                    </td>
                </tr>`;
            });

            document.getElementById("movieTable").innerHTML = html;
        });
}

window.onload = function () {
    loadMovies();
    document.getElementById("searchName").addEventListener("input", autoFilter);
    document.getElementById("filterStatus").addEventListener("change", autoFilter);
};