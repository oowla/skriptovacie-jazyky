<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Nocturne</title>
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
                
                <a href="messages.php" style="color: var(--red-crimson);">Messages</a>
                <a href="liked.php">Liked</a>
            </nav>

            <div class="user-actions">
                <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                <button class="icon-btn"><i class="fa-regular fa-user"></i></button>
                <button class="btn-sell">SELL ITEM</button>
            </div>
        </div>
    </header>

    <main class="messages-main">
        <div class="section-title">
            <h2>Inbox</h2>
            <div class="title-line"></div>
        </div>

        <div class="messages-container">
            
            <aside class="conversations-list">
                <div class="conversations-header">
                    <h3>Your Chats</h3>
                </div>
                
                <div class="conversation-item active">
                    <img src="https://images.unsplash.com/photo-1509305717900-84f40e786d82?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Seller" class="avatar">
                    <div class="conv-info">
                        <div class="conv-top">
                            <span class="conv-name">RavenBoutique</span>
                            <span class="conv-time">10:42 AM</span>
                        </div>
                        <span class="conv-item-name">Vintage Leather Jacket</span>
                        <p class="conv-preview">Yes, it's still available!</p>
                    </div>
                </div>

                <div class="conversation-item">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="User" class="avatar">
                    <div class="conv-info">
                        <div class="conv-top">
                            <span class="conv-name">Luna_99</span>
                            <span class="conv-time">Yesterday</span>
                        </div>
                        <span class="conv-item-name">Silver Cross Necklace</span>
                        <p class="conv-preview">Could you do $30?</p>
                    </div>
                </div>
            </aside>

            <section class="chat-area">
                <div class="chat-header">
                    <div class="chat-header-info">
                        <h3>RavenBoutique</h3>
                        <span>Regarding: Vintage Leather Jacket ($85.00)</span>
                    </div>
                    <button class="btn-sell">BUY NOW</button>
                </div>

                <div class="chat-history" id="chatHistory">
                    
                    <div class="message-wrapper sent">
                        <div class="message bubble">
                            Hi! Is the jacket still available? What are the exact measurements?
                        </div>
                        <span class="msg-time">10:30 AM</span>
                    </div>

                    <div class="message-wrapper received">
                        <div class="message bubble">
                            Hello! Yes, it's still available! The pit-to-pit is 22 inches, and length is 26 inches. Let me know if you need more info. 🦇
                        </div>
                        <span class="msg-time">10:42 AM</span>
                    </div>

                </div>

                <div class="chat-input-area">
                    <input type="text" id="messageInput" placeholder="Write a message...">
                    <button class="btn-primary" id="sendMessageBtn">SEND</button>
                </div>
            </section>

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