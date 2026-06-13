<?php
include 'db.php';

if (isset($_GET['account_number'])) {
    $account = mysqli_real_escape_string($conn, $_GET['account_number']);
    $query = "SELECT id AS user_id, full_name, account_number FROM users WHERE account_number = '$account'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'full_name' => $row['full_name'], 'user_id' => $row['user_id'], 'account_number' => $row['account_number']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
