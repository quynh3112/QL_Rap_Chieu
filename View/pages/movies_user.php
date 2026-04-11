<?php include "../component/header.php"; ?>

<h2 class="title">🎬 Danh sách phim</h2>

<div id="movieList" class="movie-list"></div>

<style>
body {
    background: linear-gradient(120deg, #1f1c2c, #928dab);
    font-family: Arial;
}

/* TITLE */
.title {
    text-align: center;
    color: white;
    margin-top: 20px;
}

/* GRID */
.movie-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    padding: 20px;
}

/* CARD */
.movie-card {
    border-radius: 12px;
    overflow: hidden;
    background: white;
    transition: 0.3s;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.movie-card:hover {
    transform: translateY(-8px) scale(1.03);
}

/* IMAGE */
.movie-card img {
    width: 100%;
    height: 260px;
    object-fit: cover;
}

/* INFO */
.movie-info {
    padding: 10px;
}

.movie-info h4 {
    margin: 5px 0;
    color: #333;
}

.movie-info p {
    font-size: 14px;
    color: #666;
}

/* STATUS */
.status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
    color: white;
}

.sap { background: orange; }
.dang { background: green; }
.ket { background: gray; }
</style>

<script>
const API = "http://localhost:8080/QL_Rap_Chieu/Controllers/movies.php";

function getStatusClass(status){
    if(status === "Sắp chiếu") return "sap";
    if(status === "Đang chiếu") return "dang";
    return "ket";
}

function loadMoviesUser() {
    fetch(API)
        .then(res => res.json())
        .then(data => {
            let html = "";

            data.forEach(m => {
                html += `
                <div class="movie-card" onclick="goDetail(${m.movieId})">
                    <img src="/QL_Rap_Chieu/uploads/${m.img}" 
                         onerror="this.src='https://via.placeholder.com/200x260'">

                    <div class="movie-info">
                        <h4>${m.tenPhim}</h4>
                        <p>${m.thoiLuong} phút</p>
                        <span class="status ${getStatusClass(m.trangThai)}">
                            ${m.trangThai}
                        </span>
                    </div>
                </div>
                `;
            });

            document.getElementById("movieList").innerHTML = html;
        });
}
function goDetail(id){
    window.location.href = "/QL_Rap_Chieu/View/pages/movies_detail.php?id=" + id;
}
loadMoviesUser();
</script>