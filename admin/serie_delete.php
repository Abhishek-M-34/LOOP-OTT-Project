<?php
# Edited by Amish
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

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Function to delete series, seasons, and episodes
function deleteSeries($conn, $serieId)
{
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete episodes belonging to seasons of the series
        $stmt = $conn->prepare("DELETE episodes FROM episodes 
                                JOIN seasons ON episodes.season_id = seasons.id 
                                WHERE seasons.series_id = ?");
        $stmt->bind_param("i", $serieId);
        $stmt->execute();
        $stmt->close();

        // Delete seasons of the series
        $stmt = $conn->prepare("DELETE FROM seasons WHERE series_id = ?");
        $stmt->bind_param("i", $serieId);
        $stmt->execute();
        $stmt->close();

        // Delete the series itself
        $stmt = $conn->prepare("DELETE FROM series WHERE series_id = ?");
        $stmt->bind_param("i", $serieId);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Return success message or indication
        return true;
    } catch (Exception $e) {
        // Something went wrong, rollback the transaction
        $conn->rollback();

        // Return error message or indication
        return false;
    }
}

// Check if serieId is provided via POST request
if (isset($_POST['serieId'])) {
    $serieId = intval($_POST['serieId']);

    // Call the deleteSeries function and handle the result
    $result = deleteSeries($conn, $serieId);
    if ($result) {
        echo "Series deleted successfully.";
    } else {
        echo "Error deleting series.";
    }
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
