<?php
class Conversation {
    private $conn;
    private $table_name = 'conversations';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserConversations(int $userId): array { 
        $sql = "
            SELECT c.*, i.title,
                   u_buyer.username as buyer_name,
                   u_seller.username as seller_name,
                   img.image_url
            FROM " . $this->table_name . " c
            JOIN items i ON c.item_id = i.id
            JOIN users u_buyer ON c.buyer_id = u_buyer.id
            JOIN users u_seller ON c.seller_id = u_seller.id
            LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
            WHERE c.buyer_id = :user_id OR c.seller_id = :user_id
            ORDER BY c.updated_at DESC, c.id DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConversationForUser(int $conversationId, int $userId): ?array {
        $stmt = $this->conn->prepare(
            "SELECT c.*, i.title,
                    u_buyer.username as buyer_name,
                    u_seller.username as seller_name,
                    img.image_url
             FROM " . $this->table_name . " c
             JOIN items i ON c.item_id = i.id
             JOIN users u_buyer ON c.buyer_id = u_buyer.id
             JOIN users u_seller ON c.seller_id = u_seller.id
             LEFT JOIN item_images img ON i.id = img.item_id AND img.is_primary = 1
             WHERE c.id = :conversation_id
               AND (c.buyer_id = :user_id OR c.seller_id = :user_id)"
        );
        $stmt->execute([':conversation_id' => $conversationId, ':user_id' => $userId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $conversation ?: null;
    }

    public function ensureForItemAndBuyer(int $itemId, int $buyerId): ?int {
        $item = $this->getItemSeller($itemId);
        if (!$item || $item['seller_id'] === $buyerId) {
            return null;
        }

        $sellerId = (int)$item['seller_id'];
        $existing = $this->findByItemBuyerSeller($itemId, $buyerId, $sellerId);
        if ($existing) {
            return (int)$existing['id'];
        }

        return $this->createConversation($itemId, $buyerId, $sellerId);
    }

    public function getConversationById(int $conversationId): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = :conversation_id");
        $stmt->execute([':conversation_id' => $conversationId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $conversation ?: null;
    }

    public function userCanAccess(int $conversationId, int $userId): bool {
        $stmt = $this->conn->prepare(
            "SELECT id FROM " . $this->table_name . "
             WHERE id = :conversation_id
               AND (buyer_id = :user_id OR seller_id = :user_id)"
        );
        $stmt->execute([':conversation_id' => $conversationId, ':user_id' => $userId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTimestamp(int $conversationId): bool {
        $stmt = $this->conn->prepare("UPDATE " . $this->table_name . " SET updated_at = NOW() WHERE id = :conversation_id");
        return $stmt->execute([':conversation_id' => $conversationId]);
    }

    private function getItemSeller(int $itemId): ?array {
        $stmt = $this->conn->prepare('SELECT seller_id FROM items WHERE id = :item_id');
        $stmt->execute([':item_id' => $itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ?: null;
    }

    private function findByItemBuyerSeller(int $itemId, int $buyerId, int $sellerId): ?array {
        $stmt = $this->conn->prepare(
            "SELECT id FROM " . $this->table_name . "
             WHERE item_id = :item_id
               AND buyer_id = :buyer_id
               AND seller_id = :seller_id"
        );
        $stmt->execute([':item_id' => $itemId, ':buyer_id' => $buyerId, ':seller_id' => $sellerId]);
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $conversation ?: null;
    }

    private function createConversation(int $itemId, int $buyerId, int $sellerId): int {
        $stmt = $this->conn->prepare(
            "INSERT INTO " . $this->table_name . " (item_id, buyer_id, seller_id, updated_at)
             VALUES (:item_id, :buyer_id, :seller_id, NOW())"
        );
        $stmt->execute([
            ':item_id' => $itemId,
            ':buyer_id' => $buyerId,
            ':seller_id' => $sellerId,
        ]);
        return (int)$this->conn->lastInsertId();
    }
}