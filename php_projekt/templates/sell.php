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

$error_message = '';
$success_message = '';
$seller_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $size = trim($_POST['size']);
    $brand = trim($_POST['brand']);
    $condition_status = $_POST['condition_status'];
    $category_id = $_POST['category_id']; 

    try {
        $item_id = $itemClass->createItem($seller_id, $category_id, $title, $description, $price, $size, $brand, $condition_status);

        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $is_primary = 1;
        if (isset($_FILES['itemPhotos']) && !empty($_FILES['itemPhotos']['name'][0])) {
            $file_count = count($_FILES['itemPhotos']['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                $tmp_name = $_FILES['itemPhotos']['tmp_name'][$i];
                $original_name = $_FILES['itemPhotos']['name'][$i];
                
                if ($tmp_name != "") {
                    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                    $new_filename = uniqid('img_') . '_' . time() . '.' . $ext;
                    $upload_path = $upload_dir . $new_filename;
                    $db_path = 'uploads/' . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $itemClass->addImage($item_id, $db_path, $is_primary);
                        $is_primary = 0;
                    }
                }
            }
        }
        $success_message = "Your item has been listed successfully!";

    } catch(PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

$my_items = $itemClass->getItemsBySeller($seller_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Item | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php (new Header())->render(); ?>

    <main class="sell-main">
        <div class="sell-container">
            <div class="section-title">
                <h2>List an Item</h2>
                <div class="title-line"></div>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-box">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="error-box" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form action="sell.php" method="POST" enctype="multipart/form-data" class="sell-form">
                
                <div class="form-section">
                    <h3>Photos</h3>
                    <p class="section-desc">Add up to 5 photos. The first photo will be your main image.</p>
                    
                    <div class="photo-upload-area" id="dropZone">
                        <input type="file" id="itemPhotos" name="itemPhotos[]" multiple accept="image/*" class="file-input" required>
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
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g., Vintage Distressed Leather Jacket" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5" placeholder="Describe the item, flaws, fit, etc..." required></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Details</h3>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category...</option>
                                <option value="1">Womenswear</option>
                                <option value="2">Menswear</option>
                                <option value="3">Footwear</option>
                                <option value="4">Accessories</option>
                                <option value="5">Vintage Decor</option>
                            </select>
                        </div>
                        <div class="form-group half">
                            <label for="condition_status">Condition</label>
                            <select id="condition_status" name="condition_status" required>
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
                            <label for="brand">Brand</label>
                            <input type="text" id="brand" name="brand" placeholder="e.g., Dr. Martens, Killstar..." required>
                        </div>
                        <div class="form-group half">
                            <label for="size">Size</label>
                            <input type="text" id="size" name="size" placeholder="e.g., M, 38, UK 8" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half price-group">
                            <label for="price">Price</label>
                            <div class="price-input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" id="price" name="price" step="0.01" min="1" placeholder="0.00" required style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-large">UPLOAD LISTING</button>
                </div>

            </form>
        </div>
    </main>

    <?php (new Footer())->render(); ?>

    <script src="../app.js"></script>
</body>
</html>