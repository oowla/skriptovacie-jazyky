<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

$db = (new Database())->getConnection();
$itemClass = new Item($db);

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$category_name = '';

if (!empty($category_filter)) {
    $stmt = $db->prepare("SELECT name FROM categories WHERE id = :id");
    $stmt->execute([':id' => intval($category_filter)]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    $category_name = $category ? $category['name'] : '';
}

$items = $itemClass->searchItems($search_query, $category_filter);
$error = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php (new Header())->render(); ?>

    <main class="search-main">
        
        <div class="big-search-container">
            <form action="search.php" method="GET" class="big-search-form">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Search for items, brands, or aesthetics..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-primary">SEARCH</button>
            </form>
        </div>

        <div class="section-title">
             <h2>
                <?php 
                    if ($search_query !== '') {
                        echo "Results for: '" . htmlspecialchars($search_query) . "'";
                    } elseif ($category_name !== '') {
                        echo "Category: " . htmlspecialchars($category_name);
                    } else {
                        echo "All Archive Items";
                    }
                ?>
            </h2>
            <div class="title-line"></div>
        </div>
        
        <div class="product-grid">
            <?php if(!empty($error)): ?>
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
                <div class="error-box" style="width: 100%; grid-column: 1 / -1; background-color: var(--white); color: var(--dark-charcoal);">
                    No items found matching your dark aesthetic. Try a different search.
                </div>
            <?php endif; ?>
        </div>

    </main>

    <?php (new Footer())->render(); ?>

    <script src="../app.js"></script>
</body>
</html>