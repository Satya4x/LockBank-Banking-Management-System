<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

$users_query = "SELECT account_number, full_name, balance FROM users ORDER BY full_name ASC";
$users_result = mysqli_query($conn, $users_query);

$deposits_query = "SELECT t.*, u.full_name, u.balance FROM transactions t 
                   JOIN users u ON t.account_number = u.account_number
                   WHERE t.transaction_type='deposit' 
                   ORDER BY t.timestamp DESC";
$deposits_result = mysqli_query($conn, $deposits_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposits - LOCKBANK</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo">LOCK<span>BANK</span></div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i>Users</a></li>
            <li><a href="transactions.php"><i class="fa-solid fa-money-check-alt"></i> Transactions</a></li>
            <li><a href="deposits.php"><i class="fa-solid fa-piggy-bank"></i> Deposits</a></li>
            <li><a href="withdrawals.php"><i class="fa-solid fa-hand-holding-dollar"></i> Withdrawals</a></li>
            <li><a href="notices.php"><i class="fa-solid fa-file-lines"></i> Notices</a></li>
            <li><a href="admin_logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2 class="toph2"><i class="fa-solid fa-money-bill-wave"></i> Deposit Cash</h2>

        <form action="cash_deposit.php" method="POST" class="cash-deposit-form">
            <label for="accountInput"><i class="fa-solid fa-user"></i> Search & Select Account Holder:</label>
            <input list="accounts" name="account_number" id="accountInput" placeholder="Search account number or name" required>
            
            <datalist id="accounts">
                <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                    <option value="<?php echo $user['account_number']; ?>">
                        <?php echo $user['full_name']; ?> (A/C: <?php echo $user['account_number']; ?>)
                    </option>
                <?php } ?>
            </datalist>

            <label for="amount"><i class="fa-solid fa-money-bill"></i> Enter Amount (₹):</label>
            <input type="number" id="amount" name="amount" min="1" required placeholder="Enter amount">

            <button type="submit" class="btn-deposit"><i class="fa-solid fa-piggy-bank"></i> Deposit Cash</button>
        </form>

        <h2 class="toph2"><i class="fa-solid fa-clock-rotate-left"></i> Recent Deposits</h2>

        <table class="transactions-table">
            <thead>
                <tr>
                    <th><i class="fa-solid fa-user"></i> Account Number</th>
                    <th><i class="fa-solid fa-id-card"></i> Account Holder</th>
                    <th><i class="fa-solid fa-coins"></i> Amount</th>
                    <th><i class="fa-solid fa-wallet"></i> Updated Balance</th>
                    <th><i class="fa-solid fa-check-circle"></i> Status</th>
                    <th><i class="fa-solid fa-calendar"></i> Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($deposit = mysqli_fetch_assoc($deposits_result)) { ?>
                    <tr>
                        <td><?php echo $deposit['account_number']; ?></td>
                        <td><?php echo $deposit['full_name']; ?></td>
                        <td>₹<?php echo number_format($deposit['amount'], 2); ?></td>
                        <td>₹<?php echo number_format($deposit['balance'], 2); ?></td>
                        <td>
                            <i class="fa-solid fa-circle <?php echo ($deposit['status'] == 'success') ? 'text-success' : (($deposit['status'] == 'pending') ? 'text-warning' : 'text-danger'); ?>"></i>
                            <?php echo ucfirst($deposit['status']); ?>
                        </td>
                        <td><?php echo date("d M Y, H:i A", strtotime($deposit['timestamp'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>
