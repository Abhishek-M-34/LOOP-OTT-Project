<?php
// Database connection details
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Establish database connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    // Return an error response if the database connection fails
    http_response_code(500);
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the POST request
    $videoId = $_POST['videoId'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    try {
        // Prepare the SQL statement to update the video details
        $stmt = $conn->prepare("UPDATE links SET title = ?, description = ? WHERE id = ?");
        
        // Bind the parameters: "ssi" means string, string, integer
        $stmt->bind_param("ssi", $title, $description, $videoId);

        // Execute the update query
        if ($stmt->execute()) {
            // Return a success response
            echo "Video details updated successfully.";
        } else {
            // Return an error response if the update fails
            http_response_code(500);
            echo "Error updating video details: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Return an error response if the update query fails
        http_response_code(500);
        echo "Error: " . $e->getMessage();
    }
} else {
    // Return an error response if the request method is not POST
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method. Only POST requests are allowed.";
}

// Close the database connection
$conn->close();
?>
