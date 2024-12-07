<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in as admin, otherwise redirect
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to admin login if not logged in
    exit();
}

// Fetch all students or apply search if a search term is provided
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

try {
    if ($searchTerm) {
        // Search query to filter by full name, email, or phone
        $stmt = $conn->prepare("SELECT * FROM students WHERE full_name LIKE :search OR email LIKE :search OR phone LIKE :search");
        $stmt->execute([':search' => "%$searchTerm%"]);
    } else {
        // Fetch all students if no search term is provided
        $stmt = $conn->prepare("SELECT * FROM students");
        $stmt->execute();
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Students</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/admin.css">
    <script>
        // Print function for printing the student list
        function printTable() {
            var content = document.getElementById('students-table').outerHTML;
            var printWindow = window.open('', '', 'height=400,width=800');
            printWindow.document.write('<html><head><title>Student List</title>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</head>

<body>

    <div class="admin-container">
        <h2>Student List</h2>
        <form method="POST" action="">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by name, email, or phone" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit">Search</button>
            </div>
        </form>

        <button onclick="printTable()">Print</button>

        <table id="students-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Payment Reference</th>
                    <th>Created At</th>
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
                            <td><?= htmlspecialchars($student['student_type']) ?></td>
                            <td><?= htmlspecialchars($student['payment_reference']) ?></td>
                            <td><?= htmlspecialchars($student['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No students found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>
