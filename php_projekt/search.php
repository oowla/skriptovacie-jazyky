<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search | Nocturne</title>
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

        <div class="user-actions">
            <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            <button class="icon-btn"><i class="fa-regular fa-user"></i></button>
            <button class="btn-sell">SELL ITEM</button>
        </div>
    </div>
</header>

    <main>
    </main>

    <footer>
        <div class="footer-inner">
            <div class="logo">NOCTURNE.</div>
        </div>
        <div class="footer-bottom">
            <p>&copy; Diana Štrbová 2026</p>
        </div>
    </footer>

    <script src="app.js"></script>
</body>
</html>