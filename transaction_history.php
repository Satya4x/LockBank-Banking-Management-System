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

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) FROM transactions WHERE account_number = ?");
$count_stmt->bind_param("s", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

$stmt = $conn->prepare("
    SELECT t.account_number, t.transaction_type, t.timestamp, t.amount, t.balance_after,
           t.sender_account, t.recipient_account,
           u_sender.full_name AS sender_name, 
           u_recipient.full_name AS recipient_name 
    FROM transactions t
    LEFT JOIN users u_sender ON t.sender_account = u_sender.account_number
    LEFT JOIN users u_recipient ON t.recipient_account = u_recipient.account_number
    WHERE t.account_number = ? 
    ORDER BY t.timestamp DESC 
    LIMIT ?, ?
");
$stmt->bind_param("sii", $user_id, $start, $limit);
$stmt->execute();
$transactions = $stmt->get_result();

$balance_stmt = $conn->prepare("SELECT balance FROM users WHERE account_number = ?");
$balance_stmt->bind_param("s", $user_id);
$balance_stmt->execute();
$balance_result = $balance_stmt->get_result();
$user_balance = $balance_result->fetch_assoc()['balance'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - LOCKBANK</title>
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
                <h2><i class="fas fa-wallet"></i> Balance: ₹<?php echo number_format($user_balance, 2); ?></h2>
            </div>
        </div>

        <section class="dashboard">
            <h2><i class="fas fa-receipt"></i> Transaction History</h2>
            <div class="recent-transactions">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction Type</th>
                            <th>Date & Time</th>
                            <th>Amount (₹)</th>
                            <th>Balance After Transaction</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($transactions->num_rows > 0): 
                        ?>
                            <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <?php 
                                if ($transaction['transaction_type'] === 'transfer_sent') {
                                    $recipient_name = $transaction['recipient_name'] ?? "Unknown";
                                    $recipient_account = $transaction['recipient_account'] ?? "N/A";
                                    $transaction_type = "Sent to $recipient_name ($recipient_account)";
                                } elseif ($transaction['transaction_type'] === 'transfer_received') {
                                    $sender_name = $transaction['sender_name'] ?? "Unknown";
                                    $sender_account = $transaction['sender_account'] ?? "N/A";
                                    $transaction_type = "Received from $sender_name ($sender_account)";
                                } else {
                                    $transaction_type = ucfirst(str_replace("_", " ", $transaction['transaction_type']));
                                }

                                if ($transaction['transaction_type'] === 'withdrawal' || $transaction['transaction_type'] === 'transfer_sent') {
                                    $amount = "<span style='color: red;'>-₹" . number_format(abs($transaction['amount']), 2) . "</span>";
                                } else {
                                    $amount = "<span style='color: green;'>+₹" . number_format($transaction['amount'], 2) . "</span>";
                                }
                                ?>
                                <tr>
                                    <td><?php echo $transaction_type; ?></td>
                                    <td><?php echo date("d M Y, H:i", strtotime($transaction['timestamp'])); ?></td>
                                    <td><?php echo $amount; ?></td>
                                    <td>₹<?php echo number_format($transaction['balance_after'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1); ?>" class="prev"><i class="fas fa-chevron-left"></i> Prev</a>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo ($page + 1); ?>" class="next">Next <i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

</body>
</html>
