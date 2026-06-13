<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['account_number'];
        $_SESSION['user_name'] = $user['full_name'];
        echo "<script>alert('Login Successful!'); window.location.href='user_dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid Email or Password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LOCKBANK</title>
    <link rel="stylesheet" href="login_style.css">
</head>
<body>
    <form action="login.php" method="POST">
        <div class="logo">LOCK<span>BANK</span></div>
        <h2>USER LOGIN</h2>

        <label>EMAIL ADDRESS</label>
        <input type="email" name="email" required oninput="this.value = this.value.toLowerCase()">

        <label>PASSWORD</label>
        <input type="password" name="password" required minlength="6">

        <button type="submit">LOGIN</button>
        <p>Create New Account - <a href="signup.php">OPEN ACCOUNT</a></p>
    </form>
</body>
</html>
