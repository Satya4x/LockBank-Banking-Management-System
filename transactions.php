<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

$transactions_query = "
    SELECT 
        t.*, 
        u_sender.full_name AS sender_name, 
        u_recipient.full_name AS recipient_name 
    FROM transactions t
    LEFT JOIN users u_sender ON t.sender_account = u_sender.account_number
    LEFT JOIN users u_recipient ON t.recipient_account = u_recipient.account_number
    ORDER BY t.timestamp DESC
";

$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $transactions_query = "
        SELECT 
            t.*, 
            u_sender.full_name AS sender_name, 
            u_recipient.full_name AS recipient_name 
        FROM transactions t
        LEFT JOIN users u_sender ON t.sender_account = u_sender.account_number
        LEFT JOIN users u_recipient ON t.recipient_account = u_recipient.account_number
        WHERE t.account_number = '$search' 
        ORDER BY t.timestamp DESC
    ";
}

$transactions_result = mysqli_query($conn, $transactions_query);

function getCorrectBalanceAfter($account_number, $transaction_id) {
    global $conn;
    $query = "SELECT balance_after FROM transactions WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || is_null($row['balance_after'])) {
        $query2 = "SELECT balance FROM users WHERE account_number = ?";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("s", $account_number);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $row2 = $result2->fetch_assoc();
        return $row2 ? $row2['balance'] : 0.00;
    }

    return $row['balance_after'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - LOCKBANK</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo">LOCK<span>BANK</span></div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i>Users</a></li>
            <li><a href="transactions.php" class="active"><i class="fa-solid fa-money-check-alt"></i> Transactions</a></li>
            <li><a href="deposits.php"><i class="fa-solid fa-piggy-bank"></i> Deposits</a></li>
            <li><a href="withdrawals.php"><i class="fa-solid fa-hand-holding-dollar"></i> Withdrawals</a></li>
            <li><a href="notices.php"><i class="fa-solid fa-file-lines"></i> Notices</a></li>
            <li><a href="admin_logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2 class="toph2"><i class="fa-solid fa-list"></i> Transactions</h2>

        <form method="GET" action="transactions.php" class="search-form">
            <input type="text" name="search" class="search-bar" placeholder="Search by Account Number..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-search"><i class="fa-solid fa-search"></i> Search</button>
        </form>

        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Account Number</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                    <th>Amount (₹)</th>
                    <th>Balance After Transaction (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($transactions_result)): ?>
                    <?php
                    if ($transaction['transaction_type'] === 'transfer_sent') {
                        $recipient_name = !empty($transaction['recipient_name']) ? htmlspecialchars($transaction['recipient_name']) : "Unknown";
                        $recipient_account = !empty($transaction['recipient_account']) ? htmlspecialchars($transaction['recipient_account']) : "N/A";
                        $transaction_type = "Sent to $recipient_name ($recipient_account)";
                        $amount = "<span style='color: red;'>-₹" . number_format(abs($transaction['amount']), 2) . "</span>";
                    } elseif ($transaction['transaction_type'] === 'transfer_received') {
                        $sender_name = !empty($transaction['sender_name']) ? htmlspecialchars($transaction['sender_name']) : "Unknown";
                        $sender_account = !empty($transaction['sender_account']) ? htmlspecialchars($transaction['sender_account']) : "N/A";
                        $transaction_type = "Received from $sender_name ($sender_account)";
                        $amount = "<span style='color: green;'>+₹" . number_format($transaction['amount'], 2) . "</span>";
                    } elseif ($transaction['transaction_type'] === 'deposit') {
                        $transaction_type = "Deposit";
                        $amount = "<span style='color: green;'>+₹" . number_format($transaction['amount'], 2) . "</span>";
                    } elseif ($transaction['transaction_type'] === 'withdrawal') {
                        $transaction_type = "Withdrawal";
                        $amount = "<span style='color: red;'>-₹" . number_format($transaction['amount'], 2) . "</span>";
                    } else {
                        $transaction_type = ucfirst(str_replace("_", " ", $transaction['transaction_type']));
                        $amount = "<span style='color: black;'>₹" . number_format($transaction['amount'], 2) . "</span>";
                    }

                    $balance_after = getCorrectBalanceAfter($transaction['account_number'], $transaction['id']);
                    $balance_display = "₹" . number_format($balance_after, 2);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                        <td><?php echo $transaction_type; ?></td>
                        <td><?php echo date("d M Y, H:i A", strtotime($transaction['timestamp'])); ?></td>
                        <td><?php echo $amount; ?></td>
                        <td><?php echo $balance_display; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>  
</html>
