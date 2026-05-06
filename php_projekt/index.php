<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nocturne | Alternative Secondhand</title>
    <link rel="stylesheet" href="styles.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="header-inner">
            <a href="index.php" class="logo" style="text-decoration: none;">NOCTURNE.</a>
            
            <nav class="main-nav">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" id="categoriesToggle">
                         Categories <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>
                    </a>
                    <div class="dropdown-menu" id="categoriesMenu">
                        <a href="search.php">Womenswear</a>
                        <a href="search.php">Menswear</a>
                        <a href="search.php">Footwear</a>
                        <a href="search.php">Accessories</a>
                        <a href="search.php">Vintage Decor</a>
                    </div>
                </div>
                
                <a href="messages.php">Messages</a>
                <a href="liked.php">Liked</a>
            </nav>

            <div class="user-actions" style="display: flex; align-items: center; gap: 20px;">
                <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div style="color: var(--white); font-size: 14px; font-weight: bold; letter-spacing: 1px;">
                        Welcome, <span style="color: var(--red-crimson);"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    
                    <a href="logout.php" style="color: var(--grey); text-decoration: none; font-size: 16px;" title="Log Out">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="icon-btn" style="text-decoration: none;" title="Log In">
                        <i class="fa-regular fa-user"></i>
                    </a>
                <?php endif; ?>

                <a href="sell.php" style="text-decoration: none;">
                    <button class="btn-sell" style="background-color: var(--white); color: var(--dark-charcoal);">SELL ITEM</button>
                </a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-text">
            <h1>Curate Your<br>Dark Aesthetic.</h1>
            <p>Buy and sell alternative, gothic, and vintage fashion. Give dark garments a second life.</p>
            <button class="btn-primary">SHOP THE ARCHIVE</button>
        </div>
        <div class="hero-image">
            <img src="images/skull.jpg" alt="Dark Fashion">
        </div>
    </section>

    <main>
        <div class="section-title">
            <h2>New Items</h2>
            <div class="title-line"></div>
        </div>
        
        <div class="product-grid" id="productGrid">
        </div>
    </main>

    <footer>
        <div class="footer-inner">
            <div class="logo">NOCTURNE.</div>
            <div class="footer-links">
                <a href="#">About Us</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">Privacy Policy</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; Diana Štrbová 2026</p>
        </div>
    </footer>

    <script src="app.js"></script>
</body>
</html>