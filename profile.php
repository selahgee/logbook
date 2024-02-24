<?php
session_start();

// Check if the user is logged in
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
$query = "SELECT * FROM students WHERE username = '$username'";
$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    die("Error: Student not found");
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="portal.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $student['username']; ?>!</h2>
            <h2><u>Student Profile</u></h2>
            <p><strong>Username:</strong> <?php echo $student['username']; ?></p>
            <p><strong>Reg No:</strong> <?php echo $student['student_id']; ?></p>
            <p><strong>Name:</strong> <?php echo $student['username']; ?></p>
            <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
            <p><strong>Learning Institution:</strong> <?php echo $student['learning_institution']; ?></p>
            <p><strong>Phone Number:</strong> <?php echo $student['phone_number']; ?></p>
            <p><strong>Course:</strong> <?php echo $student['course']; ?></p>
        </div>
    </div>
</body>
</html>
