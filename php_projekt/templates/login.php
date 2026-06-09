<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'components/Footer.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = (new Database())->getConnection();
    $userClass = new User($db);

    if ($userClass->login(trim($_POST['email']), $_POST['password'])) {
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Wrong email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Nocturne</title>
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
                <h2>Log In</h2>
                <div class="title-line" style="margin: 10px auto;"></div>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-box">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="auth-form">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn-primary btn-large" style="width: 100%;">ENTER THE ARCHIVE</button>
                </div>

            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
                <a href="forgot_password.php">Forgot your password?</a>
            </div>
        </div>
    </main>

    <?php (new Footer())->render(); ?>

</body>
</html>