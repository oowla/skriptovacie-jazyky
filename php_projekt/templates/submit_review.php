<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

require_once 'classes/Database.php';
require_once 'classes/Order.php';
require_once 'classes/Review.php';

$seller_id = $_POST['seller_id'] ?? null;
$order_id = $_POST['order_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = trim($_POST['comment'] ?? '');
$buyer_id = $_SESSION['user_id'];

if (!$seller_id || !$order_id || !$rating) {
    $_SESSION['review_error'] = "Fill in all the required fields.";
    header("Location: profile.php?id=" . $seller_id);
    exit();
}

$rating = intval($rating);
if ($rating < 1 || $rating > 5) {
    $_SESSION['review_error'] = "Rating must be between 1 and 5.";
    header("Location: profile.php?id=" . $seller_id);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$orderClass = new Order($db);
$reviewClass = new Review($db);

$order = $orderClass->getOrderById($order_id);
if (!$order || $order['buyer_id'] != $buyer_id || $order['seller_id'] != $seller_id) {
    $_SESSION['review_error'] = "This order does not belong to you.";
    header("Location: profile.php?id=" . $seller_id);
    exit();
}

if ($reviewClass->hasReviewedOrder($order_id)) {
    $_SESSION['review_error'] = "You have already reviewed this order.";
    header("Location: profile.php?id=" . $seller_id);
    exit();
}

if ($reviewClass->createReview($seller_id, $buyer_id, $order_id, $rating, $comment)) {
    $_SESSION['review_success'] = "Review has been added!";
} else {
    $_SESSION['review_error'] = "Something went wrong while adding the review.";
}

header("Location: profile.php?id=" . $seller_id);
exit();
?>