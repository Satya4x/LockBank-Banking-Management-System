<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

$users_query = "SELECT account_number, full_name, email, mobile, account_type, balance FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - LOCKBANK</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="logo">LOCK<span>BANK</span></div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i>Users</a></li>
            <li><a href="transactions.php"><i class="fa-solid fa-money-check-alt"></i> Transactions</a></li>
            <li><a href="deposits.php"><i class="fa-solid fa-piggy-bank"></i> Deposits</a></li>
            <li><a href="withdrawals.php"><i class="fa-solid fa-hand-holding-usd"></i> Withdrawals</a></li>
            <li><a href="notices.php"><i class="fa-solid fa-file-lines"></i> Notices</a></li>
            <li><a href="admin_logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2 class="toph2"><i class="fa-solid fa-user-gear"></i>Users</h2>

        <div class="search-container">
            <input type="text" id="userSearch" onkeyup="searchTable()" placeholder="Search by Account Number, Name, or Email">
        </div>

        <table class="user-table" id="userTable">
            <thead>
                <tr>
                    <th>Account Number</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Account Type</th>
                    <th>Updated Balance (₹)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($user = mysqli_fetch_assoc($users_result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['account_number']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($user['account_type']); ?></td>
                        <td>₹<?php echo number_format($user['balance'], 2); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_user.php?account=<?php echo urlencode($user['account_number']); ?>" class="btn-view">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <a href="edit_user.php?account=<?php echo urlencode($user['account_number']); ?>" class="btn-edit">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </a>
                                <a href="delete_user.php?account=<?php echo urlencode($user['account_number']); ?>" class="btn-delete" onclick="return confirm('Are you sure?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchTable() {
            let input = document.getElementById("userSearch").value.toLowerCase();
            let table = document.getElementById("userTable");
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName("td");
                let match = false;
                
                for (let j = 0; j < cells.length - 1; j++) {
                    if (cells[j].textContent.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }
                
                rows[i].style.display = match ? "" : "none";
            }
        }
    </script>

</body>
</html>
