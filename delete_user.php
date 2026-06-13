<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if (isset($_GET['account'])) {
    $account_number = $_GET['account'];

    $stmt = $conn->prepare("DELETE FROM users WHERE account_number = ?");
    $stmt->bind_param("i", $account_number);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='manage_users.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='manage_users.php';</script>";
}
?>
