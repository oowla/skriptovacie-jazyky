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
            <div class="logo">NOCTURNE.</div>
            
            <nav class="main-nav">
                <a href="#">Categories</a>
                <a href="#">Messages</a>
                <a href="#">Liked</a>
            </nav>

            <div class="user-actions">
                <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                <button class="icon-btn"><i class="fa-regular fa-user"></i></button>
                <button class="btn-sell">SELL ITEM</button>
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