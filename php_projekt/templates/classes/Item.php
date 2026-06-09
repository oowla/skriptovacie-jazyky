<?php

class Item {
    private $conn;
    private $table_name = "items";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAvailableItems() {
        $sql = "
            SELECT i.id, i.title, i.price, i.size, img.image_url 
            FROM " . $this->table_name . " i
            LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
            WHERE i.item_status = 'available'
            ORDER BY i.created_at DESC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buyItem($item_id) {
        $sql = "UPDATE " . $this->table_name . " SET item_status = 'sold' WHERE id = :id AND item_status = 'available'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $item_id]);
        return $stmt->rowCount() > 0;
    }

    public function searchItems($query = '', $category_id = '') {
        $sql = "SELECT i.*, img.image_url FROM " . $this->table_name . " i
                LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
                WHERE i.item_status = 'available'";
        $params = [];

        if (!empty($query)) {
            $sql .= " AND (i.title LIKE :query OR i.description LIKE :query OR i.brand LIKE :query)";
            $params[':query'] = "%$query%";
        }
        if (!empty($category_id) && is_numeric($category_id)) {
            $sql .= " AND i.category_id = :cat_id";
            $params[':cat_id'] = $category_id;
        }
        $sql .= " ORDER BY i.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createItem($seller_id, $cat_id, $title, $desc, $price, $size, $brand, $condition) {
        $sql = "INSERT INTO " . $this->table_name . " 
                (seller_id, category_id, title, description, price, size, brand, condition_status, item_status) 
                VALUES (:sid, :cid, :title, :desc, :price, :size, :brand, :cond, 'available')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':sid' => $seller_id, ':cid' => $cat_id, ':title' => $title, ':desc' => $desc,
            ':price' => $price, ':size' => $size, ':brand' => $brand, ':cond' => $condition
        ]);
        return $this->conn->lastInsertId();
    }

    public function addImage($item_id, $image_url, $is_primary) {
        $sql = "INSERT INTO item_images (item_id, image_url, is_primary) VALUES (:iid, :url, :primary)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':iid' => $item_id, ':url' => $image_url, ':primary' => $is_primary]);
    }

    public function deleteItem($item_id, $seller_id) {
        $stmt = $this->conn->prepare("SELECT id FROM " . $this->table_name . " WHERE id = :id AND seller_id = :sid");
        $stmt->execute([':id' => $item_id, ':sid' => $seller_id]);
        
        if ($stmt->rowCount() > 0) {
            $img_stmt = $this->conn->prepare("SELECT image_url FROM item_images WHERE item_id = :id");
            $img_stmt->execute([':id' => $item_id]);
            foreach ($img_stmt->fetchAll() as $img) {
                $path = ltrim($img['image_url'], '../');
                if (file_exists($path)) unlink($path);
            }
            $this->conn->prepare("DELETE FROM item_images WHERE item_id = :id")->execute([':id' => $item_id]);
            $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id")->execute([':id' => $item_id]);
            return true;
        }
        return false;
    }

    public function getItemByIdAndSeller($item_id, $seller_id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = :id AND seller_id = :seller_id");
        $stmt->execute([':id' => $item_id, ':seller_id' => $seller_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateItem($item_id, $seller_id, $title, $description, $price, $size) {
        $sql = "UPDATE " . $this->table_name . " 
                SET title = :title, description = :description, price = :price, size = :size 
                WHERE id = :id AND seller_id = :seller_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':price' => $price,
            ':size' => $size,
            ':id' => $item_id,
            ':seller_id' => $seller_id
        ]);
    }

    public function getItemDetails($item_id) {
        $sql = "
            SELECT i.*, img.image_url, u.username, c.name as category_name
            FROM " . $this->table_name . " i
            LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
            LEFT JOIN users u ON i.seller_id = u.id
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.id = :id
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $item_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItemImages($item_id) {
        $sql = "SELECT image_url FROM item_images WHERE item_id = :item_id ORDER BY is_primary DESC, id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':item_id' => $item_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLikedItems($user_id) {
        $sql = "
            SELECT i.id, i.title, i.price, i.size, img.image_url 
            FROM " . $this->table_name . " i
            JOIN likes l ON i.id = l.item_id
            LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
            WHERE l.user_id = :user_id
            ORDER BY l.created_at DESC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemsBySeller($seller_id) {
        $sql = "SELECT id, title, price, created_at FROM " . $this->table_name . " WHERE seller_id = :seller_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':seller_id' => $seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProfileItems($seller_id) {
        $sql = "
            SELECT i.id, i.title, i.price, i.size, img.image_url 
            FROM " . $this->table_name . " i
            LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
            WHERE i.seller_id = :seller_id AND i.item_status = 'available'
            ORDER BY i.created_at DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':seller_id' => $seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>