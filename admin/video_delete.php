<?php
# Edited by Amish
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

// Database connection details
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Establish MySQL database connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $mysqli->connect_error]));
}

// Check if the video ID is provided via POST
if (isset($_POST['id'])) {
    $videoId = $_POST['id'];

    // Start transaction to ensure consistency
    $mysqli->begin_transaction();

    try {
        // Delete associated records from slideshow_images (if applicable)
        $stmtImages = $mysqli->prepare("DELETE FROM slideshow_images WHERE link_id = ?");
        $stmtImages->bind_param("i", $videoId);
        $stmtImages->execute();
        $stmtImages->close();

        // Delete the video from links table
        $stmt = $mysqli->prepare("DELETE FROM links WHERE id = ?");
        $stmt->bind_param("i", $videoId);
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            $mysqli->commit();
            echo json_encode(["status" => "success", "message" => "Video deleted successfully."]);
        } else {
            throw new Exception("Video not found or already deleted.");
        }

        $stmt->close();
    } catch (Exception $e) {
        $mysqli->rollback();
        echo json_encode(["status" => "error", "message" => "Error deleting video: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request. Video ID is required."]);
}

// Close database connection
$mysqli->close();
?>
