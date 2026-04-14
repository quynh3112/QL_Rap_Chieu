const SCHEDULE_API = "../../Controllers/schedules.php";
const MOVIE_API = "../../Controllers/movies.php";
const ROOM_API = "../../Controllers/roomController.php";
let editId = null;
function saveSchedule(){
    if(editId){
        updateSchedule(); // đang sửa
    } else {
        addSchedule(); // thêm mới
    }
}
function loadMovies(){
    fetch(MOVIE_API)
        .then(res => res.json())
        .then(data => {
            let options = "<option value=''>-- Chọn phim --</option>";
            data.forEach(m => {
                options += `<option value="${m.movieId}">${m.tenPhim}</option>`;
            });
            document.getElementById("movieId").innerHTML = options;
            document.getElementById("filterMovie").innerHTML = options;
        });
}

function loadRooms(){
    fetch(ROOM_API)
        .then(res => res.json())
        .then(data => {
            let options = "<option value=''>-- Chọn phòng --</option>";
            data.forEach(r => {
                options += `<option value="${r.roomId}">${r.tenPhong}</option>`;
            });
            document.getElementById("roomId").innerHTML = options;
        });
}
function loadSchedules(){
    const movieId = document.getElementById("filterMovie").value || "";

    let url = SCHEDULE_API;
    if(movieId) url += "?movieId=" + movieId;

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
                    <td>
                        <span class="status ${getStatusClass(s.trangThai)}"> ${s.trangThai}
    </span>
                    </td>
                    <td>
                        <button onclick="editSchedule(${s.scheduleId}, ${s.movieId}, ${s.roomId}, '${s.ngayChieu}', '${s.gioChieu}', ${s.giaVe})">Sửa</button>
                        <button onclick="deleteSchedule(${s.scheduleId})">Xóa</button>
                        <button onclick="cancelSchedule(${s.scheduleId})">Hủy</button>
                        
                    </td>
                </tr>
                `;
            });

            document.getElementById("scheduleTable").innerHTML = html;
        });
}
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
function addSchedule(){
    const data = getFormData();

    if(!data.movieId || !data.roomId){
        showToast("Chọn phim và phòng!", "error");
        return;
    }
if(!data.ngayChieu || !data.gioChieu){
    showToast("Thiếu ngày hoặc giờ", "error");
    return;
}
    fetch(SCHEDULE_API, {
        method: "POST",
        headers: {
        "Content-Type": "application/json"
    },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(r => {
        showToast(r.message, "success");
        resetForm();
        loadSchedules();
    });
}
function editSchedule(id, movieId, roomId, ngay, gio, gia){
    editId = id;

    document.getElementById("movieId").value = movieId;
    document.getElementById("roomId").value = roomId;
    document.getElementById("date").value = ngay;
    document.getElementById("time").value = gio;
    document.getElementById("price").value = gia;
}
function updateSchedule(){
    if(!editId){
        showToast("Chưa chọn suất chiếu", "error");
        return;
    }

    const data = getFormData();

    fetch(SCHEDULE_API + "?id=" + editId, {
        method: "PUT",
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(r => {
        showToast(r.message, "success");
        resetForm();
        loadSchedules();
    });
}
function deleteSchedule(id){
    if(confirm("Xóa suất chiếu?")){
        fetch(SCHEDULE_API + "?id=" + id, {
            method: "DELETE"
        })
        .then(res => res.json())
        .then(r => {
            showToast(r.message, "success");
            loadSchedules();
        });
    }
}
function resetForm(){
    editId = null;

    document.getElementById("movieId").value = "";
    document.getElementById("roomId").value = "";
    document.getElementById("date").value = "";
    document.getElementById("time").value = "";
    document.getElementById("price").value = "";
}
function getStatusClass(status){
    if(status === "Sắp diễn ra") return "sap";
    if(status === "Đã kết thúc") return "ket";
    if(status === "Đã hủy") return "huy";
    if(status === "Đang chiếu") return "dang";
    return "";
}
function cancelSchedule(id){
    if(confirm("Hủy suất chiếu?")){
        fetch(SCHEDULE_API + "?id=" + id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                isCancelled: 1
            })
        })
        .then(res => res.json())
        .then(r => {
            showToast(r.message, "success");
            loadSchedules();
        });
    }
}
function resetFilter(){
    document.getElementById("filterMovie").value = "";
    loadSchedules();
}
function showToast(message, type = "success"){
    const toast = document.getElementById("toast");
    toast.innerText = message;
    toast.className = "toast show " + type;

    setTimeout(() => {
        toast.className = "toast";
    }, 3000);
}
window.onload = function(){
    loadMovies();
    loadRooms();
    loadSchedules();

    document.getElementById("filterMovie")
        .addEventListener("change", loadSchedules);
};