<?php
include 'db.php';
$admin_query = mysqli_query($conn, "SELECT admin_email, phone FROM admins LIMIT 1");
$admin = mysqli_fetch_assoc($admin_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOCKBANK</title>
    <link rel="stylesheet" href="index_style.css">
</head>
<body>
    <header>
        <div class="logo">LOCK<span>BANK</span></div>
        <a href="login.php" class="login-btn">LOGIN</a>
    </header>

    <section class="hero">
        <div class="logo">
            <h1>Welcome to LOCK<span>BANK</span></h1>
            <p>Secure & Reliable Banking System</p>
            <a href="signup.php" class="join-btn">OPEN ACCOUNT</a>
        </div>
    </section>

    <footer>
        <div class="footer-about">
            <h3>About LOCKBANK</h3>
            <p>LOCKBANK is a secure and reliable banking system that ensures safe transactions and a user-friendly experience.</p>
        </div>

        <div class="footer-help">
            <h3>Help & Support</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['admin_email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($admin['phone']); ?></p>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 LOCKBANK. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
