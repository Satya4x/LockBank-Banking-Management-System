<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if (isset($_GET['account'])) {
    $account_number = $_GET['account'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE account_number = ?");
    $stmt->bind_param("i", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "<script>alert('User not found!'); window.location.href='manage_users.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='manage_users.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - LOCKBANK</title>
    <link rel="stylesheet" href="view_edit_user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="content">
        <div class="profile-container">
            <img src="uploads/<?php echo $user['photo']; ?>" alt="User Photo" class="profile-photo">
        </div>

        <h2 class="toph2">User Details</h2>

        <table class="user-table">
            <tr><th><i class="fas fa-id-card"></i> Account Number:</th><td><?php echo $user['account_number']; ?></td></tr>
            <tr><th><i class="fas fa-user"></i> Full Name:</th><td><?php echo $user['full_name']; ?></td></tr>
            <tr><th><i class="fas fa-id-badge"></i> Aadhar Number:</th><td><?php echo $user['aadhar']; ?></td></tr>
            <tr><th><i class="fas fa-address-card"></i> PAN Number:</th><td><?php echo $user['pan_number']; ?></td></tr>
            <tr><th><i class="fas fa-venus-mars"></i> Gender:</th><td><?php echo $user['gender']; ?></td></tr>
            <tr><th><i class="fas fa-envelope"></i> Email:</th><td><?php echo $user['email']; ?></td></tr>
            <tr><th><i class="fas fa-phone"></i> Mobile:</th><td><?php echo $user['mobile']; ?></td></tr>
            <tr><th><i class="fas fa-map-marker-alt"></i> Address:</th><td><?php echo $user['address']; ?></td></tr>
            <tr><th><i class="fas fa-map-pin"></i> Pincode:</th><td><?php echo $user['pincode']; ?></td></tr>
            <tr><th><i class="fas fa-calendar"></i> Date of Birth:</th><td><?php echo $user['dob']; ?></td></tr>
            <tr><th><i class="fas fa-university"></i> Account Type:</th><td><?php echo $user['account_type']; ?></td></tr>
            <tr><th><i class="fas fa-briefcase"></i> Occupation:</th><td><?php echo $user['occupation']; ?></td></tr>
            <tr><th><i class="fas fa-calendar-alt"></i> Created At:</th><td><?php echo date("d M Y, H:i A", strtotime($user['created_at'])); ?></td></tr>
        </table>

        <br>
        <a href="manage_users.php" class="btn-edit"><i class="fas fa-arrow-left"></i> Go Back</a>
    </div>
</body>
</html>
