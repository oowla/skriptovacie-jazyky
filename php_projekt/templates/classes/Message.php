<?php
class Message {
    private $conn;
    private $table_name = 'messages';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getMessagesByConversationId(int $conversationId): array {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
        $stmt->execute([':conversation_id' => $conversationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMessage(int $conversationId, int $senderId, string $messageText): bool {
        $stmt = $this->conn->prepare(
            "INSERT INTO " . $this->table_name . " (conversation_id, sender_id, message_text, created_at)
             VALUES (:conversation_id, :sender_id, :message_text, NOW())"
        );
        return $stmt->execute([
            ':conversation_id' => $conversationId,
            ':sender_id' => $senderId,
            ':message_text' => $messageText,
        ]);
    }
}
