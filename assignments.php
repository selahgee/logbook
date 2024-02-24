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

// Get all assignment titles
$allAssignmentsQuery = "SELECT DISTINCT assignment_title FROM assignments";
$allAssignmentsResult = $db->query($allAssignmentsQuery);

// Check if the query was successful
if ($allAssignmentsResult) {
    // Fetch all assignment titles into an associative array
    $assignmentTitles = $allAssignmentsResult->fetch_all(MYSQLI_ASSOC);
    // Free the result set
    $allAssignmentsResult->free();
} else {
    die("Error fetching assignment titles: " . $db->error);
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Internship Log Book</title>
    <!-- Add any additional styling or Bootstrap if needed -->
    <link rel="stylesheet" type="text/css" href="portal.css">
    <style>
        .assignment-list {
            display: none;
        }
    </style>
    <script>
        function toggleAssignmentList(assignmentTitle) {
            var assignmentList = document.getElementById(assignmentTitle + '-assignments');
            if (assignmentList.style.display === 'none') {
                assignmentList.style.display = 'block';
            } else {
                assignmentList.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar.php'); ?>

    <div class="portal-container">
        <div class="dashboard">
            <h2>Welcome, <?php echo $student['username']; ?>!</h2>
            <h3>All Assignments</h3>

            <?php
            // Reconnect to the database to execute the next query
            $db = new mysqli('localhost', 'root', '', 'logbook');
            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }
            
            if ($assignmentTitles && count($assignmentTitles) > 0): 
                foreach ($assignmentTitles as $assignment): 
                    // Get assignments for the current assignment_title
                    $currentAssignmentTitle = $assignment['assignment_title'];
                    $assignmentsQuery = "SELECT * FROM assignments WHERE assignment_title = '$currentAssignmentTitle'";
                    $assignmentsResult = $db->query($assignmentsQuery);
            ?>

                    <h4 onclick="toggleAssignmentList('<?php echo $currentAssignmentTitle; ?>')">
                        <?php echo $currentAssignmentTitle; ?>
                    </h4>

                    <div id="<?php echo $currentAssignmentTitle; ?>-assignments" class="assignment-list">
                        <?php if ($assignmentsResult && $assignmentsResult->num_rows > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Assignment Title</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($individualAssignment = $assignmentsResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $individualAssignment['assignment_title']; ?></td>
                                            <td>
                                                <a href="view_assignment.php?assignment_id=<?php echo $individualAssignment['assignment_id']; ?>">
                                                    View Assignment
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No assignments for <?php echo $currentAssignmentTitle; ?>.</p>
                        <?php endif; ?>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No assignments available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
