<?php
    $isLoggedIn = isset($_SESSION['user']);
    $currentUserId = $isLoggedIn ? $_SESSION['user']['accountId'] : 'null';
    $currentUsername = $isLoggedIn ? $_SESSION['user']['username'] : '';
?>

<div class="comments-container">
    <h3 class="mb-4 text-warning"><i class="bi bi-chat-right-text"></i> Bình luận & Đánh giá</h3>

    <?php if ($isLoggedIn): ?>
        <div class="comment-box shadow-sm mb-5">
            <form id="commentForm">
                <div class="d-flex justify-content-between mb-2">
                    <strong><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($currentUsername); ?></strong>
                    <div class="d-flex align-items-center">
                        <label class="me-2 text-warning mb-0 small">Đánh giá:</label>
                        <div id="starSelection" class="star-selection">
                            <i class="bi bi-star-fill star-btn" data-value="1"></i>
                            <i class="bi bi-star-fill star-btn" data-value="2"></i>
                            <i class="bi bi-star-fill star-btn" data-value="3"></i>
                            <i class="bi bi-star-fill star-btn" data-value="4"></i>
                            <i class="bi bi-star-fill star-btn" data-value="5"></i>
                        </div>
                        <input type="hidden" id="ratingValue" value="5">
                    </div>
                </div>
                <textarea id="commentText" class="form-control bg-dark text-white border-secondary mb-2" rows="3" style="resize: none; height: 100px;" placeholder="Chia sẻ cảm nghĩ của bạn về phim..." required></textarea>
                <div class="text-end">
                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-4">Gửi bình luận</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary bg-transparent border-secondary text-center text-white mb-5">
            Bạn cần <a href="login.php" class="text-warning fw-bold">Đăng nhập</a> để bình luận.
        </div>
    <?php endif; ?>

    <div id="commentsList">
        <div class="text-center text-muted"><div class="spinner-border spinner-border-sm"></div> Đang tải...</div>
    </div>
</div>

<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-warning shadow-lg" style="box-shadow: 0 0 20px rgba(255,193,7,0.3) !important;">
            <div class="modal-header border-secondary bg-black">
                <h4 class="modal-title text-warning fw-bold"><i class="bi bi-pencil-square"></i> CHỈNH SỬA BÌNH LUẬN</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editReviewId">
                <div class="mb-4 d-flex align-items-center p-3 bg-black rounded border border-secondary">
                    <h6 class="me-3 text-warning mb-0">Đánh giá lại:</h6>
                    <div id="editStarSelection" class="star-selection fs-4">
                        <i class="bi bi-star-fill edit-star-btn" data-value="1"></i>
                        <i class="bi bi-star-fill edit-star-btn" data-value="2"></i>
                        <i class="bi bi-star-fill edit-star-btn" data-value="3"></i>
                        <i class="bi bi-star-fill edit-star-btn" data-value="4"></i>
                        <i class="bi bi-star-fill edit-star-btn" data-value="5"></i>
                    </div>
                    <input type="hidden" id="editRatingValue">
                </div>
                <h6 class="text-warning">Nội dung bình luận:</h6>
                <textarea id="editCommentText" class="form-control bg-black text-white border-warning p-3 fs-5" rows="4" style="resize: none;" required></textarea>
            </div>
            <div class="modal-footer border-secondary bg-black">
                <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" onclick="saveEdit()" class="btn btn-warning fw-bold px-5 text-dark">LƯU THAY ĐỔI</button>
            </div>
        </div>
    </div>
</div>

<style>
.comments-container { max-width: 900px; margin: 0 auto 40px auto; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); padding: 25px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.4); color: #fff; }
.comment-box, .comment-item { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); }
.comment-item { margin-bottom: 15px; }
.star-rating { color: #ffc107; }
.star-btn, .edit-star-btn { color: #555; font-size: 1.2rem; margin-right: 2px; cursor: pointer; transition: 0.2s; }
.star-btn.active, .edit-star-btn.active { color: #ffc107 !important; }
.comment-date { font-size: 0.8rem; color: #aaa; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const currentUserId = <?php echo $currentUserId; ?>;
    const REVIEW_API = "../../Controllers/reviewController.php"; 
</script>

<script src="../js/movie_comments.js"></script>