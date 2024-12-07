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

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $physical_disability = $_POST['physical_disability'];
    $marital_status = $_POST['marital_status'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $emergency_contact_number = $_POST['emergency_contact_number'];
    $emergency_relationship = $_POST['emergency_relationship'];
    $selected_course = $_POST['selected_course'];

    // File uploads
    $profile_image = $_FILES['profile_image']['name'];
    $medical_report = $_FILES['medical_report']['name'];
    $wassce_results = $_FILES['wassce_results']['name'];

    // File paths to upload the files
    $profile_image_path = "uploads/profile_images/" . basename($profile_image);
    $medical_report_path = "uploads/medical_reports/" . basename($medical_report);
    $wassce_results_path = "uploads/wassce_results/" . basename($wassce_results);

    try {
        // Upload files if new files are provided
        if (!empty($profile_image)) {
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image_path);
        } else {
            $profile_image_path = $student['profile_image'];
        }

        if (!empty($medical_report)) {
            move_uploaded_file($_FILES['medical_report']['tmp_name'], $medical_report_path);
        } else {
            $medical_report_path = $student['medical_report'];
        }

        if (!empty($wassce_results)) {
            move_uploaded_file($_FILES['wassce_results']['tmp_name'], $wassce_results_path);
        } else {
            $wassce_results_path = $student['wassce_results'];
        }

        // Update student details in the database
        $stmt = $conn->prepare("UPDATE students SET 
            gender = :gender, 
            date_of_birth = :date_of_birth, 
            religion = :religion, 
            nationality = :nationality, 
            physical_disability = :physical_disability, 
            marital_status = :marital_status, 
            emergency_contact_name = :emergency_contact_name, 
            emergency_contact_number = :emergency_contact_number, 
            emergency_relationship = :emergency_relationship, 
            selected_course = :selected_course, 
            profile_image = :profile_image, 
            medical_report = :medical_report, 
            wassce_results = :wassce_results 
        WHERE id = :student_id");

        $stmt->execute([
            ':gender' => $gender,
            ':date_of_birth' => $date_of_birth,
            ':religion' => $religion,
            ':nationality' => $nationality,
            ':physical_disability' => $physical_disability,
            ':marital_status' => $marital_status,
            ':emergency_contact_name' => $emergency_contact_name,
            ':emergency_contact_number' => $emergency_contact_number,
            ':emergency_relationship' => $emergency_relationship,
            ':selected_course' => $selected_course,
            ':profile_image' => $profile_image_path,
            ':medical_report' => $medical_report_path,
            ':wassce_results' => $wassce_results_path,
            ':student_id' => $student_id
        ]);

        echo "<script>alert('Details updated successfully!'); window.location.href = 'preview.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admission Details</title>
</head>
<body>

<h2>Edit Admission Details</h2>

<form method="POST" action="" enctype="multipart/form-data">
    <label>Full Name:</label>
    <input type="text" value="<?= htmlspecialchars($student['full_name']) ?>" readonly><br>

    <label>Phone Number:</label>
    <input type="text" value="<?= htmlspecialchars($student['phone']) ?>" readonly><br>

    <label>Email Address:</label>
    <input type="email" value="<?= htmlspecialchars($student['email']) ?>" readonly><br>

    <label>Gender:</label>
    <select name="gender">
        <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
    </select><br>

    <label>Date of Birth:</label>
    <input type="date" name="date_of_birth" value="<?= htmlspecialchars($student['date_of_birth']) ?>"><br>

    <label>Religion:</label>
    <input type="text" name="religion" value="<?= htmlspecialchars($student['religion']) ?>"><br>

    <label>Nationality:</label>
    <input type="text" name="nationality" value="<?= htmlspecialchars($student['nationality']) ?>"><br>

    <label>Physical Disability:</label>
    <input type="text" name="physical_disability" value="<?= htmlspecialchars($student['physical_disability']) ?>"><br>

    <label>Marital Status:</label>
    <input type="text" name="marital_status" value="<?= htmlspecialchars($student['marital_status']) ?>"><br>

    <h3>Emergency Contact</h3>
    <label>Emergency Contact Name:</label>
    <input type="text" name="emergency_contact_name" value="<?= htmlspecialchars($student['emergency_contact_name']) ?>"><br>

    <label>Emergency Contact Number:</label>
    <input type="text" name="emergency_contact_number" value="<?= htmlspecialchars($student['emergency_contact_number']) ?>"><br>

    <label>Emergency Relationship:</label>
    <input type="text" name="emergency_relationship" value="<?= htmlspecialchars($student['emergency_relationship']) ?>"><br>

    <h3>Course Selection</h3>
    <select name="selected_course">
    <optgroup label="HND Programmes - 3 Years">
        <option value="Communication" <?= $student['selected_course'] == 'Communication' ? 'selected' : '' ?>>HND - Communication</option>
        <option value="Marketing" <?= $student['selected_course'] == 'Marketing' ? 'selected' : '' ?>>HND - Marketing</option>
        <option value="Public Relations" <?= $student['selected_course'] == 'Public Relations' ? 'selected' : '' ?>>HND - Public Relations</option>
        <option value="Journalism" <?= $student['selected_course'] == 'Journalism' ? 'selected' : '' ?>>HND - Journalism</option>
    </optgroup>
    <optgroup label="Diploma Programmes - 2 Years">
        <option value="Diploma Journalism" <?= $student['selected_course'] == 'Diploma Journalism' ? 'selected' : '' ?>>Diploma - Journalism</option>
        <option value="Diploma Marketing" <?= $student['selected_course'] == 'Diploma Marketing' ? 'selected' : '' ?>>Diploma - Marketing</option>
        <option value="Diploma Public Relations" <?= $student['selected_course'] == 'Diploma Public Relations' ? 'selected' : '' ?>>Diploma - Public Relations</option>
    </optgroup>
    <optgroup label="Certificate Programmes - 6 Months">
        <option value="Script Writing" <?= $student['selected_course'] == 'Script Writing' ? 'selected' : '' ?>>Certificate - Script Writing</option>
        <option value="Photography" <?= $student['selected_course'] == 'Photography' ? 'selected' : '' ?>>Certificate - Photography</option>
        <option value="Film Editing" <?= $student['selected_course'] == 'Film Editing' ? 'selected' : '' ?>>Certificate - Film Editing</option>
        <option value="Film Acting & Directing" <?= $student['selected_course'] == 'Film Acting & Directing' ? 'selected' : '' ?>>Certificate - Film Acting & Directing</option>
        <option value="Graphic Designing" <?= $student['selected_course'] == 'Graphic Designing' ? 'selected' : '' ?>>Certificate - Graphic Designing</option>
    </optgroup>
    <optgroup label="Proposed HND Programmes - 3 Years">
        <option value="Investigative Journalism" <?= $student['selected_course'] == 'Investigative Journalism' ? 'selected' : '' ?>>HND - Investigative Journalism</option>
        <option value="Secretaryship in Management" <?= $student['selected_course'] == 'Secretaryship in Management' ? 'selected' : '' ?>>HND - Secretaryship in Management</option>
        <option value="Business Administration" <?= $student['selected_course'] == 'Business Administration' ? 'selected' : '' ?>>HND - Business Administration</option>
    </optgroup>
</select><br>


    <h3>Upload Files</h3>
    <label>Profile Image:</label>
    <input type="file" name="profile_image"><br>

    <label>Medical Report:</label>
    <input type="file" name="medical_report"><br>

    <label>WASSCE Results:</label>
    <input type="file" name="wassce_results"><br>

    <button type="submit">Update Details</button>
</form>

</body>
</html>
