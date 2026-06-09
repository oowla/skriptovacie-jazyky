<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'classes/Order.php';

$database = new Database();
$db = $database->getConnection();

$itemClass = new Item($db);
$orderClass = new Order($db);

$item = $itemClass->getItemDetails($_GET['id']);

if (!$item) {
    die("Listing not found.");
}

if ($item['seller_id'] == $_SESSION['user_id']) {
    die("You cannot buy your own listing.");
}

try {
    $db->beginTransaction();

    if ($itemClass->buyItem($_GET['id'])) {
        $orderClass->createOrder($_GET['id'], $_SESSION['user_id'], $item['seller_id'], $item['price']);
        
        $db->commit();
        
        header("Location: item.php?id=" . $_GET['id'] . "&success=bought");
    } else {
        $db->rollBack();
        echo "This item has already been sold or an error occurred.";
    }
} catch (Exception $e) {
    $db->rollBack();
    echo "Something went wrong: " . $e->getMessage();
}
exit();
?>