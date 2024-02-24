<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'supervisor') {
    header('Location: index.php'); // Redirect to login page if not logged in as a supervisor
    exit();
}

// Database connection (update with your database credentials)
$db = new mysqli('localhost', 'root', '', 'logbook');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get supervisor information from the database
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' AND userType = 'supervisor'";
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
    <title>Supervisor Dashboard - Internship Log Book</title>
    <!-- Add any additional styling or Bootstrap if needed -->
    <link rel="stylesheet" type="text/css" href="portal.css">
    <style>
        /* Center the welcome banner */
        .welcome-header {
            text-align: center;
        }

        /* Add some padding to the countdown */
        #demo {
            padding: 20px;
            font-size: 30px;
        }
    </style>
    
</head>
<body>

    <?php include ('C:\xampp\htdocs\php\navbar1.php'); 
     $date = date('2024-03-15');
     $time = date('17:00:00');
     $date_today = $date . ' ' . $time;
    ?>

    <div class="portal-container">
        <header class="welcome-header">
            <h2>Welcome, <?php echo $supervisor['username']; ?>!</h2>
            <script type="text/javascript">
        // Countdown script
        var count_id = "<?php echo $date_today; ?>";
        var countDownDate = new Date(count_id).getTime();
        
        var x = setInterval(function () {
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("demo").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        }, 1000);
    </script>
            <h3>Internship Countdown</h3>
            <p id="demo" style="font-size:30px;"></p>
        </header>

      

    </div>
</body>
</html>
