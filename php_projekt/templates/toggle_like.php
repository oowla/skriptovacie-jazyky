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

require_once 'classes/Database.php';
require_once 'classes/Item.php';

$item_id = $data['item_id'];
$user_id = $_SESSION['user_id'];

try {
    $db = (new Database())->getConnection();
    $itemClass = new Item($db);
    
    $status = $itemClass->toggleLike($user_id, $item_id);
    echo json_encode(['status' => $status]);

} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
