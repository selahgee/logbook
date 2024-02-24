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
$query = "SELECT * FROM users WHERE username = '$username' AND userType = 'student'";
$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    die("Error: Student not found");
}

// Get assignment details
if (isset($_GET['assignment_id'])) {
    $assignmentId = $_GET['assignment_id'];
    $assignmentQuery = "SELECT * FROM assignments WHERE assignment_id = $assignmentId";
    $assignmentResult = $db->query($assignmentQuery);

    if ($assignmentResult && $assignmentResult->num_rows > 0) {
        $assignment = $assignmentResult->fetch_assoc();
    } else {
        die("Error: Assignment not found");
    }
} else {
    die("Error: Assignment ID not provided");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment_file'])) {
    // Add submitted_by based on the current session user
    $submittedBy = $student['username'];

    $file = $_FILES['assignment_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Check if the file was uploaded without errors
    if ($fileError === UPLOAD_ERR_OK) {
        // Define the upload directory
        $uploadDir = 'uploads/';

        // Ensure that the directory exists; create it if not
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique name for the file
        $uniqueFileName = uniqid('assignment_' . $assignmentId . '_') . '_' . $fileName;

        // Construct the full file path
        $filePath = $uploadDir . $uniqueFileName;

        // Move the uploaded file to the specified directory
        move_uploaded_file($fileTmpName, $filePath);

        // Update the assignment record with the file path, status, and update date
        $updateQuery = "UPDATE assignments SET status = 'completed', submission_date = NOW(), updated_at = NOW(), file_path = '$filePath', submitted_by = '$submittedBy' WHERE assignment_id = $assignmentId";
        $db->query($updateQuery);

        // Redirect to the assignment page
        header("Location: assignments.php?assignment_id=$assignmentId");
        exit();
    } else {
        echo "Error uploading file.";
    }
}

// Close the database connection
$db->close();
?>

<!-- Rest of the HTML code remains unchanged -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignment - Internship Log Book</title>
    <!-- Add any additional styling or Bootstrap if needed -->
    <link rel="stylesheet" type="text/css" href="portal.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $student['username']; ?>!</h2>
            <h3>Assignment Details</h3>
            <p><strong>Title:</strong> <?php echo $assignment['assignment_title']; ?></p>
            <p><strong>Description:</strong> <?php echo $assignment['assignment_description']; ?></p>
            <p><strong>Submitted By:</strong> <?php echo $assignment['submitted_by']; ?></p>
            <p><strong>Submission Date:</strong> <?php echo $assignment['submission_date']; ?></p>
            <p><strong>Update Date:</strong> <?php echo $assignment['updated_at']; ?></p>

            <?php if ($assignment['status'] === 'pending'): ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="assignment_file"><strong>Upload File:</strong></label>
                    <input type="file" name="assignment_file" id="assignment_file" required>
                    <button type="submit" name="submit">Submit Assignment</button>
                </form>
            <?php endif; ?>

            <!--<?php if ($assignment['file_path']): ?>
               <p><a href="<?php echo $assignment['file_path']; ?>" download>Download Assignment</a></p>
            <?php endif; ?>-->
        </div>
    </div>
</body>
</html>
