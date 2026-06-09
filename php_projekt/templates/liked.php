<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

$db = (new Database())->getConnection();
$itemClass = new Item($db);

$user_id = $_SESSION['user_id'];
$error = '';

$items = $itemClass->getLikedItems($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liked Items | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php (new Header())->render(); ?>

    <main style="max-width: 1400px; margin: 60px auto; padding: 0 5%;">
        
        <div class="section-title">
            <h2>Your Liked Items</h2>
            <div class="title-line"></div>
        </div>
        
        <div class="product-grid">
            <?php if(count($items) > 0): ?>
                
                <?php foreach($items as $item): ?>
                    <?php $imgUrl = !empty($item['image_url']) ? '../' . htmlspecialchars($item['image_url']) : '../images/no-image.jpg'; ?>
                    
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <a href="item.php?id=<?php echo $item['id']; ?>">
                                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            </a>
                            
                            <button class="btn-favorite active" onclick="toggleFavorite(this, <?php echo $item['id']; ?>)">
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
                <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background-color: var(--white); border: 2px solid var(--dark-charcoal);">
                    <i class="fa-regular fa-heart" style="font-size: 3rem; margin-bottom: 20px; color: var(--grey);"></i>
                    <h3 style="margin-bottom: 10px;">You haven't liked any items yet.</h3>
                    <p style="color: var(--grey);">Find pieces for your dark aesthetic and save them here.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php (new Footer())->render(); ?>

    <script src="../app.js"></script>
</body>
</html>