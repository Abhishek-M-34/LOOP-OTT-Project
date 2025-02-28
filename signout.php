<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_SESSION['user_email'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the home page or any other desired page after sign-out
    header("Location: index.php"); // Redirect to the home page
    exit();
} else {
    // If the user is not logged in, you can provide a message or perform a different action
    // For example, redirecting to the home page with a message
    header("Location: home.php?message=You are already logged out."); // Redirect with a message
    exit();
}
?>
