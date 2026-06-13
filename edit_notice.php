<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Notice ID not specified!'); window.location.href='notices.php';</script>";
    exit();
}

$notice_id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM notices WHERE id = $notice_id");
$notice = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_notice'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $update_query = "UPDATE notices SET title = '$title', content = '$content' WHERE id = $notice_id";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Notice updated successfully!'); window.location.href='notices.php';</script>";
    } else {
        echo "<script>alert('Error updating notice!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Notice - LOCKBANK</title>
    <link rel="stylesheet" href="edit_notice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <div class="content">
        <h2 class="toph2"><i class="fa-solid fa-edit"></i> Edit Notice</h2>
        <div class="add-notice-container">
            <form method="POST">
                <input type="text" name="title" value="<?= htmlspecialchars($notice['title']) ?>" required>
                <textarea name="content" required><?= htmlspecialchars($notice['content']) ?></textarea>
                <div class="button-row">
                    <button type="submit" name="update_notice" class="btn-edit">
                        <i class="fa-solid fa-check"></i> Update
                    </button>
                    <a href="notices.php" class="btn-view">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
