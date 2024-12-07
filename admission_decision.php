<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in as admin, otherwise redirect
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to admin login if not logged in
    exit();
}

// Fetch all students
try {
    $stmt = $conn->prepare("SELECT * FROM students");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Handle admission decision form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
    $admission_status = $_POST['admission_status'];
    $entry_term = $_POST['entry_term'];
    $student_major = $_POST['student_major'];

    try {
        // Update the student's admission status and other details in the database
        $stmt = $conn->prepare("UPDATE students SET admission_status = :admission_status, entry_term = :entry_term, student_major = :student_major WHERE id = :student_id");
        $stmt->execute([
            ':admission_status' => $admission_status,
            ':entry_term' => $entry_term,
            ':student_major' => $student_major,
            ':student_id' => $student_id
        ]);

        // Notify the admin
        echo "<script>alert('Admission decision updated successfully!'); window.location.href = 'admin_view_students.php';</script>";
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
    <title>Admin - Admission Decision</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/admin.css">
</head>

<body>

    <div class="admin-container">
        <h2>Make Admission Decision</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Entry Term</th>
                    <th>Major/Course</th>
                    <th>Admission Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id']) ?></td>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= htmlspecialchars($student['phone']) ?></td>
                            <td><?= htmlspecialchars($student['entry_term']) ?></td>
                            <td><?= htmlspecialchars($student['student_major']) ?></td>
                            <td><?= htmlspecialchars($student['admission_status']) ?></td>
                            <td>
                                <button onclick="showDecisionForm(<?= $student['id'] ?>, '<?= $student['full_name'] ?>', '<?= $student['entry_term'] ?>', '<?= $student['student_major'] ?>')">Make Decision</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No students found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Admission decision form (hidden initially) -->
        <div id="decision-form" style="display:none;">
            <h3>Admission Decision for <span id="student-name"></span></h3>
            <form method="POST" action="">
                <input type="hidden" name="student_id" id="student-id">
                
                <!-- Editable Entry Term and Major/Course -->
                <label for="entry-term">Entry Term:</label>
                <input type="text" id="entry-term" name="entry_term" required>
                
                <label for="student-major">Student Major/Course:</label>
                <input type="text" id="student-major" name="student_major" required>
                
                <label for="admission-status">Admission Status:</label>
                <select name="admission_status" id="admission-status" required>
                    <option value="Accepted">Accepted</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Pending">Pending</option>
                </select>
                <button type="submit">Submit Decision</button>
                <button type="button" onclick="hideDecisionForm()">Cancel</button>
            </form>
        </div>

    </div>

    <script>
        // Show the decision form and set the student's details
        function showDecisionForm(studentId, studentName, entryTerm, studentMajor) {
            document.getElementById('student-id').value = studentId;
            document.getElementById('student-name').textContent = studentName;
            document.getElementById('entry-term').value = entryTerm;
            document.getElementById('student-major').value = studentMajor;
            document.getElementById('decision-form').style.display = 'block';
        }

        // Hide the decision form
        function hideDecisionForm() {
            document.getElementById('decision-form').style.display = 'none';
        }
    </script>

</body>

</html>
