<?php
session_start(); // Start the session to check if the admin is logged in
include 'db.php'; // Include database connection

// Check if the user is logged in as admin, otherwise redirect
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['admission_form'])) {
    $upload_dir = 'uploads/'; // Folder to save the uploaded file

    // Get the uploaded file information
    $file_name = $_FILES['admission_form']['name'];
    $file_tmp = $_FILES['admission_form']['tmp_name'];
    $file_size = $_FILES['admission_form']['size'];
    $file_error = $_FILES['admission_form']['error'];

    // Define allowed file types and max file size (e.g., PDF, DOC, DOCX)
    $allowed_types = ['pdf', 'doc', 'docx'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Check for file upload errors
    if ($file_error !== UPLOAD_ERR_OK) {
        echo "<script>alert('Error uploading file.');</script>";
        exit();
    }

    // Validate file type
    if (!in_array($file_ext, $allowed_types)) {
        echo "<script>alert('Invalid file type. Only PDF, DOC, and DOCX files are allowed.');</script>";
        exit();
    }

    // Validate file size (e.g., 10MB limit)
    if ($file_size > 10485760) {
        echo "<script>alert('File is too large. Maximum size is 10MB.');</script>";
        exit();
    }

    // Generate a unique name for the file to avoid collisions
    $unique_file_name = uniqid('admission_', true) . '.' . $file_ext;

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($file_tmp, $upload_dir . $unique_file_name)) {
        // Optionally store the file information in the database
        try {
            $stmt = $conn->prepare("INSERT INTO uploaded_admission_forms (file_name, upload_date) VALUES (:file_name, NOW())");
            $stmt->execute([':file_name' => $unique_file_name]);
            echo "<script>alert('File uploaded successfully!'); window.location.href = 'admin_dashboard.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error saving file details: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Error moving file to the upload directory.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Admission Form</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="admin-container">
        <h2>Upload Admission Form</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="admission_form">Choose Admission Form:</label>
            <input type="file" name="admission_form" accept=".pdf,.doc,.docx" required>
            <button type="submit">Upload</button>
        </form>
    </div>

</body>

</html>
