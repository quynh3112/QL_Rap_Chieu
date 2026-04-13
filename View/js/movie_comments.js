// Lấy ID phim từ URL
const movieId = new URLSearchParams(window.location.search).get("id");
let allComments = [];

// Xử lý chọn sao 
setupStars('.star-btn', '#ratingValue');
setupStars('.edit-star-btn', '#editRatingValue');

function setupStars(btnClass, inputId) {
    const stars = document.querySelectorAll(btnClass);
    const input = document.querySelector(inputId);
    
    stars.forEach(star => {
        star.onclick = function() {
            const val = this.dataset.value;
            input.value = val;
            stars.forEach(s => s.classList.toggle('active', s.dataset.value <= val));
        };
    });
}

function renderStars(rating) {
    let stars = '';
    for(let i=1; i<=5; i++) stars += `<i class="bi bi-star${i <= rating ? '-fill' : ''} text-warning small"></i> `;
    return stars;
}

// Tải bình luận
async function loadComments() {
    if(!movieId) return;
    try {
        // Biến REVIEW_API và currentUserId được lấy từ thẻ <script> bên file PHP
        const res = await fetch(`${REVIEW_API}?movieId=${movieId}`);
        const result = await res.json();
        let html = '';
        
        if(!result.status) {
            html = `<p class="text-center text-muted py-3">${result.message}</p>`;
        } else {
            allComments = result.data; // Lưu lại dữ liệu
            result.data.forEach(c => {
                let actionBtns = '';
                if(currentUserId === Number(c.accountId)) {
                    actionBtns = `
                        <div class="mt-3 pt-3 border-top border-secondary d-flex justify-content-between align-items-center bg-dark rounded p-2">
                            <span class="text-warning small"><i class="bi bi-info-circle"></i> Đây là bình luận của bạn</span>
                            <div>
                                <button onclick="openEditModal(${c.reviewId})" class="btn btn-warning fw-bold text-dark shadow-sm me-2 px-3">
                                    <i class="bi bi-pencil-square"></i> SỬA 
                                </button>
                                <button onclick="deleteComment(${c.reviewId})" class="btn btn-outline-danger px-3">
                                    <i class="bi bi-trash"></i> XÓA
                                </button>
                            </div>
                        </div>`;
                }
                html += `
                <div class="comment-item">
                    <div class="d-flex justify-content-between">
                        <strong class="text-info"><i class="bi bi-person-circle"></i> ${c.username || 'Khách'}</strong>
                        <div>${renderStars(c.rating)}</div>
                    </div>
                    <div class="comment-date mb-2">${c.reviewDate}</div>
                    <p class="mb-0 text-light">${c.comment}</p>
                    ${actionBtns}
                </div>`;
            });
        }
        document.getElementById("commentsList").innerHTML = html;
    } catch(err) { 
        console.error("Lỗi:", err); 
        document.getElementById("commentsList").innerHTML = '<p class="text-danger">Lỗi khi tải bình luận.</p>';
    }
}

// Mở modal sửa
function openEditModal(reviewId) {
    const review = allComments.find(r => r.reviewId == reviewId);
    if (!review) return;

    document.getElementById('editReviewId').value = review.reviewId;
    document.getElementById('editCommentText').value = review.comment;
    document.getElementById('editRatingValue').value = review.rating;
    
    // Cập nhật hiển thị sao trong modal
    const editStars = document.querySelectorAll('.edit-star-btn');
    editStars.forEach(s => s.classList.toggle('active', s.dataset.value <= review.rating));
    
    try {
        const editModal = new bootstrap.Modal(document.getElementById('editCommentModal'));
        editModal.show();
    } catch (e) {
        alert("Lỗi: Thiếu thư viện Bootstrap JS!");
    }
}

// Lưu cập nhật (PUT)
async function saveEdit() {
    const reviewId = document.getElementById('editReviewId').value;
    const comment = document.getElementById('editCommentText').value;
    const rating = document.getElementById('editRatingValue').value;

    try {
        const res = await fetch(REVIEW_API, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reviewId, rating, comment })
        });
        const result = await res.json();
        if(result.status) {
            // Tắt Modal bằng Bootstrap 5
            const modalElement = document.getElementById('editCommentModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if(modalInstance) modalInstance.hide();
            
            loadComments(); // Tải lại danh sách
        } else { 
            alert(result.message); 
        }
    } catch(err) { 
        alert("Lỗi kết nối máy chủ!"); 
    }
}

// Thêm bình luận (POST)
const commentForm = document.getElementById("commentForm");
if(commentForm) {
    commentForm.onsubmit = async (e) => {
        e.preventDefault();
        try {
            const res = await fetch(REVIEW_API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    movieId: movieId, 
                    accountId: currentUserId, 
                    comment: document.getElementById("commentText").value, 
                    rating: document.getElementById("ratingValue").value 
                })
            });
            const result = await res.json();
            if(result.status) {
                document.getElementById("commentText").value = '';
                // Mặc định trả số sao về 5
                document.getElementById("ratingValue").value = 5;
                document.querySelectorAll('.star-btn').forEach(s => s.classList.add('active'));
                
                loadComments();
            } else { 
                alert(result.message); 
            }
        } catch(err) { 
            alert("Lỗi kết nối máy chủ!"); 
        }
    }
}

// Xóa bình luận (DELETE)
async function deleteComment(reviewId) {
    if(!confirm("Bạn có chắc chắn muốn xóa bình luận này không?")) return;
    try {
        const res = await fetch(REVIEW_API, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reviewId: reviewId }) 
        });
        const result = await res.json();
        if(result.status) {
            loadComments();
        } else { 
            alert(result.message); 
        }
    } catch(err) { 
        alert("Lỗi kết nối máy chủ!"); 
    }
}

// Chạy hàm lấy bình luận ngay khi file JS được tải xong
loadComments();