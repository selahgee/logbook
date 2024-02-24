<?php
session_start();

// Check if the user is logged in as a student
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'student') {
    header('Location: index.php'); // Redirect to login page if not logged in as a student
    exit();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'logbook');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get student information from the database
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' AND userType = 'student'";
$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    die("Error: Student not found");
}

// Get logs for the current student
$studentId = $student['id'];  // Assuming 'id' is the primary key in the 'users' table
$logsQuery = "SELECT logs.*, assignments.assignment_title
              FROM logs
              JOIN assignments ON logs.assignment_id = assignments.assignment_id
              WHERE logs.student_id = '$studentId'
              ORDER BY logs.created_at DESC";
$logsResult = $db->query($logsQuery);

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="portal.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $student['username']; ?>!</h2>
            <h3>View Logs</h3>

            <?php if ($logsResult && $logsResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Assignment Title</th>
                            <th>Supervisor Comment</th>
                            <th>Grade</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = $logsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $log['assignment_title']; ?></td>
                                <td><?php echo $log['comment']; ?></td>
                                <td><?php echo $log['grade']; ?></td>
                                <td><?php echo $log['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No logs available for this student.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
