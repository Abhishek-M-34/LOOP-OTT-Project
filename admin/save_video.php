<?php
# Created by Amish
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

// Check if required POST data is received
if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description'])) {
    $videoId = $_POST['id'];
    $newTitle = $_POST['title'];
    $newDescription = $_POST['description'];

    // Prepare an update statement
    $stmt = $mysqli->prepare("UPDATE links SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $newTitle, $newDescription, $videoId); // 'ssi' -> string, string, integer

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Video updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating video."]);
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request. Missing parameters."]);
}

// Close database connection
$mysqli->close();
?>
