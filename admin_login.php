<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($admin_password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        echo "<script>alert('Login Successful!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid Username or Password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LOCKBANK</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">LOCK<span>BANK</span></div>
        <h2>Admin Login</h2>
        <form action="admin_login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form>
    </div>
</body>
</html>
