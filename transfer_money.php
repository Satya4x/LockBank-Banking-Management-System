<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT full_name, photo, balance, lpin FROM users WHERE account_number = ?");
$query->bind_param("s", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$_SESSION['user_photo'] = $user['photo'] ?? 'default.png';
$_SESSION['user_name'] = $user['full_name'] ?? 'Unknown User';
$balance = $user['balance'] ?? 0;
$stored_lpin = trim((string) $user['lpin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient = trim($_POST['recipient']);
    $amount = $_POST['amount'];
    $entered_lpin = trim($_POST['lpin']);

    if (!is_numeric($amount) || $amount <= 0) {
        echo "<script>alert('Invalid amount! Please enter a valid amount.');</script>";
    } elseif ($amount > $balance) {
        echo "<script>alert('Insufficient Balance!');</script>";
    } elseif ($entered_lpin !== $stored_lpin) { 
        echo "<script>alert('Incorrect LPIN! Transaction failed.');</script>";
    } else {
        $stmt = $conn->prepare("SELECT account_number, full_name, balance FROM users WHERE account_number = ? OR mobile = ?");
        $stmt->bind_param("ss", $recipient, $recipient);
        $stmt->execute();
        $receiver_result = $stmt->get_result();
        $receiver = $receiver_result->fetch_assoc();

        if (!$receiver) {
            echo "<script>alert('Recipient not found! Please enter a valid account number or mobile number.');</script>";
        } elseif ($receiver['account_number'] == $user_id) {
            echo "<script>alert('You cannot transfer money to yourself!');</script>";
        } else {
            $receiver_account = $receiver['account_number'];
            $receiver_name = $receiver['full_name'];

            $new_sender_balance = $balance - $amount;
            $new_receiver_balance = $receiver['balance'] + $amount;

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE account_number = ?");
                $stmt->bind_param("ds", $new_sender_balance, $user_id);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE account_number = ?");
                $stmt->bind_param("ds", $new_receiver_balance, $receiver_account);
                $stmt->execute();

                $stmt = $conn->prepare("INSERT INTO transactions 
                    (account_number, transaction_type, amount, recipient_account, recipient_name, balance_after, status) 
                    VALUES (?, 'transfer_sent', ?, ?, ?, ?, 'completed')");
                $stmt->bind_param("sdsds", $user_id, $amount, $receiver_account, $receiver_name, $new_sender_balance);
                $stmt->execute();

                $stmt = $conn->prepare("INSERT INTO transactions 
                    (account_number, transaction_type, amount, sender_account, sender_name, balance_after, status) 
                    VALUES (?, 'transfer_received', ?, ?, ?, ?, 'completed')");
                $stmt->bind_param("sdsds", $receiver_account, $amount, $user_id, $_SESSION['user_name'], $new_receiver_balance);
                $stmt->execute();

                $masked_sender = str_repeat("X", strlen($user_id) - 5) . substr($user_id, -5);
                $masked_receiver = str_repeat("X", strlen($receiver_account) - 5) . substr($receiver_account, -5);

                $sender_msg = "You have successfully sent ₹" . number_format($amount, 2) .
                              " to $receiver_name (A/C: $masked_receiver).\n" .
                              "Remaining Balance: ₹" . number_format($new_sender_balance, 2);

                $receiver_msg = "You have received ₹" . number_format($amount, 2) .
                                " from " . $_SESSION['user_name'] . " (A/C: $masked_sender).\n" .
                                "Updated Balance: ₹" . number_format($new_receiver_balance, 2);

                $stmt = $conn->prepare("INSERT INTO notices (user_id, title, content, created_at) VALUES ((SELECT id FROM users WHERE account_number = ?), ?, ?, NOW())");
                $title = "Payment Sent";
                $stmt->bind_param("sss", $user_id, $title, $sender_msg);
                $stmt->execute();

                $title = "Payment Received";
                $stmt->bind_param("sss", $receiver_account, $title, $receiver_msg);
                $stmt->execute();

                $conn->commit();
                echo "<script>alert('Transfer Successful!'); window.location.href='transaction_history.php';</script>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>alert('Transfer Failed! Please try again.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money - LOCKBANK</title>
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
            <h2><i class="fas fa-exchange-alt"></i> Transfer Money</h2>
            <form action="transfer_money.php" method="POST" class="transfer-form">
                <label><i class="fas fa-user"></i> Recipient Account/Mobile Number:</label>
                <input type="text" name="recipient" required pattern="\d+" title="Enter a valid account number or mobile number">

                <label><i class="fas fa-money-bill-wave"></i> Amount:</label>
                <input type="number" name="amount" required min="1" max="<?php echo $balance; ?>" step="0.01">

                <label><i class="fas fa-key"></i> Enter LPIN:</label>
                <input type="password" name="lpin" required pattern="\d{5}" title="Enter your 5-digit LPIN" maxlength="5">

                <button type="submit" class="btn-transfer"><i class="fas fa-paper-plane"></i> Send Money</button>
            </form>
        </section>
    </div>

</body>
</html>
