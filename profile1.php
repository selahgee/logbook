<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'supervisor') {
    header('Location: index.php'); // Redirect to login page if not logged in as a supervisor
    exit();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'logbook');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get student information from the database
$username = $_SESSION['username'];
$query = "SELECT * FROM supervisor WHERE username = '$username'";
$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    $supervisor = $result->fetch_assoc();
} else {
    die("Error: Supervisor not found");
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Profile - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="portal.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar1.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $supervisor['name']; ?>!</h2>
            <h2><u>Supervisor Profile</u></h2>
            <p><strong>Username:</strong> <?php echo $supervisor['username']; ?></p>            
            <p><strong>Name:</strong> <?php echo $supervisor['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $supervisor['email']; ?></p>
            <p><strong>Institution:</strong> <?php echo $supervisor['institution']; ?></p>
            <p><strong>Phone Number:</strong> <?php echo $supervisor['phone_number']; ?></p>
            <p><strong>Job Rank:</strong> <?php echo $supervisor['job_rank']; ?></p>
        </div>
    </div>
</body>
</html>
