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

            $sql = "SELECT * FROM review WHERE movieId = '$movieId'";

            return $conn->query($sql);
        }
    }
?>