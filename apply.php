<?php
session_start(); // Start the session
include 'db.php'; // Include database connection

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student info from the database
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

// Handle form submission
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
    $medical_report = $_FILES['medical_report']['name'];
    $wassce_results = $_FILES['wassce_results']['name'];
    $profile_image = $_FILES['profile_image']['name'];

    // File paths to upload the files
    $medical_report_path = "uploads/medical_reports/" . basename($medical_report);
    $wassce_results_path = "uploads/wassce_results/" . basename($wassce_results);
    $profile_image_path = "uploads/profile_images/" . basename($profile_image);

    try {
        // Upload files
        if (!empty($medical_report)) {
            move_uploaded_file($_FILES['medical_report']['tmp_name'], $medical_report_path);
        }
        if (!empty($wassce_results)) {
            move_uploaded_file($_FILES['wassce_results']['tmp_name'], $wassce_results_path);
        }
        if (!empty($profile_image)) {
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image_path);
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
            medical_report = :medical_report, 
            wassce_results = :wassce_results, 
            profile_image = :profile_image 
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
            ':medical_report' => !empty($medical_report) ? $medical_report_path : null,
            ':wassce_results' => !empty($wassce_results) ? $wassce_results_path : null,
            ':profile_image' => !empty($profile_image) ? $profile_image_path : null,
            ':student_id' => $student_id
        ]);

        echo "<script>alert('Admission form submitted successfully!'); window.location.href = 'preview.php';</script>";
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
    <title>Student Admission Form</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/apply.css">
</head>

<body>
    <?php include 'navbar.php' ?>
    <div class="apply_all">

    <div class="note_div">
        <h4>NOTE</h4>
        <p>If you have already submitted your application, click <a href="preview.php">here</a> to preview it</p>
    </div>
        <h2>Student Admission Form</h2>

        <form method="POST" action="" enctype="multipart/form-data">
        <div class="forms">
                <h3>Personal Info:</h3>
            </div>
            <div class="forms_group">
                <div class="forms">
                    <label>Profile Image:</label>
                    <input type="file" name="profile_image" required>
                </div>

                <div class="forms">
                    <label>Full Name:</label>
                    <input type="text" value="<?= htmlspecialchars($student['full_name']) ?>" readonly>
                </div>


                <div class="forms">
                    <label>Phone Number:</label>
                    <input type="text" value="<?= htmlspecialchars($student['phone']) ?>" readonly>
                </div>
            </div>

            <div class="forms_group">
                <div class="forms">
                    <label>Email Address:</label>
                    <input type="email" value="<?= htmlspecialchars($student['email']) ?>" readonly>
                </div>
                <div class="forms">
                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="forms">

                    <label>Date of Birth:</label>
                    <input type="date" name="date_of_birth" required>
                </div>
            </div>

            <div class="forms_group">
                <div class="forms">
                    <label>Religion:</label>
                    <select name="religion" required>
                        <option value="Christian">Christian</option>
                        <option value="Islamic">Islamic</option>
                        <option value="Traditional">Traditional</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="forms">
                    <label>Nationality:</label>
                    <select name="nationality" required>
                        <option value="Ghanaian">Ghanaian</option>
                        <option value="Foreign">Foreign</option>
                    </select>
                </div>
                <div class="forms">

                    <label>Physical Disability:</label>
                    <input type="text" name="physical_disability">
                </div>
            </div>

            <div class="forms_group">
                <div class="forms">
                    <label>Marital Status:</label>
                    <select name="marital_status" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                    </select>
                </div>
            </div>
            <div class="forms">
                <h3>Emergency Contact:</h3>
            </div>
            <div class="forms_group">
                <div class="forms">
                    <label>Emergency Contact Name:</label>
                    <input type="text" name="emergency_contact_name" required>
                </div>
                <div class="forms">
                    <label>Emergency Contact Number:</label>
                    <input type="text" name="emergency_contact_number" required>
                </div>
                <div class="forms">
                    <label>Emergency Relationship:</label>
                    <input type="text" name="emergency_relationship" required>
                </div>
            </div>
            <div class="forms">
                <h3>Academics & Health Info:</h3>
            </div>
            <div class="forms_group">
            <div class="forms">
               <label>Course / Programmes</label>
                <select name="selected_course" required>
                    <optgroup label="HND Programmes - 3 Years">
                        <option value="HND Communication">HND - Communication</option>
                        <option value="HND Marketing">HND - Marketing</option>
                        <option value="HND Public Relations">HND - Public Relations</option>
                        <option value="HND Journalism">HND - Journalism</option>
                    </optgroup>
                    <optgroup label="Diploma Programmes - 2 Years">
                        <option value="Diploma Journalism">Diploma - Journalism</option>
                        <option value="Diploma Marketing">Diploma - Marketing</option>
                        <option value="Diploma Public Relations">Diploma - Public Relations</option>
                    </optgroup>
                    <optgroup label="Certificate Programmes - 6 Months">
                        <option value="Certificate Script Writing">Certificate - Script Writing</option>
                        <option value="Certificate Photography">Certificate - Photography</option>
                        <option value="Certificate Film Editing">Certificate - Film Editing</option>
                        <option value="Certificate Film Acting & Directing">Certificate - Film Acting & Directing</option>
                        <option value="Certificate Graphic Designing">Certificate - Graphic Designing</option>
                    </optgroup>
                    <optgroup label="Proposed HND Programmes - 3 Years">
                        <option value="HND Investigative Journalism">HND - Investigative Journalism</option>
                        <option value="HND Secretaryship in Management">HND - Secretaryship in Management</option>
                        <option value="HND Business Administration">HND - Business Administration</option>
                    </optgroup>
                </select>
            </div>
          
            <div class="forms">
                <label>Upload Medical Report (Optional):</label>
                <input type="file" name="medical_report">
            </div>
            <div class="forms">
                <label>Upload WASSCE Results:</label>
                <input type="file" name="wassce_results" required>
            </div>
            </div>
            <div class="forms">
                <button type="submit">Submit Form</button>
            </div>
           
        </form>
    </div>

</body>

</html>