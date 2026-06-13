<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT full_name, photo, balance FROM users WHERE account_number = ?");
$query->bind_param("s", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$_SESSION['user_photo'] = $user['photo'] ?? 'default.png';
$_SESSION['user_name'] = $user['full_name'] ?? 'Unknown User';
$balance = $user['balance'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Balance - LOCKBANK</title>
    <link rel="stylesheet" href="user_dashboard_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">LOCK<span>BANK</span></div>
    <div class="user-profile">
        <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" class="profile-photo" alt="Profile Photo">
        <p class="username"><?php echo htmlspecialchars($user['full_name']); ?></p>
    </div>

    <ul>
        <li><a href="user_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="check_balance.php"><i class="fas fa-wallet"></i> Check Balance</a></li>
        <li><a href="transfer_money.php"><i class="fas fa-exchange-alt"></i> Transfer Money</a></li>
        <li><a href="transaction_history.php"><i class="fas fa-receipt"></i> Transaction History</a></li>
        <li><a href="notifications.php"><i class="fas fa-bell"></i> Notification</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <div class="header">
        <div class="left-header">
            <h2><i class="fas fa-id-card"></i> Account No: <?php echo htmlspecialchars($user_id); ?></h2>
        </div>
        <div class="right-header">
            <h2><i class="fas fa-wallet"></i> Balance: ₹<?php echo number_format($balance, 2); ?></h2>
        </div>
    </div>

    <section class="dashboard">
        <h2><i class="fas fa-wallet"></i> Available Balance</h2>
        <div class="balance-box">
            <p>This is your current account balance.</p>
            <h3>₹<?php echo number_format($balance, 2); ?></h3>
        </div>
    </section>
</div>

</body>
</html>
