<?php
session_start();

require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

$database = new Database();
$db = $database->getConnection();

$itemClass = new Item($db);

$items = $itemClass->getAvailableItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nocturne | Alternative Secondhand</title>
    <link rel="stylesheet" href="../styles.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php (new Header())->render(); ?>

    <section class="hero">
        <div class="hero-text">
            <h1>Curate Your<br>Dark Aesthetic.</h1>
            <p>Buy and sell alternative, gothic, and vintage fashion. Give dark garments a second life.</p>
            <a href="search.php" style="text-decoration: none;">
                <button class="btn-primary">SHOP THE ARCHIVE</button>
            </a>
        </div>
        <div class="hero-image">
            <img src="../images/skull.jpg" alt="Dark Fashion">
        </div>
    </section>

    <main>
        <div class="section-title">
            <h2>New Items</h2>
            <div class="title-line"></div>
        </div>
        
        <div class="product-grid">
            <?php if(isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php elseif(count($items) > 0): ?>
                
                <?php foreach($items as $item): ?>
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
                <p>No items found. Be first to upload something!</p>
            <?php endif; ?>
        </div>
    </main>

    <?php (new Footer())->render(); ?>

    <script src="../app.js"></script>
</body>
</html>