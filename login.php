<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
                <h2>Admin Login</h2>
                <br>
                </div>
            <?php if (isset($error)) {
                echo "<p style='color:red; text-align:center;'>$error</p>";
            } ?>
            <form method="POST" action="">
                <div class="forms">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="forms">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <div class="form">
                    <input type="checkbox" id="show-password" onclick="togglePassword()"> Show Password
                </div>
                <div class="forms">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>