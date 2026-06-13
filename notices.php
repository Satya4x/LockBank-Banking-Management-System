<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_notice'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $user_id = $_POST['user_id'] === "all" ? "NULL" : intval($_POST['user_id']);

    $insert_query = "INSERT INTO notices (user_id, title, content, created_at) 
                     VALUES ($user_id, '$title', '$content', NOW())";

    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Notice sent successfully!'); window.location.href='notices.php';</script>";
    } else {
        echo "<script>alert('Error sending notice!');</script>";
    }
}

$query = "SELECT notices.id, users.full_name, notices.title, notices.content, notices.created_at 
          FROM notices 
          LEFT JOIN users ON notices.user_id = users.id 
          ORDER BY notices.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notices - LOCKBANK</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="notices.css">
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
        <li><a href="notices.php" class="active"><i class="fa-solid fa-file-lines"></i> Notices</a></li>
        <li><a href="admin_logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <h2 class="toph2"><i class="fa-solid fa-bell"></i> Notices</h2>

    <div class="add-notice-container">
        <h3>Add New Notice</h3>
        <form method="POST" action="">

            <label for="accountInput">Enter Account Number or type 'All':</label>
            <input type="text" id="accountInput" name="account_number" placeholder="Enter Account Number or 'All'" required oninput="fetchUserDetails(this.value)">
            <input type="hidden" id="userId" name="user_id">

            <label for="userNameDisplay">Full Name:</label>
            <input type="text" id="userNameDisplay" placeholder="Full name will appear here" disabled>

            <div id="userDetails" style="margin-top: 5px; color: yellow;"></div>

            <input type="text" name="title" placeholder="Notice Title" required>
            <textarea name="content" placeholder="Notice Content" required style="height: 220px;"></textarea>
            <button type="submit" name="add_notice"><i class="fa-solid fa-paper-plane"></i> Send Notice</button>
        </form>
    </div>

    <div class="search-container">
        <input type="text" id="noticeSearch" onkeyup="searchNotices()" placeholder="Search by title, content or user">
    </div>

    <table class="user-table" id="noticeTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sent To</th>
                <th>Title</th>
                <th>Content</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['full_name'] ?? 'All Users'; ?></td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['content']); ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <td>
                        <a href="edit_notice.php?id=<?= $row['id']; ?>" class="btn-edit"><i class="fa-solid fa-edit"></i> Edit</a>
                        <a href="delete_notice.php?id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">
                            <i class="fa-solid fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    function searchNotices() {
        const input = document.getElementById("noticeSearch").value.toLowerCase();
        const rows = document.getElementById("noticeTable").getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName("td");
            let match = false;

            for (let j = 1; j < cells.length - 1; j++) {
                if (cells[j].textContent.toLowerCase().includes(input)) {
                    match = true;
                    break;
                }
            }
            rows[i].style.display = match ? "" : "none";
        }
    }

    function fetchUserDetails(value) {
        const userDetails = document.getElementById("userDetails");
        const hiddenId = document.getElementById("userId");
        const userNameDisplay = document.getElementById("userNameDisplay");

        if (value.trim().toLowerCase() === "all") {
            hiddenId.value = "all";
            userNameDisplay.value = "All Users";
            userDetails.innerHTML = "Notice will be sent to <strong>All Users</strong>";
            return;
        }

        if (!/^\d{6,}$/.test(value)) {
            hiddenId.value = "";
            userNameDisplay.value = "";
            userDetails.innerHTML = "";
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("GET", "get_user_name.php?account_number=" + value, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                    userNameDisplay.value = res.full_name;
                    userDetails.innerHTML = `A/C: ${res.account_number}`;
                    hiddenId.value = res.user_id;
                } else {
                    userDetails.innerHTML = "<span style='color: red;'>User not found.</span>";
                    userNameDisplay.value = "";
                    hiddenId.value = "";
                }
            }
        };
        xhr.send();
    }
</script>

</body>
</html>
