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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $account_type = $_POST['account_type'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $occupation = $_POST['occupation'];
    $aadhar = $_POST['aadhar'];
    $pan_number = $_POST['pan_number'];
    $lpin = !empty($_POST['lpin']) ? password_hash($_POST['lpin'], PASSWORD_DEFAULT) : $user['lpin'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo'];
        $photo_name = time() . "_" . basename($photo['name']);
        $photo_path = "uploads/" . $photo_name;

        if ($photo['size'] > 4 * 1024 * 1024) {
            echo "<script>alert('Photo size must be below 4MB');</script>";
        } else {
            if (move_uploaded_file($photo['tmp_name'], $photo_path)) {
                $stmt = $conn->prepare("UPDATE users SET photo=? WHERE account_number=?");
                $stmt->bind_param("si", $photo_name, $account_number);
                $stmt->execute();
            }
        }
    }

    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, mobile=?, account_type=?, address=?, pincode=?, dob=?, gender=?, occupation=?, aadhar=?, pan_number=?, lpin=?, password=? WHERE account_number=?");
    $stmt->bind_param("ssisssssssssss", $full_name, $email, $mobile, $account_type, $address, $pincode, $dob, $gender, $occupation, $aadhar, $pan_number, $lpin, $password, $account_number);
        
    if ($stmt->execute()) {
        echo "<script>alert('User details updated successfully!'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user details!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - LOCKBANK</title>
    <link rel="stylesheet" href="view_edit_user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="content">
        <div class="profile-container">
            <img src="uploads/<?php echo $user['photo']; ?>" alt="User Photo" class="profile-photo">
        </div>

        <h2 class="toph2"><i class="fa-solid fa-user-edit"></i> Edit User</h2>

        <form action="edit_user.php?account=<?php echo $account_number; ?>" method="POST" enctype="multipart/form-data">

            <label><i class="fa-solid fa-user"></i> Full Name:</label>
            <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>

            <label><i class="fa-solid fa-envelope"></i> Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

            <label><i class="fa-solid fa-phone"></i> Mobile:</label>
            <input type="text" name="mobile" value="<?php echo $user['mobile']; ?>" required pattern="\d{10}" maxlength="10" title="Enter a valid 10-digit mobile number">

            <label><i class="fa-solid fa-credit-card"></i> Account Type:</label>
            <select name="account_type" required>
                <option value="Saving" <?php echo ($user['account_type'] == 'Saving') ? 'selected' : ''; ?>>Saving</option>
                <option value="Current" <?php echo ($user['account_type'] == 'Current') ? 'selected' : ''; ?>>Current</option>
            </select>

            <label><i class="fa-solid fa-id-card"></i> Aadhar Number:</label>
            <input type="text" name="aadhar" value="<?php echo $user['aadhar']; ?>" required pattern="\d{12}" maxlength="12" title="Enter a valid 12-digit Aadhar number">

            <label><i class="fa-solid fa-address-card"></i> PAN Number:</label>
            <input type="text" name="pan_number" value="<?php echo $user['pan_number']; ?>" required pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter a valid PAN number (e.g., ABCDE1234F)">

            <label><i class="fa-solid fa-map-marker-alt"></i> Address:</label>
            <textarea name="address" required><?php echo $user['address']; ?></textarea>

            <label><i class="fa-solid fa-location-dot"></i> Pincode:</label>
            <input type="text" name="pincode" value="<?php echo $user['pincode']; ?>" required pattern="\d{6}" maxlength="6" title="Enter a valid 6-digit pincode">

            <label><i class="fa-solid fa-calendar"></i> Date of Birth:</label>
            <input type="date" name="dob" value="<?php echo $user['dob']; ?>" required>

            <label><i class="fa-solid fa-venus-mars"></i> Gender:</label>
            <select name="gender" required>
                <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <label><i class="fa-solid fa-briefcase"></i> Occupation:</label>
            <input type="text" name="occupation" value="<?php echo $user['occupation']; ?>" required>

            <label><i class="fa-solid fa-image"></i> Profile Photo (Max: 4MB)</label>
            <input type="file" name="photo" accept="image/*">

            <label><i class="fa-solid fa-lock"></i> LPIN (Used for secure transactions)</label>

            <div class="password-wrapper">
                <input type="password" name="lpin" id="lpinField" placeholder="Enter new LPIN (leave blank to keep existing)" required pattern="\d{5}" maxlength="5">
            </div>

            <label><i class="fa-solid fa-key"></i> Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="passwordField" placeholder="Enter new password (leave blank to keep existing)" required minlength="6" maxlength="50">
            </div>

            <div class="button-container">
                <button type="submit" class="btn-edit"><i class="fa-solid fa-save"></i> Update User</button>
                <a href="manage_users.php" class="btn-delete"><i class="fa-solid fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
