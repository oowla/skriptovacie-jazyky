<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Item.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$db = (new Database())->getConnection();
$itemClass = new Item($db);

$item_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$error_message = '';
$success_message = '';

$item = $itemClass->getItemByIdAndSeller($item_id, $user_id);

if (!$item) {
    die("You do not have permission to edit this item.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $size = trim($_POST['size']);

    if ($itemClass->updateItem($item_id, $user_id, $title, $description, $price, $size)) {
        $success_message = "Item successfully updated!";
        
        $item['title'] = $title;
        $item['description'] = $description;
        $item['price'] = $price;
        $item['size'] = $size;
    } else {
        $error_message = "Changes not made. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: var(--bg-ash);">

    <header style="padding: 20px 5%; background-color: var(--dark-charcoal); display:flex; justify-content:space-between; align-items:center;">
         <a href="index.php" class="logo" style="text-decoration: none;">NOCTURNE.</a>
         <a href="sell.php" style="color: var(--white); text-decoration: none; font-weight:bold;">&larr; Back to Manage Listings</a>
    </header>

    <main style="max-width: 800px; margin: 60px auto; padding: 0 5%;">
        <div style="background-color: var(--white); border: 2px solid var(--dark-charcoal); box-shadow: 8px 8px 0px var(--dark-charcoal); padding: 40px;">
            <div class="section-title">
                <h2>Edit Listing</h2>
                <div class="title-line"></div>
            </div>

            <?php if (!empty($success_message)): ?>
                <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; font-weight: bold; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form action="edit.php?id=<?php echo $item['id']; ?>" method="POST">
                
                <div class="form-group" style="display: flex; flex-direction: column; margin-bottom: 20px;">
                    <label style="font-weight: bold; margin-bottom: 8px; text-transform: uppercase; font-size:14px;">Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required style="padding: 15px; border: 2px solid var(--dark-charcoal); font-family: 'Roboto'; font-size: 15px;">
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; margin-bottom: 20px;">
                    <label style="font-weight: bold; margin-bottom: 8px; text-transform: uppercase; font-size:14px;">Description</label>
                    <textarea name="description" rows="5" required style="padding: 15px; border: 2px solid var(--dark-charcoal); font-family: 'Roboto'; font-size: 15px;"><?php echo htmlspecialchars($item['description']); ?></textarea>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <div style="flex: 1; display: flex; flex-direction: column;">
                        <label style="font-weight: bold; margin-bottom: 8px; text-transform: uppercase; font-size:14px;">Size</label>
                        <input type="text" name="size" value="<?php echo htmlspecialchars($item['size']); ?>" style="padding: 15px; border: 2px solid var(--dark-charcoal); font-family: 'Roboto'; font-size: 15px;">
                    </div>
                    
                    <div style="flex: 1; display: flex; flex-direction: column;">
                        <label style="font-weight: bold; margin-bottom: 8px; text-transform: uppercase; font-size:14px;">Price ($)</label>
                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required style="padding: 15px; border: 2px solid var(--dark-charcoal); font-family: 'Roboto'; font-size: 15px;">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 18px; font-size: 16px;">SAVE CHANGES</button>
            </form>

        </div>
    </main>

    <?php (new Footer())->render(); ?>

</body>
</html>