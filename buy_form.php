<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $student_type = trim($_POST['student_type']);
    $payment_reference = trim($_POST['payment_reference']);

    try {
        // Check if the email or phone number already exists
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = :email OR phone = :phone");
        $stmt->execute([':email' => $email, ':phone' => $phone]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            echo "<script>alert('Email or phone already exists. Please use a different email or phone.');</script>";
        } else {
            // Insert new student into the database
            $stmt = $conn->prepare("INSERT INTO students (full_name, email, phone, password, student_type, payment_reference) 
                                    VALUES (:full_name, :email, :phone, :password, :student_type, :payment_reference)");
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $password,
                ':student_type' => $student_type,
                ':payment_reference' => $payment_reference
            ]);
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href = 'student_login.php';</script>";
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
    <title>Buy Admission Form</title>
    <link rel="stylesheet" href="./css/base.css">
</head>

<body>
    <div class="all">
        <div class="all_box">
            <h2>Buy Admission Form</h2>
            <form id="paymentForm">
                <div class="forms">
                    <label>Full Name</label>
                    <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" required>
                </div>
                <div class="forms">
                    <label>Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email address" required>
                </div>
                <div class="forms">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="forms">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <div class="forms">
                    <label>Are you Ghanaian or Foreign?</label>
                    <select name="student_type" id="student_type" required>
                        <option value="Ghanaian">Ghanaian (GHS 105)</option>
                        <option value="Foreign">Foreign ($100)</option>
                    </select>
                </div>
                <div class="forms">
                    <button type="button" onclick="payWithPaystack()">Buy Form</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack() {
            const full_name = document.getElementById('full_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const student_type = document.getElementById('student_type').value;
            
            let amount = student_type === 'Ghanaian' ? 10500 : 10000 * 12; // GHS 105 or $100 (in kobo or pesewas)
            
            let handler = PaystackPop.setup({
                key: 'pk_test_112a19f8ae988db1be016b0323b0e4fe95783fe8', 
                email: email,
                amount: amount,
                currency: student_type === 'Ghanaian' ? 'GHS' : 'USD',
                callback: function(response) {
                    // Payment was successful, store the details
                    fetch('buy_form.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            full_name: full_name,
                            email: email,
                            phone: phone,
                            password: password,
                            student_type: student_type,
                            payment_reference: response.reference
                        })
                    })
                    .then(res => res.text())
                    .then(data => {
                        alert('Payment successful! Reference: ' + response.reference);
                        window.location.href = 'student_login.php';
                    })
                    .catch(err => alert('Error: ' + err));
                },
                onClose: function() {
                    alert('Payment was not completed.');
                }
            });

            handler.openIframe();
        }
    </script>
</body>

</html>
