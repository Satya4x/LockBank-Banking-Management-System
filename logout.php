<?php
session_start();
session_destroy();
echo "<script>alert('Logout Successful!'); window.location.href='login.php';</script>";
?>
