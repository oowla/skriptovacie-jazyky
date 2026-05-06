<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Item | Nocturne</title>
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
                <a href="sell.php" style="text-decoration: none;"><button class="btn-sell" style="background-color: var(--white); color: var(--dark-charcoal);">SELL ITEM</button></a>
            </div>
        </div>
    </header>

    <main class="sell-main">
        <div class="sell-container">
            <div class="section-title">
                <h2>List an Item</h2>
                <div class="title-line"></div>
            </div>

            <form action="upload.php" method="POST" enctype="multipart/form-data" class="sell-form">
                
                <div class="form-section">
                    <h3>Photos</h3>
                    <p class="section-desc">Add up to 5 photos. The first photo will be your main image.</p>
                    
                    <div class="photo-upload-area" id="dropZone">
                        <input type="file" id="itemPhotos" name="itemPhotos[]" multiple accept="image/*" class="file-input">
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <i class="fa-solid fa-camera"></i>
                            <span>Click to upload or drag & drop</span>
                        </div>
                        <div class="preview-container" id="previewContainer"></div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>About the Item</h3>
                    
                    <div class="form-group">
                        <label for="itemTitle">Title</label>
                        <input type="text" id="itemTitle" name="title" placeholder="e.g., Vintage Distressed Leather Jacket" required>
                    </div>

                    <div class="form-group">
                        <label for="itemDesc">Description</label>
                        <textarea id="itemDesc" name="description" rows="5" placeholder="Describe the item, flaws, fit, etc..." required></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Details</h3>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="itemCategory">Category</label>
                            <select id="itemCategory" name="category" required>
                                <option value="">Select Category...</option>
                                <option value="womenswear">Womenswear</option>
                                <option value="menswear">Menswear</option>
                                <option value="footwear">Footwear</option>
                                <option value="accessories">Accessories</option>
                                <option value="vintage">Vintage Decor</option>
                            </select>
                        </div>
                        <div class="form-group half">
                            <label for="itemCondition">Condition</label>
                            <select id="itemCondition" name="condition" required>
                                <option value="">Select Condition...</option>
                                <option value="new_tags">New with tags</option>
                                <option value="new_no_tags">New without tags</option>
                                <option value="very_good">Very Good</option>
                                <option value="good">Good</option>
                                <option value="flawed">Flawed / Distressed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="itemBrand">Brand</label>
                            <input type="text" id="itemBrand" name="brand" placeholder="e.g., Dr. Martens, Killstar...">
                        </div>
                        <div class="form-group half">
                            <label for="itemSize">Size</label>
                            <input type="text" id="itemSize" name="size" placeholder="e.g., M, 38, UK 8">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Pricing</h3>
                    <div class="form-group price-group">
                        <label for="itemPrice">Price</label>
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">$</span>
                            <input type="number" id="itemPrice" name="price" step="0.01" min="1" placeholder="0.00" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-large">UPLOAD LISTING</button>
                </div>

            </form>
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