<?php
session_start(); // Start the session

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('You must log in to access this page.'); window.location.href = 'student_login.php';</script>";
    exit();
}

// Get student info from session
$student_name = $_SESSION['student_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="./css/base.css">
</head>

<body>
    <div class="all">
        <div class="all_box">
            <h2>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>
            <p>You're logged in to the student portal.</p>
            
            <div class="links">
                <a href="admission_status.php">Check Admission Status</a> <br>
                <a href="student_logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>
