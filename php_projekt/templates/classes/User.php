<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password_hash FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        return false;
    }

    public function register($username, $email, $password) {
        $stmt_check = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt_check->execute([':email' => $email]);
        if ($stmt_check->rowCount() > 0) {
            return "Email is already taken.";
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
        
        if ($stmt->execute([':username' => $username, ':email' => $email, ':password_hash' => $hashed_password])) {
            return "success";
        }
        return "Registration failed.";
    }

    public function requestPasswordReset($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        
        if ($stmt->rowCount() === 0) {
            return false;
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $reset_token = bin2hex(random_bytes(32));
        $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare("UPDATE users SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id");
        $stmt->execute([
            ':token' => $reset_token,
            ':expires' => $reset_expires,
            ':id' => $user['id']
        ]);

        $base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/php_projekt/templates/';
        $reset_link = $base_url . "reset_password.php?token=" . $reset_token;
        return $reset_link;
    }

    public function validateResetToken($token) {
        $stmt = $this->conn->prepare("SELECT id, email FROM users WHERE password_reset_token = :token AND password_reset_expires > NOW()");
        $stmt->execute([':token' => $token]);
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function resetPassword($token, $new_password) {
        $user = $this->validateResetToken($token);
        if (!$user) {
            return "Invalid or expired reset token.";
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password_hash = :password, password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id");
        
        if ($stmt->execute([':password' => $hashed_password, ':id' => $user['id']])) {
            return "success";
        }
        return "Password reset failed. Please try again.";
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, username FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteAccount($user_id) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
                SELECT img.image_url 
                FROM item_images img 
                JOIN items i ON img.item_id = i.id 
                WHERE i.seller_id = :user_id
            ");
            $stmt->execute([':user_id' => $user_id]);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $path = ltrim($row['image_url'], '../');
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $stmt_items = $this->conn->prepare("SELECT id FROM items WHERE seller_id = :user_id");
            $stmt_items->execute([':user_id' => $user_id]);
            $items = $stmt_items->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($items)) {
                $in_items = implode(',', $items);
                $this->conn->exec("DELETE FROM item_images WHERE item_id IN ($in_items)");
                $this->conn->exec("DELETE FROM likes WHERE item_id IN ($in_items)");
                $this->conn->exec("DELETE FROM orders WHERE item_id IN ($in_items)");
            }

            $this->conn->prepare("DELETE FROM likes WHERE user_id = :user_id")->execute([':user_id' => $user_id]);
            $this->conn->prepare("DELETE FROM reviews WHERE reviewer_id = :user_id OR reviewee_id = :user_id")->execute([':user_id' => $user_id]);
            $this->conn->prepare("DELETE FROM orders WHERE buyer_id = :user_id")->execute([':user_id' => $user_id]);
            $this->conn->prepare("DELETE FROM messages WHERE sender_id = :user_id")->execute([':user_id' => $user_id]);
            $this->conn->prepare("DELETE FROM conversations WHERE buyer_id = :user_id OR seller_id = :user_id")->execute([':user_id' => $user_id]);

            $this->conn->prepare("DELETE FROM items WHERE seller_id = :user_id")->execute([':user_id' => $user_id]);
            $this->conn->prepare("DELETE FROM users WHERE id = :user_id")->execute([':user_id' => $user_id]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>