<?php
    include "../config/db.php";

    class Review {
        public function create($movieId, $accountId, $rating, $comment, $reviewDate) {
            global $conn;

            $sql = "INSERT INTO review (movieId, accountId, rating, comment, reviewDate)
                    VALUES ('$movieId', '$accountId', '$rating', '$comment', '$reviewDate')";
            
            return $conn->query($sql);
        }

        public function update($reviewId, $rating, $comment) {
            global $conn;

            $sql = "UPDATE review SET rating = '$rating', comment = '$comment' 
                    WHERE reviewId = '$reviewId'";
            
            return $conn->query($sql);
        }

        public function delete($reviewId) {
            global $conn;

            $sql = "DELETE FROM review WHERE reviewId = '$reviewId'";
            
            return $conn->query($sql);
        }

        public function getall($movieId) {
            global $conn;
            // Kết nối bảng review với bảng account để lấy username
            $sql = "SELECT r.*, a.username 
                    FROM review r 
                    LEFT JOIN Account a ON r.accountId = a.accountId 
                    WHERE r.movieId = '$movieId'
                    ORDER BY r.reviewDate DESC"; 
            return $conn->query($sql);
        }
    }
?>