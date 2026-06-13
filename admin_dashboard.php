<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;
$total_transactions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total FROM transactions"))['total'] ?? 0;
$total_withdrawals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total FROM transactions WHERE transaction_type='withdrawal'"))['total'] ?? 0;
$total_deposits = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total FROM transactions WHERE transaction_type='deposit'"))['total'] ?? 0;

$recent_transactions = mysqli_query($conn, "SELECT * FROM transactions ORDER BY timestamp DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LOCKBANK</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="admin_dashboard_00.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">LOCK<span>BANK</span></div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Users</a></li>
            <li><a href="transactions.php"><i class="fa-solid fa-money-check-alt"></i> Transactions</a></li>
            <li><a href="deposits.php"><i class="fa-solid fa-piggy-bank"></i> Deposits</a></li>
            <li><a href="withdrawals.php"><i class="fa-solid fa-hand-holding-usd"></i> Withdrawals</a></li>
            <li><a href="notices.php"><i class="fa-solid fa-file-lines"></i> Notices</a></li>
            <li><a href="admin_logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2 class="toph2"><i class="fa-solid fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h2>

        <div class="dashboard-stats">
            <div class="stat-box">
                <h3><i class="fa-solid fa-users"></i> <?php echo $total_users; ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-box">
                <h3><i class="fa-solid fa-coins"></i> ₹<?php echo number_format($total_transactions, 2); ?></h3>
                <p>Total Transactions</p>
            </div>
            <div class="stat-box">
                <h3><i class="fa-solid fa-piggy-bank"></i> ₹<?php echo number_format($total_deposits, 2); ?></h3>
                <p>Total Deposits</p>
            </div>
            <div class="stat-box">
                <h3><i class="fa-solid fa-hand-holding-usd"></i> ₹<?php echo number_format($total_withdrawals, 2); ?></h3>
                <p>Total Withdrawals</p>
            </div>
        </div>

        <div class="dashboard-box">
            <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Latest Transactions</h2>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Account No.</th>
                        <th>Type</th>
                        <th>Amount (₹)</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_transactions) > 0): ?>
                        <?php while ($transaction = mysqli_fetch_assoc($recent_transactions)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['account_number']); ?></td>
                                <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                                <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                                <td class="<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </td>
                                <td><?php echo date("d M Y, H:i", strtotime($transaction['timestamp'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No recent transactions.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-box">
            <h2 class="section-title"><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
            <div class="quick-actions">
                <a href="deposits.php" class="btn-action"><i class="fa-solid fa-plus"></i> Add Deposit</a>
                <a href="withdrawals.php" class="btn-action"><i class="fa-solid fa-minus"></i> Process Withdrawal</a>
                <a href="manage_users.php" class="btn-action"><i class="fa-solid fa-users"></i> Manage Users</a>
            </div>
        </div>
    </div>
</body>
</html>
