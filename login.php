<?php
# Edited by Amish
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // Change if needed
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "hack"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $vendor_name = isset($_POST['vendername']) ? $_POST['vendername'] : "";
    $shop_name = isset($_POST['shopname']) ? $_POST['shopname'] : "";
    $rating = isset($_POST['rating']) ? $_POST['rating'] : "";
    $items = isset($_POST['items']) ? implode(", ", $_POST['items']) : "";

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO vendor (vendor_name, shop_name, items, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $vendor_name, $shop_name, $items, $rating);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record inserted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid Request!";
}
?>
