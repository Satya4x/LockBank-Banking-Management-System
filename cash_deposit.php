<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_number = trim($_POST['account_number']);
    $amount = trim($_POST['amount']);

    if (!is_numeric($amount) || $amount <= 0) {
        echo "<script>alert('Invalid amount!'); window.location.href='deposits.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT balance FROM users WHERE account_number = ?");
    $stmt->bind_param("s", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "<script>alert('User not found!'); window.location.href='deposits.php';</script>";
        exit();
    }

    $new_balance = $user['balance'] + $amount;

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE account_number = ?");
        $stmt->bind_param("ds", $new_balance, $account_number);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO transactions 
            (account_number, transaction_type, amount, status, balance_after) 
            VALUES (?, 'deposit', ?, 'completed', ?)");
        $stmt->bind_param("sdd", $account_number, $amount, $new_balance);
        $stmt->execute();

        $conn->commit();

        $notice_title = "Cash Deposit";
        $notice_content = "₹" . number_format($amount, 2) . " has been successfully deposited to your account.\nYour updated balance is ₹" . number_format($new_balance, 2) . ".";
        $insert_notice = $conn->prepare("INSERT INTO notices (user_id, title, content, created_at) VALUES ((SELECT id FROM users WHERE account_number = ?), ?, ?, NOW())");
        $insert_notice->bind_param("sss", $account_number, $notice_title, $notice_content);
        $insert_notice->execute();

        echo "<script>alert('Deposit Successful!'); window.location.href='deposits.php';</script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Deposit Failed: " . $e->getMessage() . "'); window.location.href='deposits.php';</script>";
    }
}
?>
