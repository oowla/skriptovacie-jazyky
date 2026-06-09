<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$db = (new Database())->getConnection();
$itemClass = new Item($db);

$item = $itemClass->getItemDetails($_GET['id']);

if (!$item) {
    die("The ad was not found or was deleted.");
}

$item_images = $itemClass->getItemImages($item['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($item['title']); ?> | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php (new Header())->render(); ?>

    <main class="vinted-item-main">
        <div class="big-white-wrapper">
            
            <div class="item-gallery" style="position: relative; display: flex; align-items: center; justify-content: center; background: var(--white); height: 600px; overflow: hidden; border: 2px solid var(--dark-charcoal); box-shadow: 6px 6px 0px var(--dark-charcoal);">
                
                <?php if (count($item_images) > 1): ?>
                    <button class="gallery-nav gallery-prev" onclick="prevImage()" style="position: absolute; left: 20px; z-index: 10; background: rgba(0,0,0,0.5); color: white; border: none; font-size: 24px; padding: 10px 15px; cursor: pointer; border-radius: 4px;">❮</button>
                <?php endif; ?>
                
                <div class="gallery-container" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative;">
                    <?php if (!empty($item_images)): ?>
                        <?php foreach ($item_images as $index => $img): ?>
                            <img 
                                src="../<?php echo htmlspecialchars($img['image_url']); ?>" 
                                alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                class="gallery-image" 
                                onclick="openFullscreen(<?php echo $index; ?>)"
                                style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>; width: 100%; height: 100%; object-fit: cover; cursor: pointer;"
                            >
                        <?php endforeach; ?>
                    <?php else: ?>
                        <img src="../images/no-image.jpg" alt="No image" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php endif; ?>
                </div>
                
                <?php if (count($item_images) > 1): ?>
                    <button class="gallery-nav gallery-next" onclick="nextImage()" style="position: absolute; right: 20px; z-index: 10; background: rgba(0,0,0,0.5); color: white; border: none; font-size: 24px; padding: 10px 15px; cursor: pointer; border-radius: 4px;">❯</button>
                <?php endif; ?>

                <?php if (count($item_images) > 1): ?>
                    <div class="gallery-dots" style="position: absolute; bottom: 15px; display: flex; gap: 8px; z-index: 10;">
                        <?php foreach ($item_images as $index => $img): ?>
                            <span class="gallery-dot" onclick="goToImage(<?php echo $index; ?>)" style="width: 10px; height: 10px; background: <?php echo $index === 0 ? 'white' : 'rgba(255,255,255,0.5)'; ?>; border-radius: 50%; cursor: pointer; transition: all 0.3s;"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="item-details-content">
                <h1 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h1>
                <div class="item-price">$<?php echo htmlspecialchars($item['price']); ?></div>
                
                <div class="item-specs">
                    <div class="spec-row">
                        <span class="spec-label">Size</span>
                        <span class="spec-value"><?php echo htmlspecialchars($item['size']); ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Brand</span>
                        <span class="spec-value" style="font-weight: bold;"><?php echo htmlspecialchars($item['brand']); ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Condition</span>
                        <span class="spec-value"><?php echo htmlspecialchars($item['condition_status']); ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Category</span>
                        <span class="spec-value"><?php echo htmlspecialchars($item['category_name']); ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Seller</span>
                        <span class="spec-value" style="font-weight: bold;">
                            <a href="profile.php?id=<?php echo $item['seller_id']; ?>" style="color: var(--dark-charcoal); text-decoration: none;"> 
                            @<?php echo htmlspecialchars($item['username']); ?>
    </a>
</span>
                    </div>
                </div>
                
                <div class="item-description">
                    <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                </div>
                
                <div class="item-actions">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $item['seller_id']): ?>
                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="action-link">
                            <button class="btn-primary" style="width: 100%; padding: 15px; margin-bottom: 10px;"><i class="fa-solid fa-pen"></i> EDIT ITEM</button>
                        </a>
                        <a href="delete.php?id=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure you want to delete this listing?');" class="action-link">
                            <button class="btn-sell" style="width: 100%; padding: 15px; background: transparent; color: var(--red-crimson); border: 2px solid var(--red-crimson);"><i class="fa-solid fa-trash"></i> DELETE</button>
                        </a>
                    <?php else: ?>
                        <?php if ($item['item_status'] === 'sold'): ?>
                            <button class="btn-primary" disabled style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 2px; opacity: 0.5; cursor: not-allowed;">SOLD OUT</button>
                        <?php elseif(isset($_SESSION['user_id'])): ?>
                            <a href="buy.php?id=<?php echo $item['id']; ?>" style="width: 100%; text-decoration: none; display: block; margin-bottom: 12px;">
                                <button class="btn-primary" style="width: 100%; padding: 20px; font-size: 1.05rem; letter-spacing: 2px; display: flex; align-items: center; justify-content: center; gap: 10px;"><i class="fa-solid fa-shopping-bag"></i> BUY NOW</button>
                            </a>
                            <a href="messages.php?start_chat=<?php echo $item['id']; ?>" style="width: 100%; text-decoration: none; display: block;">
                                <button class="btn-sell" style="width: 100%; padding: 15px; font-size: 0.95rem; letter-spacing: 1.5px; background: var(--white); color: var(--dark-charcoal); border: 1px solid var(--dark-charcoal); display: flex; align-items: center; justify-content: center; gap: 8px;"><i class="fa-solid fa-envelope"></i> MESSAGE SELLER</button>
                            </a>
                        <?php else: ?>
                            <a href="login.php" style="width: 100%; text-decoration: none;">
                                <button class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 2px;">LOG IN TO BUY</button>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div id="fullscreenModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 1000; overflow: hidden;">
        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative;">
            <button onclick="closeFullscreen()" style="position: absolute; top: 20px; right: 30px; background: none; border: none; color: white; font-size: 40px; cursor: pointer; z-index: 1005; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">×</button>
            
            <div id="fullscreenImageContainer" style="width: 90%; height: 90%; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; background: rgba(0,0,0,0.5);">
                <img id="fullscreenImage" src="" alt="Fullscreen" style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: grab; user-select: none; transition: transform 0.1s ease-out;" onmousedown="startDrag(event)">
            </div>
            
            <?php if (count($item_images) > 1): ?>
                <button onclick="fullscreenPrevImage()" style="position: absolute; left: 30px; background: rgba(255,255,255,0.8); border: none; font-size: 32px; padding: 15px 20px; cursor: pointer; border-radius: 4px; z-index: 1005; transition: background 0.3s;">❮</button>
                <button onclick="fullscreenNextImage()" style="position: absolute; right: 30px; background: rgba(255,255,255,0.8); border: none; font-size: 32px; padding: 15px 20px; cursor: pointer; border-radius: 4px; z-index: 1005; transition: background 0.3s;">❯</button>
            <?php endif; ?>
            
            <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); background: rgba(60,60,60,0.85); padding: 10px 18px; border-radius: 6px; display: flex; gap: 12px; align-items: center; z-index: 1005;">
                <button onclick="zoomOut()" style="background: rgba(100,100,100,0.8); border: 1px solid rgba(150,150,150,0.5); padding: 6px 12px; cursor: pointer; border-radius: 3px; font-size: 16px; font-weight: bold; transition: background 0.3s; color: white;">−</button>
                <span id="zoomLevel" style="font-weight: bold; min-width: 45px; text-align: center; color: white; font-size: 13px;">100%</span>
                <button onclick="zoomIn()" style="background: rgba(100,100,100,0.8); border: 1px solid rgba(150,150,150,0.5); padding: 6px 12px; cursor: pointer; border-radius: 3px; font-size: 16px; font-weight: bold; transition: background 0.3s; color: white;">+</button>
                <button onclick="resetZoom()" style="background: rgba(100,100,100,0.8); border: 1px solid rgba(150,150,150,0.5); padding: 6px 10px; cursor: pointer; border-radius: 3px; font-size: 12px; transition: background 0.3s; color: white;">Reset</button>
            </div>
            
            <?php if (count($item_images) > 1): ?>
                <div id="imageCounter" style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,255,255,0.9); padding: 10px 20px; border-radius: 4px; font-weight: bold; z-index: 1005;"></div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php (new Footer())->render(); ?>

    <script>
        const fullscreenImages = <?php 
            $prefixed_images = array_map(function($img) {
                return '../' . $img['image_url'];
            }, $item_images);
            echo json_encode($prefixed_images); 
        ?>;
    </script>
    
    <script src="../app.js"></script>
</body>
</html>