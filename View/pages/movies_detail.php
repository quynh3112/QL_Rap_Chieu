<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?php include "../component/header.php"; ?>

<div id="movieDetail" class="detail-container"></div>
<h3 style="text-align:center; margin-top:20px;">📅 Lịch chiếu</h3>
<div id="scheduleList" style="padding:20px;"></div>
<?php include "movie_comments.php"; ?>
<style>
body {
    background: linear-gradient(135deg, #1f1c2c, #928dab);
    font-family: Arial;
    color: white;
}

/* CONTAINER */
.detail-container {
    max-width: 900px;
    margin: 40px auto;
    display: flex;
    gap: 30px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}

/* IMAGE */
.detail-container img {
    width: 300px;
    height: 420px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.5);
}

/* INFO */
.detail-info {
    flex: 1;
}

.detail-info h2 {
    font-size: 28px;
    margin-bottom: 10px;
}

/* TAG */
.tag {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    margin-bottom: 10px;
}

.sap { background: orange; }
.dang { background: #00c853; }
.ket { background: gray; }

/* TEXT */
.detail-info p {
    margin: 6px 0;
    font-size: 15px;
}

/* MÔ TẢ */
.desc {
    margin-top: 15px;
    padding: 10px;
    background: rgba(0,0,0,0.2);
    border-radius: 10px;
}

/* BUTTON */
.back-btn {
    display: inline-block;
    margin: 20px;
    padding: 10px 15px;
    background: linear-gradient(45deg, #ff9800, #ff5722);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.back-btn:hover {
    transform: scale(1.05);
}
header .container {
    max-width: none !important;
    width: auto !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* Ép thẻ H2 của header không được nhận khoảng lề (margin) của Bootstrap */
header h2 {
    margin-bottom: 0 !important;
    white-space: nowrap !important; /* Chống rớt chữ Rạp CGV */
    line-height: normal !important;
}

/* Đảm bảo icon không bị Bootstrap làm lệch */
header i {
    line-height: inherit !important;
}

</style>

<a href="movies_user.php" class="back-btn">← Quay lại</a>
<script>
const SCHEDULE_API = "../../Controllers/schedules.php";
const API = "../../Controllers/movies.php";
const params = new URLSearchParams(window.location.search);
const id = params.get("id");

function getStatusClass(status){
    if(status === "Sắp chiếu") return "sap";
    if(status === "Đang chiếu") return "dang";
    return "ket";
}

function loadDetail(){
    fetch(API + "?id=" + id)
        .then(res => res.json())
        .then(m => {

            if(!m){
                document.getElementById("movieDetail").innerHTML = "Không tìm thấy phim";
                return;
            }

            let html = `
            <img src="../../uploads/${m.img}" 
                 onerror="this.src='https://via.placeholder.com/300x400'">

            <div class="detail-info">
                <h2>${m.tenPhim}</h2>

                <span class="tag ${getStatusClass(m.trangThai)}">
                    ${m.trangThai}
                </span>

                <p><b>⏱ Thời lượng:</b> ${m.thoiLuong} phút</p>
                <p><b>🎬 Đạo diễn:</b> ${m.daoDien}</p>
                <p><b>🎭 Diễn viên:</b> ${m.dienVien}</p>
                <p><b>📅 Năm:</b> ${m.namSanXuat}</p>

                <div class="desc">
                    <b>📖 Mô tả:</b>
                    <p>${m.moTa ?? "Không có mô tả"}</p>
                </div>
            </div>
            `;

            document.getElementById("movieDetail").innerHTML = html;
        });
}
function getScheduleStatusClass(status){
    if(status === "Sắp diễn ra") return "sap";
    if(status === "Đang chiếu") return "dang";
    if(status === "Đã kết thúc") return "ket";
    if(status === "Đã hủy") return "ket";
    return "";
}
function loadSchedules(){
    console.log("movieId:", id);

    fetch(SCHEDULE_API + "?movieId=" + id)
        .then(res => res.json())
        .then(data => {
            console.log("DATA:", data); 

            let html = "";

            if(!data || data.length === 0){
                html = "<p style='text-align:center'>Không có lịch chiếu</p>";
            }

            data.forEach(s => {
                html += `
                <div style="
                    background: rgba(255,255,255,0.1);
                    padding: 10px;
                    margin-bottom: 10px;
                    border-radius: 10px;
                ">
                    🏢 ${s.tenPhong} <br>
                    📅 ${s.ngayChieu} - ⏰ ${s.gioChieu} <br>
                    💰 ${s.giaVe} VNĐ <br>
                    <span class="tag ${getScheduleStatusClass(s.trangThai)}">
                        ${s.trangThai}
                    </span>
                </div>
                `;
            });

            document.getElementById("scheduleList").innerHTML = html;
        })
        .catch(err => {
            console.error("Lỗi fetch:", err);
        });
}
loadDetail();
loadSchedules();
</script>

