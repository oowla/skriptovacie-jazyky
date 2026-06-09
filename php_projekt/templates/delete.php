<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

require_once 'classes/Database.php';
require_once 'classes/Item.php';

$db = (new Database())->getConnection();
$itemClass = new Item($db);

$itemClass->deleteItem($_GET['id'], $_SESSION['user_id']);

header("Location: index.php");
exit();
?>