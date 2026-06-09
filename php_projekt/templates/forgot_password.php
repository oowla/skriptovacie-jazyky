<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'components/Footer.php';


$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = (new Database())->getConnection();
    $userClass = new User($db);
    $email = trim($_POST['email']);

    $reset_link = $userClass->requestPasswordReset($email);

    if ($reset_link) {
        $message = "<div style='color: green; font-weight: bold;'>We found your email! <br><br>Click the link below to reset your password: <br><a href='$reset_link' style='color:blue;'>Reset my password</a></div>";
    } else {
        $message = "<div style='color: red; font-weight: bold;'>We couldn't find any account associated with this email.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Nocturne</title>
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
                <h2>Reset Password</h2>
                <div class="title-line" style="margin: 10px auto;"></div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="error-box" style="background-color: #f8f9fa; border-color: #ccc; text-align: center; word-break: break-all;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Enter your email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email to receive a reset link">
                </div>
                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn-primary btn-large" style="width: 100%;">SEND RESET LINK</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Remembered your password? <a href="login.php">Log in here</a>.</p>
            </div>
        </div>
    </main>
    <?php (new Footer())->render(); ?>
</body>
</html>