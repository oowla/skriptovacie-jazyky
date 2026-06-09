<?php

class Order {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($item_id, $buyer_id, $seller_id, $price) {
        $sql = "INSERT INTO " . $this->table_name . " (item_id, buyer_id, seller_id, price) 
                VALUES (:item_id, :buyer_id, :seller_id, :price)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':item_id' => $item_id,
            ':buyer_id' => $buyer_id,
            ':seller_id' => $seller_id,
            ':price' => $price
        ]);
        return $this->conn->lastInsertId();
    }

    public function getOrdersByBuyer($buyer_id) {
        $sql = "SELECT o.*, i.title, i.description, img.image_url 
                FROM " . $this->table_name . " o
                JOIN items i ON o.item_id = i.id
                LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
                WHERE o.buyer_id = :buyer_id
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':buyer_id' => $buyer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersBySeller($seller_id) {
        $sql = "SELECT o.*, i.title, i.description, img.image_url, u.username as buyer_username
                FROM " . $this->table_name . " o
                JOIN items i ON o.item_id = i.id
                LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
                JOIN users u ON o.buyer_id = u.id
                WHERE o.seller_id = :seller_id
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':seller_id' => $seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($order_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUnreviewedOrders($buyer_id, $seller_id) {
        $sql = "SELECT o.* FROM " . $this->table_name . " o
                LEFT JOIN reviews r ON o.id = r.order_id
                WHERE o.buyer_id = :buyer_id AND o.seller_id = :seller_id AND r.id IS NULL
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':buyer_id' => $buyer_id,
            ':seller_id' => $seller_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>