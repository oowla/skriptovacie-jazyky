<?php

class Header {
    private $is_authenticated = false;
    private $username = '';

    public function __construct() {
        $this->is_authenticated = isset($_SESSION['user_id']);
        if ($this->is_authenticated) {
            $this->username = htmlspecialchars($_SESSION['username']);
        }
    }

    public function render() {
        ?>
        <header>
            <div class="header-inner">
                <a href="index.php" class="logo" style="text-decoration: none;">NOCTURNE.</a>
                
                <nav class="main-nav">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" id="categoriesToggle">
                             Categories <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>
                        </a>
                        <div class="dropdown-menu" id="categoriesMenu">
                            <a href="search.php?category=1">Womenswear</a>
                            <a href="search.php?category=2">Menswear</a>
                            <a href="search.php?category=3">Footwear</a>
                            <a href="search.php?category=4">Accessories</a>
                            <a href="search.php?category=5">Vintage Decor</a>
                        </div>
                    </div>
                    
                    <a href="messages.php">Messages</a>
                    <a href="liked.php">Liked</a>
                </nav>

                <div class="user-actions" style="display: flex; align-items: center; gap: 20px;">
                    
                    <form action="search.php" method="GET" class="hidden-search-form">
                        <button type="button" class="icon-btn search-trigger" style="padding: 0;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <input type="text" name="q" class="hidden-search-input" placeholder="Search...">
                    </form>
                    
                    <?php if($this->is_authenticated): ?>
                        <div style="color: var(--white); font-size: 14px; font-weight: bold; letter-spacing: 1px;">
                            Welcome,
                                    <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" style="color: var(--red-crimson); text-decoration: none; border-bottom: 1px solid var(--red-crimson);">
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                    </a>
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
        <?php
    }
}
?>