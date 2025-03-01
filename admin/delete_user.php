<?php
# Edited by Amish
session_start();

// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php"); // Redirect to login page
    exit();
}

// Database connection details
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

try {
    // Establish a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions for error handling
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if userId is provided in the request
if (isset($_POST['userId'])) {
    // Sanitize the input
    $userId = intval($_POST['userId']); // Use intval for better input sanitization (expects an integer)

    try {
        // Prepare the DELETE statement
        $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT); // Bind the parameter as an integer

        // Execute the statement
        $stmt->execute();

        // Check if a row was deleted
        if ($stmt->rowCount() > 0) {
            echo "User deleted successfully";
        } else {
            echo "Failed to delete user: User not found";
        }
    } catch (PDOException $e) {
        // Catch and display any errors
        echo "Failed to delete user: " . $e->getMessage();
    }
} else {
    echo "User ID not provided";
}
?>
