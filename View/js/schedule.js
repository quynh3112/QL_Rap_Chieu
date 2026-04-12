const SCHEDULE_API = "/QL_Rap_Chieu/Controllers/schedules.php";
const MOVIE_API = "/QL_Rap_Chieu/Controllers/movies.php";
const ROOM_API = "/QL_Rap_Chieu/Controllers/roomController.php";

let editId = null;

// ================= LOAD DROPDOWN =================
function loadMovies(){
    fetch(MOVIE_API)
    .then(res => res.json())
    .then(data => {
        let html = "<option value=''>-- Chọn phim --</option>";
        data.forEach(m => {
            html += `<option value="${m.movieId}">${m.tenPhim}</option>`;
        });
        document.getElementById("movieId").innerHTML = html;
        document.getElementById("filterMovie").innerHTML = html;
    });
}

function loadRooms(){
    fetch(ROOM_API)
    .then(res => res.json())
    .then(data => {
        let html = "<option value=''>-- Chọn phòng --</option>";
        data.forEach(r => {
            html += `<option value="${r.roomId}">${r.tenPhong}</option>`;
        });
        document.getElementById("roomId").innerHTML = html;
    });
}

// ================= LOAD TABLE =================
function loadSchedules(){
    let movieId = document.getElementById("filterMovie").value;
    let url = SCHEDULE_API + (movieId ? "?movieId=" + movieId : "");

    fetch(url)
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach(s => {
            html += `
            <tr>
                <td>${s.scheduleId}</td>
                <td>${s.tenPhim}</td>
                <td>${s.tenPhong}</td>
                <td>${s.ngayChieu}</td>
                <td>${s.gioChieu}</td>
                <td>${s.giaVe}</td>
                <td>${s.trangThai}</td>
                <td>
                    <button onclick="editSchedule(${s.scheduleId}, ${s.movieId}, ${s.roomId}, '${s.ngayChieu}', '${s.gioChieu}', ${s.giaVe})">Sửa</button>
                    <button onclick="deleteSchedule(${s.scheduleId})">Xóa</button>
                </td>
            </tr>
            `;
        });

        document.getElementById("scheduleTable").innerHTML = html;
    });
}

// ================= GET FORM =================
function getFormData(){
    return {
        movieId: document.getElementById("movieId").value,
        roomId: document.getElementById("roomId").value,
        ngayChieu: document.getElementById("date").value,
        gioChieu: document.getElementById("time").value,
        giaVe: document.getElementById("price").value,
        isCancelled: 0
    };
}

// ================= SAVE (THÊM + SỬA) =================
function saveSchedule(){
    const data = getFormData();

    if(!data.movieId || !data.roomId){
        alert("Chọn phim và phòng!");
        return;
    }

    let method = editId ? "PUT" : "POST";
    let url = editId ? SCHEDULE_API + "?id=" + editId : SCHEDULE_API;

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(r => {
        alert(r.message);
        resetForm();
        loadSchedules();
    })
    .catch(err => console.log(err));
}

// ================= EDIT =================
function editSchedule(id, movieId, roomId, ngay, gio, gia){
    editId = id;

    document.getElementById("movieId").value = movieId;
    document.getElementById("roomId").value = roomId;
    document.getElementById("date").value = ngay;
    document.getElementById("time").value = gio;
    document.getElementById("price").value = gia;
}

// ================= DELETE =================
function deleteSchedule(id){
    if(confirm("Xóa suất chiếu?")){
        fetch(SCHEDULE_API + "?id=" + id, {
            method: "DELETE"
        })
        .then(res => res.json())
        .then(r => {
            alert(r.message);
            loadSchedules();
        });
    }
}

// ================= RESET =================
function resetForm(){
    editId = null;

    document.getElementById("movieId").value = "";
    document.getElementById("roomId").value = "";
    document.getElementById("date").value = "";
    document.getElementById("time").value = "";
    document.getElementById("price").value = "";
}

// ================= INIT =================
window.onload = function(){
    loadMovies();
    loadRooms();
    loadSchedules();

    document.getElementById("filterMovie")
    .addEventListener("change", loadSchedules);
};