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

// Establish database connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if required parameters are set
if (isset($_POST['serieId'], $_POST['title'], $_POST['description'])) {
    $serieId = $_POST['serieId'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Prepare and execute the query to update series details
    $stmt = $conn->prepare("UPDATE series SET title = ?, description = ? WHERE series_id = ?");
    $stmt->bind_param("ssi", $title, $description, $serieId); // "ssi" means string, string, integer

    try {
        if ($stmt->execute()) {
            echo "Series details updated successfully.";
        } else {
            echo "Error updating series details: " . $stmt->error;
        }
    } catch (Exception $e) {
        echo "Error updating series details: " . $e->getMessage();
    }

    $stmt->close(); // Close the statement
} else {
    echo "Required parameters are missing.";
}

$conn->close(); // Close the connection
?>
