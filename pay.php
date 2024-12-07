<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $country = trim($_POST['country']);

    // Determine the amount based on country (Ghanaian or Foreign)
    $amount = $country === 'Ghanaian' ? 105 * 100 : 100 * 100; // Convert to pesewas (GHS) or cents (USD)

    try {
        // Check if the email or phone already exists
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = :email OR phone = :phone");
        $stmt->execute([':email' => $email, ':phone' => $phone]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            echo "<script>alert('Email or Phone number already exists. Please use different credentials.');</script>";
        } else {
            // Insert student with pending payment status
            $stmt = $conn->prepare("INSERT INTO students (name, email, phone, password, payment_status) 
                                    VALUES (:name, :email, :phone, :password, 'pending')");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $password
            ]);

            // Get the last inserted ID
            $student_id = $conn->lastInsertId();

            // Initialize Paystack Payment
            $url = "https://api.paystack.co/transaction/initialize";
            $fields = [
                'email' => $email,
                'amount' => $amount,
                'currency' => $country === 'Ghanaian' ? 'GHS' : 'USD',
                'callback_url' => 'http://yourdomain.com/verify.php?student_id=' . $student_id
            ];

            $fields_string = http_build_query($fields);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer YOUR_PAYSTACK_SECRET_KEY",
                "Cache-Control: no-cache"
            ]);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $result = json_decode($response, true);

            if ($result && $result['status']) {
                $authorization_url = $result['data']['authorization_url'];
                header("Location: " . $authorization_url);
            } else {
                echo "<script>alert('Payment could not be initialized. Please try again later.');</script>";
            }
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
    <title>Buy Form</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/pay.css">
</head>

<body>
    <div class="all">
        <div class="all_box">
           
            <div class="logo_img">
                    <img src="./images/logo.png" alt="">
                </div>
                <div class="forms_title">
                <h2>Buy Admission Form</h2>
                </div>
            <form method="POST" action="">
                <div class="forms">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="forms">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="forms">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="forms">
                    <label>Country</label>
                    <select name="country" required>
                        <option value="Ghanaian">Ghanaian</option>
                        <option value="Foreign">Foreign</option>
                    </select>
                </div>
                <div class="forms">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter a password" required>
                </div>
                <div class="forms">
                    <button type="submit">Buy Form</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
