<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'components/Footer.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = (new Database())->getConnection();
    $userClass = new User($db);

    $result = $userClass->register(trim($_POST['username']), trim($_POST['email']), $_POST['password']);

    if ($result === "success") {
        $success_message = "Account has been successfully created! You can log in.";
    } else {
        $error_message = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | Nocturne</title>
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
                <h2>Sign Up</h2>
                <div class="title-line" style="margin: 10px auto;"></div>
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

            <form action="register.php" method="POST" class="auth-form">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Choose a username">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password">
                </div>

                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn-primary btn-large" style="width: 100%;">JOIN NOCTURNE</button>
                </div>

            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Log in here</a>.</p>
            </div>
        </div>
    </main>

    <?php (new Footer())->render(); ?>

</body>
</html>