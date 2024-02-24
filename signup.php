<?php
session_start();

// Include the validation file
require_once('validation.php');

// Check if a role is specified in the URL
if ($_GET['role'] !== 'student' && $_GET['role'] !== 'supervisor') {
    header('Location: choice.php'); // Redirect to the role selection page
    exit();
}

// Assuming the form is submitted
if (isset($_POST['submit'])) {
    // Validate form data
    $errors = validation($_POST);

    if (empty($errors)) {
        // Form data is valid, proceed with signup
        $newUsername = $_POST['username'];
        $newEmail = $_POST['email'];
        $newPassword = $_POST['password'];
        $userType = $_GET['role'];

        // Hash the password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Establish database connection
        $db = new mysqli('localhost', 'root', '', 'logbook');

        // Check for a successful connection
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        // Perform signup for users table
        $query = "INSERT INTO users (username, email, password, confirmpassword, userType) 
                  VALUES ('$newUsername', '$newEmail', '$hashedPassword', '$hashedPassword', '$userType')";
        $result = $db->query($query);

        if ($result) {
            // Get the last inserted user ID
            $lastUserID = $db->insert_id;

            // If the role is student, insert details into students table
            if ($_GET['role'] === 'student') {
                $studentNo = $_POST['student_id'];
                $username = $_POST['username'];
                $email= $_POST['email'];
                $learning_institution = $_POST['learning_institution'];
                $phone_number = $_POST['phone_number'];
                $course = $_POST['course'];

                // Assuming 'students' table has a 'user_id' column
                $insertStudentQuery = "INSERT INTO students (user_id, student_id, username,email,learning_institution,phone_number,course) VALUES ('$lastUserID', '$studentNo', '$username','$email','$learning_institution','$phone_number','$course')";
                $resultStudent = $db->query($insertStudentQuery);

                if (!$resultStudent) {
                    echo "Error inserting student details: " . $db->error;
                }
            } elseif ($_GET['role'] === 'supervisor') {
                $supervisorId = $_POST['Supervisorid'];
                $institution = $_POST['institution'];
                $phoneNumber = $_POST['phone_number'];
                $jobRank = $_POST['job_rank'];

                // Assuming 'supervisors' table has a 'user_id' column
                $insertSupervisorQuery = "INSERT INTO supervisors (user_id, supervisor_id, username, email, institution, phone_number, job_rank) 
                                          VALUES ('$lastUserID', '$supervisorId', '$newUsername', '$newEmail', '$institution', '$phoneNumber', '$jobRank')";
                $resultSupervisor = $db->query($insertSupervisorQuery);

                if (!$resultSupervisor) {
                    echo "Error inserting supervisor details: " . $db->error;
                }
            }

            // Redirect to login page after successful signup
            header('Location: index.php');
            exit();
        } else {
            echo "Error inserting user details: " . $db->error;
        }

        // Close the database connection
        $db->close();
    } else {
        // Form data is not valid, handle errors (e.g., display error messages)
        print_r($errors); // You might want to handle errors in a more user-friendly way
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Internship Log Book</title>
</head>
<body>
    <form action="signup.php?role=<?= $_GET['role'] ?>" method="post">
        <h2>Signup</h2>

        <!-- Additional fields based on user type -->
        <?php if ($_GET['role'] === 'student'): ?>
        <label for="student_id">Student ID:</label>
        <input type="text" id="student_id" name="student_id" required>
        <label for="learning_institution">Learning Institution:</label>
        <input type="text" id="learning_institution" name="learning_institution" required>
        <label for="course">Course:</label>
        <input type="text" id="course" name="course" required>
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>
        <br>
        <?php elseif ($_GET['role'] === 'supervisor'): ?>
        <label for="Supervisorid">Supervisor ID:</label>
        <input type="text" id="Supervisorid" name="Supervisorid" required>
        <br>
        <label for="institution">Institution:</label>
        <input type="text" id="institution" name="institution" required>
        <br>
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>
        <br>
        <label for="job_rank">Job Rank:</label>
        <input type="text" id="job_rank" name="job_rank" required>
        <br>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
        <br>
        <p>Already have an account? <a href="index.php">Log in</a></p>
        <button type="submit" name="submit">Sign Up</button>
    </form>
</body>
</html>
