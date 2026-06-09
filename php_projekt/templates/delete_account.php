<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'classes/Database.php';
require_once 'classes/User.php';

$db = (new Database())->getConnection();
$userClass = new User($db);

if ($userClass->deleteAccount($_SESSION['user_id'])) {
    $_SESSION = array();
    session_destroy();
    
    header("Location: index.php?msg=account_deleted");
} else {
    header("Location: profile.php?id=" . $_SESSION['user_id'] . "&error=delete_failed");
}
exit();
?>