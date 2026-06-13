<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Notice ID missing!'); window.location.href='notices.php';</script>";
    exit();
}

$notice_id = intval($_GET['id']);

if (mysqli_query($conn, "DELETE FROM notices WHERE id = $notice_id")) {
    echo "<script>alert('Notice deleted successfully!'); window.location.href='notices.php';</script>";
} else {
    echo "<script>alert('Error deleting notice!'); window.location.href='notices.php';</script>";
}
?>
