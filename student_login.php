<?php
session_start(); // Start the session
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = trim($_POST['email_or_phone']);
    $password = trim($_POST['password']);

    try {
        // Check if the email or phone exists in the database (students table)
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = :email_or_phone OR phone = :email_or_phone");
        $stmt->execute([':email_or_phone' => $email_or_phone]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($password, $student['password'])) {
            // Save student info in session
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            
            // Redirect to the student portal after login
            echo "<script>alert('Login successful!'); window.location.href = 'student_portal.php';</script>";
        } else {
            // Invalid email or password
            echo "<script>alert('Invalid email/phone or password. Please try again.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/signup.css">
</head>

<body>

    <div class="all">
        <div class="all_box">
        <div class="logo_img">
                    <img src="./images/logo.png" alt="">
                </div>
                <div class="forms_title">
                <h2>Student Login
                </h2>
                </div>
                <br>
            <form method="POST" action="">
                <div class="forms">
                    <label>Email or Phone Number</label>
                    <input type="text" name="email_or_phone" placeholder="Enter your email or phone" required>
                </div>
                <div class="forms">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="forms">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
