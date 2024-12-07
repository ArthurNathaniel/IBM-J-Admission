<?php
session_start(); // Start the session

// Destroy the session to log out the student
session_unset();
session_destroy();

echo "<script>alert('You have been logged out.'); window.location.href = 'student_login.php';</script>";
?>
