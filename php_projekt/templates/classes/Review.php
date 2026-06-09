<?php

class Review {
    private $conn;
    private $table_name = "reviews";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createReview($seller_id, $buyer_id, $order_id, $rating, $comment) {
        $sql = "INSERT INTO " . $this->table_name . " (reviewee_id, reviewer_id, order_id, rating, comment) 
                VALUES (:reviewee_id, :reviewer_id, :order_id, :rating, :comment)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':reviewee_id' => $seller_id,
            ':reviewer_id' => $buyer_id,
            ':order_id' => $order_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
    }

    public function getReviewsBySeller($seller_id) {
        $sql = "SELECT r.*, u.username as buyer_username 
                FROM " . $this->table_name . " r
                JOIN users u ON r.reviewer_id = u.id
                WHERE r.reviewee_id = :reviewee_id
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':reviewee_id' => $seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($seller_id) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM " . $this->table_name . " 
                WHERE reviewee_id = :reviewee_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':reviewee_id' => $seller_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hasReviewedOrder($order_id) {
        $sql = "SELECT id FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt->rowCount() > 0;
    }
}
?>