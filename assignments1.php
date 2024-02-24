<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'supervisor') {
    header('Location: index.php'); // Redirect to login page if not logged in as a supervisor
    exit();
}

// Database connection (update with your database credentials)
$db = new mysqli('localhost', 'root', '','logbook');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_assignment'])) {
        // Handle assignment creation
        $assignment_title = $_POST['assignment_title'];
        $assignment_description = $_POST['assignment_description'];
        $submission_date = $_POST['submission_date'];

        $insertQuery = "INSERT INTO assignments (assignment_title, assignment_description, submission_date, status) 
                        VALUES ('$assignment_title', '$assignment_description', '$submission_date', 'pending')";
        
        if ($db->query($insertQuery)) {
            echo "Assignment created successfully.";
        } else {
            echo "Error creating assignment: " . $db->error;
        }
    } elseif (isset($_POST['update_assignment'])) {
        // Handle assignment update
        $assignment_id = $_POST['assignment_id'];
        $assignment_title = $_POST['assignment_title'];
        $assignment_description = $_POST['assignment_description'];
        $submission_date = $_POST['submission_date'];

        $updateQuery = "UPDATE assignments 
                        SET assignment_title = '$assignment_title', assignment_description = '$assignment_description', 
                            submission_date = '$submission_date'
                        WHERE assignment_id = $assignment_id";

        if ($db->query($updateQuery)) {
            echo "Assignment updated successfully.";
        } else {
            echo "Error updating assignment: " . $db->error;
        }
    } elseif (isset($_POST['delete_assignment'])) {
        // Handle assignment deletion
        $assignment_id = $_POST['assignment_id'];

        $deleteQuery = "DELETE FROM assignments WHERE assignment_id = $assignment_id";

        if ($db->query($deleteQuery)) {
            echo "Assignment deleted successfully.";
        } else {
            echo "Error deleting assignment: " . $db->error;
        }
    }
}

// Fetch existing assignments
$selectQuery = "SELECT * FROM assignments";
$result = $db->query($selectQuery);

if (!$result) {
    die("Error fetching assignments: " . $db->error);
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Assignments - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="supervisor.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar1.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
            <h3>Assignment Management</h3>

            <!-- Assignment Creation Form -->
            <form action="" method="post">
                <h4>Create Assignment</h4>
                <label for="assignment_title">Assignment Title:</label>
                <input type="text" name="assignment_title" required>
                <label for="assignment_description">Assignment Description:</label>
                <textarea name="assignment_description" required></textarea>
                <label for="submission_date">Submission Date:</label>
                <input type="date" name="submission_date" required>
                <button type="submit" name="create_assignment">Create Assignment</button>
            </form>

            <!-- Assignment List for Update/Delete -->
            <h4>Existing Assignments</h4>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Submission Date</th>
                        <th>submitted By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($assignment = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $assignment['assignment_title']; ?></td>
                            <td><?php echo $assignment['assignment_description']; ?></td>
                            <td><?php echo $assignment['submission_date']; ?></td>
                            <td><?php echo $assignment ['submitted_by'];?></td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                    <button type="submit" name="update_assignment">Update</button>
                                    <button type="submit" name="delete_assignment">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
