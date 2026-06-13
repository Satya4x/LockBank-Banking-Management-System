<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = strtoupper($_POST['full_name']);
    $aadhar = $_POST['aadhar'];
    $pan_number = strtoupper($_POST['pan_number']);
    $gender = $_POST['gender'];
    $email = strtolower($_POST['email']);
    $mobile = $_POST['mobile'];
    $address = strtoupper($_POST['address']);
    $pincode = $_POST['pincode'];
    $dob = $_POST['dob'];
    $account_type = $_POST['account_type'];
    $occupation = strtoupper($_POST['occupation']);
    $lpin = $_POST['lpin'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $account_number = mt_rand(100000000000, 999999999999);

    $photo = $_FILES['photo'];
    $photo_name = time() . "_" . basename($photo['name']);
    $photo_path = "uploads/" . $photo_name;

    if ($photo['size'] > 4 * 1024 * 1024) {
        echo "<script>alert('Photo size must be below 4MB');</script>";
    } else {
        move_uploaded_file($photo['tmp_name'], $photo_path);

        $query = "INSERT INTO users (account_number, full_name, aadhar, pan_number, gender, email, mobile, address, pincode, dob, account_type, occupation, password, photo, lpin) 
        VALUES ('$account_number', '$full_name', '$aadhar', '$pan_number', '$gender', '$email', '$mobile', '$address', '$pincode', '$dob', '$account_type', '$occupation', '$password', '$photo_name', '$lpin')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Account Created Successfully!'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open New Account - LOCKBANK</title>
    <link rel="stylesheet" href="signup_style.css">
</head>
<body>
    <form action="signup.php" method="POST" enctype="multipart/form-data">
        <div class="logo">LOCK<span>BANK</span></div>
        <h2>OPEN NEW ACCOUNT FORM</h2>

        <label>ACCOUNT NUMBER</label>
        <input type="text" value="<?php echo mt_rand(100000000000, 999999999999); ?>" readonly>

        <label>FULL NAME</label>
        <input type="text" name="full_name" required oninput="this.value = this.value.toUpperCase()" maxlength="50">

        <label>AADHAR NUMBER</label>
        <input type="text" name="aadhar" required pattern="\d{12}" title="Enter exactly 12 digits" maxlength="12">
        
        <label>PAN NUMBER</label>
        <input type="text" name="pan_number" required pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter valid PAN number (e.g., ABCDE1234F)" maxlength="10" oninput="this.value = this.value.toUpperCase()">

        <label>GENDER</label>
        <select name="gender" required>
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label>EMAIL ADDRESS</label>
        <input type="email" name="email" required oninput="this.value = this.value.toLowerCase()" maxlength="50">

        <label>MOBILE NO.</label>
        <input type="text" name="mobile" required pattern="\d{10}" title="Enter exactly 10 digits" maxlength="10">

        <label>ADDRESS</label>
        <textarea name="address" required oninput="this.value = this.value.toUpperCase()" maxlength="100"></textarea>

        <label>ADDRESS PINCODE</label>
        <input type="text" name="pincode" required pattern="\d{6}" title="Enter exactly 6 digits" maxlength="6">

        <label>PASSPORT PHOTO (Max 4MB)</label>
        <input type="file" name="photo" required accept="image/*">

        <label>DATE OF BIRTH</label>
        <input type="date" name="dob" required>

        <label>ACCOUNT TYPE</label>
        <select name="account_type" required>
            <option value="">Select</option>
            <option value="Saving">Saving</option>
            <option value="Current">Current</option>
        </select>

        <label>OCCUPATION</label>
        <input type="text" name="occupation" required oninput="this.value = this.value.toUpperCase()" maxlength="50">

        <label for="lpin">LPIN (5 DIGITS) – Used to authorize secure transactions</label>
    <input type="password" name="lpin" id="lpin" required pattern="\d{5}" title="Enter exactly 5 digits" maxlength="5" style="padding-right: 40px;">
      <label>PASSWORD</label>
        <input type="password" name="password" required minlength="6" maxlength="50">

        <label>Terms & Conditions</label>
        <div class="terms-box">
            <p>1. You agree that all the information provided is accurate.</p>
            <p>2. The bank reserves the right to suspend or close your account in case of any suspicious activity.</p>
            <p>3. Your personal data will be securely stored and will not be shared with third parties without your consent.</p>
            <p>4. Any unauthorized transactions from your account should be reported immediately.</p>
            <p>5. The bank is not liable for losses due to negligence in handling your account credentials.</p>
        </div>

        <div class="terms">
            <input class="terms" type="checkbox" name="agree" required>
            <label>I have read and agree to the Terms & Conditions</label>
        </div>

        <button type="submit">CREATE ACCOUNT</button>
    </form>
</body>
</html>
