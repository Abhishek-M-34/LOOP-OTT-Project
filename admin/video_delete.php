<?php
session_start();
// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php"); // Redirect to your login page
    exit();
}

// Database connection details
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Establish MySQL database connection using mysqli
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to delete video and associated slideshow images
function deleteVideo($mysqli, $videoId)
{
    // Start a transaction
    $mysqli->begin_transaction();

    try {
        // Delete associated records from slideshow_images table
        $stmtImages = $mysqli->prepare("DELETE FROM slideshow_images WHERE link_id = ?");
        $stmtImages->bind_param("i", $videoId); // 'i' for integer type
        $stmtImages->execute();

        // Delete the video record from the links table
        $stmt = $mysqli->prepare("DELETE FROM links WHERE id = ?");
        $stmt->bind_param("i", $videoId); // 'i' for integer type
        $stmt->execute();

        // Commit the transaction
        $mysqli->commit();

        return true;
    } catch (mysqli_sql_exception $e) {
        // Rollback the transaction on error
        $mysqli->rollback();
        return false;
    }
}

// Check if videoId is provided via POST request
if (isset($_POST['videoId'])) {
    $videoId = $_POST['videoId'];

    // Call the deleteVideo function and handle the result
    $result = deleteVideo($mysqli, $videoId);
    if ($result) {
        echo "Video deleted successfully.";
    } else {
        echo "Error deleting video.";
    }

    // Redirect to video_management.php after deletion
    header("Location: video_management.php");
    exit();
} else {
    echo "Invalid request.";
}

// Close the database connection
$mysqli->close();
?>
