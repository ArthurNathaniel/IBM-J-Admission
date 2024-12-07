<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

echo "<h1>Welcome, " . $_SESSION['admin_name'] . "!</h1>";
echo "<p>This is your admin dashboard.</p>";
echo "<a href='logout.php'>Logout</a>";
?>
