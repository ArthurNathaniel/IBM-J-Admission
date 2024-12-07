<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            echo "<script>alert('Email already exists. Please use a different email.');</script>";
        } else {
            // Insert new admin into the database
            $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password
            ]);
            echo "<script>alert('Signup successful! You can now login.'); window.location.href = 'login.php';</script>";
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
    <title>Admin Signup</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/signup.css">
</head>

<body>
    <div class="all">
        <div class="all_box">
        
            <form method="POST" action="">
                <div class="logo_img">
                    <img src="./images/logo.png" alt="">
                </div>
                <div class="forms_title">
                <h2>Admin Signup</h2>
                </div>
                <div class="forms">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
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
                    <button type="submit">Signup</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const showPasswordCheckbox = document.getElementById('show-password');
            
            // Toggle the type of the password field
            passwordField.type = showPasswordCheckbox.checked ? 'text' : 'password';
        }
    </script>
</body>

</html>
