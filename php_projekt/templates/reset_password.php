<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'components/Footer.php';

$message = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = (new Database())->getConnection();
    $userClass = new User($db);
    
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $post_token = $_POST['token'];

    if ($new_password === $confirm_password) {
        
        $result = $userClass->resetPassword($post_token, $new_password);
        
        if ($result === "success") {
            $message = "<div style='color: #155724; font-weight: bold;'>Password has been successfully changed! <br><br><a href='login.php' style='color: #155724; text-decoration: underline;'>Click here to log in</a>.</div>";
        } else {
            $message = "<div style='color: red; font-weight: bold;'>" . htmlspecialchars($result) . "</div>";
        }
    } else {
        $message = "<div style='color: red; font-weight: bold;'>Passwords do not match! Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    <header style="display: flex; justify-content: center; padding: 20px 0;">
        <a href="index.php" class="logo" style="text-decoration: none;">NOCTURNE.</a>
    </header>

    <main class="auth-main">
        <div class="auth-container">
            <div class="section-title" style="text-align: center; margin-bottom: 30px;">
                <h2>New Password</h2>
                <div class="title-line" style="margin: 10px auto;"></div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="error-box" style="background-color: #f8f9fa; border-color: #ccc; text-align: center; margin-bottom: 20px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST" class="auth-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required placeholder="Enter new password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password">
                </div>
                
                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn-primary btn-large" style="width: 100%;">CHANGE PASSWORD</button>
                </div>
            </form>
            
        </div>
    </main>
    <?php (new Footer())->render(); ?>
</body>
</html>