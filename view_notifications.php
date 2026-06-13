<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid notice ID.'); window.location.href='notifications.php';</script>";
    exit();
}

$notice_id = intval($_GET['id']);
$account_number = $_SESSION['user_id'];

$user_query = $conn->prepare("SELECT id FROM users WHERE account_number = ?");
$user_query->bind_param("s", $account_number);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();
$user_id = $user_data['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM notices WHERE id = ? AND (user_id IS NULL OR user_id = ?)");
$stmt->bind_param("ii", $notice_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Notice not found or access denied.'); window.location.href='notifications.php';</script>";
    exit();
}

$notice = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification - LOCKBANK</title>
    <link rel="stylesheet" href="view_notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container">
    <h2><?php echo htmlspecialchars($notice['title']); ?></h2>
    <div class="notice-meta">
        <i class="fas fa-clock"></i>
        <?php echo date("d M Y, h:i A", strtotime($notice['created_at'])); ?>
    </div>
    <div class="notice-content">
        <?php echo nl2br(htmlspecialchars($notice['content'])); ?>
    </div>
    <a href="notifications.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>

</body>
</html>
