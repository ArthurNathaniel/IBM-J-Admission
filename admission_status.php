<?php
session_start(); // Start session to check if the student is logged in
include 'db.php'; // Include database connection

// Check if the student is logged in, otherwise redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID

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
    <title>Student Admission Status</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/student.css">
</head>

<body>

    <div class="student-container">
        <h2>Your Admission Details</h2>

        <!-- Two-column table format -->
        <table>
            <tbody>
                <tr>
                    <td><strong>Student Full Name:</strong></td>
                    <td><?= htmlspecialchars($student['full_name']) ?></td>
                </tr>
                <tr>
                    <td><strong>Admission ID:</strong></td>
                    <td><?= htmlspecialchars($student['id']) ?></td>
                </tr>
                <tr>
                    <td><strong>Entry Term:</strong></td>
                    <td><?= htmlspecialchars($student['entry_term']) ?></td>
                </tr>
                <tr>
                    <td><strong>Major/Course:</strong></td>
                    <td><?= htmlspecialchars($student['student_major']) ?></td>
                </tr>
                <tr>
                    <td><strong>Admission Status:</strong></td>
                    <td><?= htmlspecialchars($student['admission_status']) ?></td>
                </tr>
            </tbody>
        </table>

    </div>

</body>

</html>
