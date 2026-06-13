<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

$account_number = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE account_number = ?");
$stmt->bind_param("s", $account_number);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

$notice_query = $conn->prepare("SELECT * FROM notices WHERE user_id IS NULL OR user_id = (SELECT id FROM users WHERE account_number = ?) ORDER BY created_at DESC");
$notice_query->bind_param("s", $account_number);
$notice_query->execute();
$notices = $notice_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification - LOCKBANK</title>
    <link rel="stylesheet" href="notifications.css">
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
            <h2><i class="fas fa-id-card"></i> Account No: <?php echo htmlspecialchars($user['account_number']); ?></h2>
        </div>
        <div class="right-header">
            <h2><i class="fas fa-wallet"></i> Balance: ₹<?php echo number_format($user['balance'], 2); ?></h2>
        </div>
    </div>

    <section class="dashboard">
    <h2><i class="fas fa-bell"></i>  Notification</h2>

    <?php if (mysqli_num_rows($notices) > 0): ?>
        <div class="notice-table-wrapper">
            <table class="notice-table">
                <thead>
                    <tr style="background: yellow; color: black;">
                        <th>Title</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($notice = mysqli_fetch_assoc($notices)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($notice['title']); ?></td>
                            <td><?php echo date("d M Y, h:i A", strtotime($notice['created_at'])); ?></td>
                            <td>
                            <a href="view_notifications.php?id=<?php echo $notice['id']; ?>" class="btn-read"><i class="fas fa-eye"></i> Read</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="color: yellow;">No  notification available at the moment.</p>
    <?php endif; ?>
</section>

</div>

</body>
</html>
