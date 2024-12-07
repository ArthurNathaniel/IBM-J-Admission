<?php
session_start(); // Start session
include 'db.php'; // Include database connection

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details from the database
try {
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = :student_id");
    $stmt->execute([':student_id' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo "Student not found!";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .profile-img {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .profile-img img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 5px solid #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admission Form Preview</h2>

    <!-- Profile Image -->
    <div class="profile-img">
        <?php if (!empty($student['profile_image'])): ?>
            <img src="<?= htmlspecialchars($student['profile_image']) ?>" alt="Profile Image">
        <?php else: ?>
            <img src="default-profile.png" alt="Default Profile Image">
        <?php endif; ?>
    </div>

    <!-- Display Student Information -->
    <p><strong>Full Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
    <p><strong>Email Address:</strong> <?= htmlspecialchars($student['email']) ?></p>
    <p><strong>Phone Number:</strong> <?= htmlspecialchars($student['phone']) ?></p>
    <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['date_of_birth']) ?></p>
    <p><strong>Religion:</strong> <?= htmlspecialchars($student['religion']) ?></p>
    <p><strong>Nationality:</strong> <?= htmlspecialchars($student['nationality']) ?></p>
    <p><strong>Physical Disability:</strong> <?= htmlspecialchars($student['physical_disability'] ?? 'None') ?></p>
    <p><strong>Marital Status:</strong> <?= htmlspecialchars($student['marital_status']) ?></p>

    <!-- Emergency Contact Information -->
    <h3>Emergency Contact</h3>
    <p><strong>Contact Name:</strong> <?= htmlspecialchars($student['emergency_contact_name']) ?></p>
    <p><strong>Contact Number:</strong> <?= htmlspecialchars($student['emergency_contact_number']) ?></p>
    <p><strong>Relationship:</strong> <?= htmlspecialchars($student['emergency_relationship']) ?></p>

    <!-- Course Selection -->
    <h3>Course Selection</h3>
    <p><strong>Selected Course:</strong> <?= htmlspecialchars($student['selected_course']) ?></p>

    <!-- Display Uploads -->
    <h3>Uploaded Documents</h3>
    <p><strong>Medical Report:</strong> 
        <?php if (!empty($student['medical_report'])): ?>
            <a href="<?= htmlspecialchars($student['medical_report']) ?>" class="btn" target="_blank">View Medical Report</a>
        <?php else: ?>
            <span>Not Provided</span>
        <?php endif; ?>
    </p>

    <p><strong>WASSCE Results:</strong> 
        <?php if (!empty($student['wassce_results'])): ?>
            <a href="<?= htmlspecialchars($student['wassce_results']) ?>" class="btn" target="_blank">View WASSCE Results</a>
        <?php else: ?>
            <span>Not Provided</span>
        <?php endif; ?>
    </p>

    <a href="student_dashboard.php" class="btn">Back to Dashboard</a>
    <a href="edit.php" class="btn btn-edit">Edit Details</a>
</div>

</body>
</html>
