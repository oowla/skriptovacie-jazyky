<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Item.php';
require_once 'classes/Order.php';
require_once 'classes/Review.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$db = (new Database())->getConnection();
$userClass = new User($db);
$itemClass = new Item($db);
$orderClass = new Order($db);
$reviewClass = new Review($db);

$profile_id = $_GET['id'];

$profile_user = $userClass->getUserById($profile_id);
if (!$profile_user) {
    die("User not found.");
}

$profile_items = $itemClass->getProfileItems($profile_id);

$reviews = $reviewClass->getReviewsBySeller($profile_id);
$rating_data = $reviewClass->getAverageRating($profile_id);
$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'] ?: 0;

$can_review = false;
$unreviewed_orders = [];
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $profile_id) {
    $unreviewed_orders = $orderClass->getUnreviewedOrders($_SESSION['user_id'], $profile_id);
    if (count($unreviewed_orders) > 0) {
        $can_review = true;
    }
}

$review_error = isset($_SESSION['review_error']) ? $_SESSION['review_error'] : null;
$review_success = isset($_SESSION['review_success']) ? $_SESSION['review_success'] : null;
unset($_SESSION['review_error'], $_SESSION['review_success']);

$fake_locations = ["London, UK", "Berlin, Germany", "Prague, CZ", "Underworld", "Salem, MA"];
$fake_location = $fake_locations[$profile_id % count($fake_locations)];
$fake_joined = date('F Y', strtotime('-' . ($profile_id * 3) . ' months'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@<?php echo htmlspecialchars($profile_user['username']); ?> | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-ash);">

    <?php (new Header())->render(); ?>

    <main style="max-width: 1200px; margin: 40px auto 100px auto; padding: 0 5%;">
        
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="profile-info" style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1>@<?php echo htmlspecialchars($profile_user['username']); ?></h1>
                        <div class="profile-meta">
                            <span><i class="fa-solid fa-location-dot"></i> <?php echo $fake_location; ?></span>
                            <span><i class="fa-regular fa-calendar"></i> Joined <?php echo $fake_joined; ?></span>
                        </div>
                        <div class="profile-stats">
                            <strong><?php echo count($profile_items); ?></strong> Active Listings
                        </div>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profile_user['id']): ?>
                        <div style="margin-left: 20px;">
                            <a href="delete_account.php" 
                               onclick="return confirm('WARNING: Are you absolutely sure you want to delete your entire account? This will permanently erase all your listings, photos, messages, and reviews. This action cannot be undone!');" 
                               style="text-decoration: none;">
                                <button class="btn-sell" style="background-color: #ffebee; color: var(--red-crimson); border: 1px solid var(--red-crimson); padding: 10px 15px; font-size: 13px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> DELETE ACCOUNT
                                </button>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="profile-reviews">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--dark-charcoal); padding-bottom: 10px; margin-bottom: 20px;">
                <h3 style="font-family: 'Playfair Display'; font-size: 1.5rem;">
                    Reviews 
                    <?php if ($total_reviews > 0): ?>
                        <span style="color: var(--red-crimson);">★ <?php echo $avg_rating; ?></span>
                    <?php endif; ?>
                </h3>
                <span style="font-size: 13px; color: var(--grey);">(<?php echo $total_reviews; ?> Reviews)</span>
            </div>

            <?php if ($review_success): ?>
                <div class="review-message success" style="background: #d4edda; color: #155724; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <?php echo htmlspecialchars($review_success); ?>
                </div>
            <?php endif; ?>

            <?php if ($review_error): ?>
                <div class="review-message error" style="background: #f8d7da; color: #721c24; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <?php echo htmlspecialchars($review_error); ?>
                </div>
            <?php endif; ?>

            <?php if ($can_review): ?>
                <div class="review-form-card">
                    <h4 style="font-family: 'Playfair Display'; margin-bottom: 15px;">Leave a Review</h4>
                    <form method="POST" action="submit_review.php" class="review-form">
                        <input type="hidden" name="seller_id" value="<?php echo $profile_id; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $unreviewed_orders[0]['id']; ?>">
                        
                        <div class="rating-input">
                            <label style="font-size: 14px; margin-bottom: 6px; display: block;">Rating *</label>
                            <div class="star-rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo $i == 5 ? '' : ''; ?> required>
                                    <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="comment-input" style="margin-top: 12px;">
                            <label style="font-size: 14px; margin-bottom: 6px; display: block;">Comment</label>
                            <textarea name="comment" rows="3" placeholder="Share your experience with this seller..." style="width: 100%; padding: 10px; border: 2px solid var(--dark-charcoal); border-radius: 4px; font-family: inherit; resize: vertical; font-size: 14px;"></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top: 12px; padding: 10px 24px;">Submit Review</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-stars"><?php echo $userClass->renderStars($review['rating']); ?></div>
                        <?php if (!empty($review['comment'])): ?>
                            <p class="review-text">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                        <?php else: ?>
                            <p class="review-text" style="font-style: italic; color: var(--grey);">No comment</p>
                        <?php endif; ?>
                        <div class="review-author">- @<?php echo htmlspecialchars($review['buyer_username']); ?> 
                            <span style="color: var(--grey); font-size: 12px;">· <?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--grey); text-align: center; padding: 30px 0;">This member has no reviews yet.</p>
            <?php endif; ?>
        </div>

        <div class="section-title" style="margin-top: 60px;">
            <h2>Member's Archive</h2>
            <div class="title-line"></div>
        </div>

        <div class="product-grid">
            <?php if(count($profile_items) > 0): ?>
                <?php foreach($profile_items as $item): ?>
                    <?php $imgUrl = !empty($item['image_url']) ? '../' . htmlspecialchars($item['image_url']) : '../images/no-image.jpg'; ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <a href="item.php?id=<?php echo $item['id']; ?>">
                                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            </a>
                            <button class="btn-favorite" onclick="toggleFavorite(this, <?php echo $item['id']; ?>)">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-info">
                            <div class="product-brand">
                                <a href="item.php?id=<?php echo $item['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </div>
                            <div class="product-details">
                                <span class="product-price">$<?php echo htmlspecialchars($item['price']); ?></span>
                                <span class="product-size"><?php echo htmlspecialchars($item['size']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; color: var(--grey);">This member currently has no active listings.</p>
            <?php endif; ?>
        </div>
        
    </main>

   <?php (new Footer())->render(); ?>
    <script src="../app.js"></script>
</body>
</html>