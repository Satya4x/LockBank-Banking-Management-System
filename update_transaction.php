<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='admin_login.php';</script>";
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $transaction_id = $_GET['id'];
    $new_status = $_GET['status'];

    $stmt = $conn->prepare("SELECT account_number, amount, transaction_type, status FROM transactions WHERE id=?");
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();

    if (!$transaction) {
        echo "<script>alert('Transaction not found!'); window.location.href='deposits.php';</script>";
        exit();
    }

    $account_number = $transaction['account_number'];
    $amount = $transaction['amount'];
    $transaction_type = $transaction['transaction_type'];
    $current_status = $transaction['status'];

    if ($current_status == 'completed') {
        echo "<script>alert('Transaction is already completed!'); window.location.href='deposits.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT balance FROM users WHERE account_number=?");
    $stmt->bind_param("s", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "<script>alert('User account not found!'); window.location.href='deposits.php';</script>";
        exit();
    }

    $current_balance = $user['balance'];
    $new_balance = $current_balance;

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE transactions SET status=? WHERE id=?");
        $stmt->bind_param("si", $new_status, $transaction_id);
        $stmt->execute();

        if ($new_status == "completed") {
            if ($transaction_type == "deposit") {
                $new_balance += $amount;
            } elseif ($transaction_type == "withdrawal") {
                if ($current_balance < $amount) {
                    throw new Exception("Insufficient balance for withdrawal!");
                }
                $new_balance -= $amount;
            }

            $stmt = $conn->prepare("UPDATE users SET balance=? WHERE account_number=?");
            $stmt->bind_param("ds", $new_balance, $account_number);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE transactions SET balance_after=? WHERE id=?");
            $stmt->bind_param("di", $new_balance, $transaction_id);
            $stmt->execute();
        }

        $conn->commit();
        echo "<script>alert('Transaction updated successfully!'); window.location.href='deposits.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Transaction update failed: " . $e->getMessage() . "'); window.location.href='deposits.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='deposits.php';</script>";
}
?>
