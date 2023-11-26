<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['rollNumber'])) {
    // If not logged in, redirect to the login page
    header("Location: ../index.php");
    exit();
}

// Retrieve user information or perform any additional actions based on the session data

// You can fetch additional user information from the database using the roll number if needed

// Example: Get user's roll number from the session
$rollNumber = $_SESSION['rollNumber'];

// Include any additional logic or fetch more user details as needed

// Now you can display a welcome message or any other content
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <!-- Include any additional styles or scripts as needed -->
</head>

<body>
    <header>
        <h1>Wassup?</h1>
    </header>

    <main>
        <p><?php echo $rollNumber; ?>! Hi mate</p>
        <!-- Include any additional content or features for the logged-in user -->
    </main>

    <footer>
        <!-- Include any footer content or links as needed -->
    </footer>
</body>

</html>
