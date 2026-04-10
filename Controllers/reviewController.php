<?php
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input", true));

    include "../models/review.php";

    $review = new Review();

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case "POST":
            $rating = $data["rating"] ?? null;
            $comment = $data["comment"] ?? null;

            if (!$rating) {
                echo json_encode([
                    "status" => false,
                    "message" => "Vui lòng đánh giá sao!"
                ]);
                exit;
            }

            if (!$comment) {
                echo json_encode([
                    "status" => false,
                    "message" => "Vui lòng viết bình luận!"
                ]);
                exit;
            }

            if ($review->create($data["movieId"], $data["accountId"], $rating, $comment, date('Y-m-d H:i:s'))) {
                echo json_encode([
                    "status" => true,
                    "message" => "Bình luận thành công!"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Bình luận thất bại!"
                ]);
            }

            break;
        
        case "PUT":
            $reviewId = $data["reviewId"] ?? null;
            $rating = $data["rating"] ?? null;
            $comment = $data["comment"] ?? null;

            if (!$rating) {
                echo json_encode([
                    "status" => false,
                    "message" => "Vui lòng đánh giá sao!"
                ]);
                exit;
            }

            if (!$comment) {
                echo json_encode([
                    "status" => false,
                    "message" => "Vui lòng viết bình luận!"
                ]);
                exit;
            }

            if ($review->update($reviewId, $rating, $comment)) {
                echo json_encode([
                    "status" => true,
                    "message" => "Cập nhật bình luận thành công!"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Cập nhật bình luận thất bại!"
                ]);
            }

            break;

        case "DELETE":
            $reviewId = $data["reviewId"] ?? null;

            if ($review->delete($reviewId)) {
                echo json_encode([
                    "status" => true,
                    "message" => "Xóa bình luận thành công!"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Xóa bình luận thất bại!"
                ]);
            }

            break;

        case "GET":
            $reviewId = $data["reviewId"] ?? null;
            $list = [];
            $result = $review -> getall($reviewId);
            
            if ($result && $result -> num_rows > 0) {
                while ($row = $result -> fetch_assoc()) {
                    $list[] = [
                        "rating" => $row['rating'],
                        "comment" => $row['comment'],
                        "review_date" => $row['review_date']
                    ] ;    
                }
                echo json_encode ([
                    "status" => true,
                    "data" => $list
                ]);
            }
            else {
                echo json_encode ([
                    "status" => false,
                    "message" => "Chưa có bình luận nào!"
                ]);
            }
    }
?>