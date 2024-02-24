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

$pendingAssignmentsQuery = "SELECT * FROM assignments WHERE status = 'pending'";
$pendingAssignmentsResult = $db->query($pendingAssignmentsQuery);

// Get completed assignments
$completedAssignmentsQuery = "SELECT * FROM assignments WHERE status = 'completed'";
$completedAssignmentsResult = $db->query($completedAssignmentsQuery);

// Close the database connection
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="portal.css">
</head>
<body>
    <?php include ('C:\xampp\htdocs\php\navbar.php'); 
     $date = date('2024-03-15');
     $time = date('17:00:00');
     $date_today = $date . ' ' . $time;
    ?>

    <div class="portal-container">
        <header class="welcome-header">
            <h2>Welcome, <?php echo $student['username']; ?>!</h2>
            
    <script type="text/javascript">
        // ... Countdown script remains unchanged
         // setting the date am counting to
         var count_id = "<?php echo $date_today; ?>";
                var countDownDate = new Date(count_id).getTime();
                // set the count down to every second
                var x = setInterval(function () {
                    // get today's date
                    var now = new Date().getTime();
                    // distance between now and the countdown
                    var distance = countDownDate - now;
                    // calculation for days, hours, minutes and seconds
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24)); 
                    var hours = Math.floor((distance % (1000 * 60 * 60*24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("demo").innerHTML = days + "d" + hours + "h" + minutes + "m" + seconds + "s";
                }, 1000);
    </script>
            <p><h3>Internship Countdown</h3></p>
            <p id="demo" style="font-size:30px;"></p>
        </header>

        <div class="dashboard">
            <h3>Assignment Details</h3>  
            <div class="dashboard-left">          

            <?php if (isset($pendingAssignmentsResult) && $pendingAssignmentsResult->num_rows > 0): ?>
                <h4>Pending Assignments</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pendingAssignment = $pendingAssignmentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $pendingAssignment['assignment_title']; ?></td>
                                <td>Pending</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No Pending assignments.</p>
            <?php endif; ?>            
            </div>
            <div class= "dashboard-right">

            <?php if (isset($completedAssignmentsResult) && $completedAssignmentsResult->num_rows > 0): ?>
                <h4>Completed Assignments</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($completedAssignment = $completedAssignmentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $completedAssignment['assignment_title']; ?></td>
                                <td>Completed</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No completed assignments.</p>
            <?php endif; ?>
            </div>
            <div class= "dashboard-total">

            <table>
                <thead>
                    <tr>
                        <th>Total Pending</th>
                        <th>Total Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $pendingAssignmentsResult->num_rows ?? 0; ?></td>
                        <td><?php echo $completedAssignmentsResult->num_rows ?? 0; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</body>
</html>
