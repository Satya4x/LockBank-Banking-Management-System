<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT * FROM users WHERE account_number = ?");
$query->bind_param("s", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - LOCKBANK</title>
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
            <h2><i class="fas fa-id-card"></i> Account No: <?php echo htmlspecialchars($user['account_number']); ?></h2>
        </div>
        <div class="right-header">
            <h2><i class="fas fa-wallet"></i> Balance: ₹<?php echo number_format($user['balance'], 2); ?></h2>
        </div>
    </div>

    <section class="dashboard">
        <h2><i class="fas fa-clipboard-list"></i> Account Overview</h2>
        
<div class="account-details">
    <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : "N/A"; ?></p>
    <p><strong><i class="fas fa-phone"></i> Mobile:</strong> <?php echo !empty($user['mobile']) ? htmlspecialchars($user['mobile']) : "N/A"; ?></p>
    <p><strong><i class="fas fa-map-marker-alt"></i> Address:</strong> <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : "N/A"; ?></p>
    <p><strong><i class="fas fa-id-card-alt"></i> Aadhar Number:</strong> <?php echo !empty($user['aadhar']) ? htmlspecialchars($user['aadhar']) : "N/A"; ?></p>
    <p><strong><i class="fas fa-id-badge"></i> PAN Number:</strong> <?php echo !empty($user['pan_number']) ? htmlspecialchars($user['pan_number']) : "N/A"; ?></p>
    <p><strong><i class="fas fa-university"></i> Account Type:</strong> <?php echo !empty($user['account_type']) ? htmlspecialchars($user['account_type']) : "N/A"; ?></p>
    <p><strong><i class="fas fa-user-tie"></i> Occupation:</strong> <?php echo !empty($user['occupation']) ? htmlspecialchars($user['occupation']) : "N/A"; ?></p>
</div>

        <div class="recent-transactions">
            <h2><i class="fas fa-history"></i> Recent Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $transactions = mysqli_query($conn, "SELECT * FROM transactions WHERE account_number='$user_id' ORDER BY timestamp DESC LIMIT 5");
                    if (mysqli_num_rows($transactions) > 0) {
                        while ($row = mysqli_fetch_assoc($transactions)) {
                            echo "<tr>
                                <td>" . date("d M Y", strtotime($row['timestamp'])) . "</td>
                                <td>" . ucfirst($row['transaction_type']) . "</td>
                                <td>₹" . number_format($row['amount'], 2) . "</td>
                                <td>" . ucfirst($row['status']) . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No recent transactions.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

</body>
</html>
