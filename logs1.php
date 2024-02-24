<?php
session_start();

// Check if the user is logged in as a supervisor
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'supervisor') {
    header('Location: index.php'); // Redirect to the login page if not logged in as a supervisor
    exit();
}

// Database connection (update with your database credentials)
$db = new mysqli('localhost', 'root', '', 'logbook');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_log'])) {
        // Handle log creation
        $assignment_id = $_POST['assignment_id'];
        $comment = $_POST['comment'];
        $grade = $_POST['grade'];
        $submitted_by = $_POST['submitted_by'];

        $insertQuery = "INSERT INTO logs (assignment_id, supervisor_username, comment, grade, submitted_by) 
                        VALUES ('$assignment_id', '{$_SESSION['username']}', '$comment', '$grade', '$submitted_by')";

        if ($db->query($insertQuery)) {
            echo "Log created successfully.";
        } else {
            echo "Error creating log: " . $db->error;
        }
    } elseif (isset($_POST['download_submission'])) {
        // Handle file download
        $assignment_id = $_POST['assignment_id'];

        $selectAssignmentQuery = "SELECT * FROM assignments WHERE assignment_id = '$assignment_id'";
        $resultAssignment = $db->query($selectAssignmentQuery);

        if ($resultAssignment && $resultAssignment->num_rows > 0) {
            $assignment = $resultAssignment->fetch_assoc();
            $file_path = $assignment['file_path'];
            $submitted_by = $assignment['submitted_by'];

            // Display student's name and initiate download
            echo "Download Submission submitted by: $submitted_by";
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            readfile($file_path);
            exit();
        } else {
            echo "Error fetching assignment details: " . $db->error;
        }
    }
}

// Fetch existing assignment titles
$selectAssignmentsQuery = "SELECT assignment_id, assignment_title, submitted_by FROM assignments";
$resultAssignments = $db->query($selectAssignmentsQuery);

// Fetch existing logs for the supervisor
$selectLogsQuery = "SELECT logs.*, assignments.assignment_title FROM logs
                    INNER JOIN assignments ON logs.assignment_id = assignments.assignment_id
                    WHERE logs.supervisor_username = '{$_SESSION['username']}'";
$resultLogs = $db->query($selectLogsQuery);

if (!$resultAssignments || !$resultLogs) {
    die("Error fetching data: " . $db->error);
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Logs - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="supervisor.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar1.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
            <h3>Log Management</h3>

            <!-- Log Creation Form -->
            <form action="" method="post">
                <h4>Create Log</h4>
                <label for="assignment_id">Assignment:</label>
                <select name="assignment_id" required>
                    <?php while ($assignment = $resultAssignments->fetch_assoc()): ?>
                        <option value="<?php echo $assignment['assignment_id']; ?>"><?php echo $assignment['assignment_title']; ?></option>
                    <?php endwhile; ?>
                </select>

                <!-- Submitted By dropdown -->
                <label for="submitted_by">Submitted By:</label>
                <select name="submitted_by" required>
                    <?php
                    // Resetting the data seek to reuse the loop
                    $resultAssignments->data_seek(0);
                    while ($assignment = $resultAssignments->fetch_assoc()):
                    ?>
                        <option value="<?php echo $assignment['submitted_by']; ?>"><?php echo $assignment['submitted_by']; ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="comment">Comment:</label>
                <textarea name="comment" required></textarea>
                <label for="grade">Grade:</label>
                <input type="text" name="grade" required>
                <button type="submit" name="create_log">Create Log</button>
            </form>

            <!-- Download Submission Form -->
            <form action="" method="post">
                <h4>Download Submission</h4>
                <label for="assignment_id_download">Assignment:</label>
                <select name="assignment_id" required>
                    <?php
                    // Resetting the data seek to reuse the loop
                    $resultAssignments->data_seek(0);
                    while ($assignment = $resultAssignments->fetch_assoc()):
                    ?>
                        <option value="<?php echo $assignment['assignment_id']; ?>"><?php echo $assignment['assignment_title']; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="download_submission">Download Submission</button>
            </form>

            <!-- Existing Logs -->
            <h4>Existing Logs</h4>
            <table>
                <thead>
                    <tr>
                        <th>Assignment</th>                        
                        <th>Supervisor Comment</th>
                        <th>Grade</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $resultLogs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $log['assignment_title']; ?></td>                            
                            <td><?php echo $log['comment']; ?></td>
                            <td><?php echo $log['grade']; ?></td>
                            <td><?php echo $log['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

