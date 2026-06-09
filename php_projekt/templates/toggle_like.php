<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_logged_in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['item_id'])) {
    echo json_encode(['status' => 'error']);
    exit();
}

$item_id = $data['item_id'];
$user_id = $_SESSION['user_id'];

$host = 'localhost';
$dbname = 'php_projekt';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = :uid AND item_id = :iid");
    $stmt->execute([':uid' => $user_id, ':iid' => $item_id]);
    
    if ($stmt->rowCount() > 0) {
        $conn->prepare("DELETE FROM likes WHERE user_id = :uid AND item_id = :iid")->execute([':uid' => $user_id, ':iid' => $item_id]);
        echo json_encode(['status' => 'unliked']);
    } else {
        $conn->prepare("INSERT INTO likes (user_id, item_id) VALUES (:uid, :iid)")->execute([':uid' => $user_id, ':iid' => $item_id]);
        echo json_encode(['status' => 'liked']);
    }

} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>