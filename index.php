<?php
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $db = new mysqli('localhost', 'root', '', 'logbook');

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Check user credentials using prepared statement
    $query = "SELECT id, username, password, userType FROM users WHERE username = ?";
    $stmt = $db->prepare($query);

    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashedPassword, $userType);
            $stmt->fetch();

            // Verify the entered password against the hashed password
            if (password_verify($password, $hashedPassword)) {
                // Store user information in the session
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['userType'] = $userType;

                // Redirect based on user type
                if ($userType === 'student') {
                    header('Location: studentPortal.php');
                    exit();
                } else {
                    header('Location: supervisorPortal.php');
                    exit();
                }
            } else {
                // Invalid password, handle accordingly
                echo "Invalid password";
            }
        } else {
            // User not found, handle accordingly
            echo "User not found";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle statement preparation error
        echo "Statement preparation error";
    }

    // Close the database connection
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Internship Log Book</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <form action="index.php" method="post">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="choice.php">Sign Up</a></p>
    </form>
</body>
</html>
