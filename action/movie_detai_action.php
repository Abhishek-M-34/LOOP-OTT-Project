<?php
// Connect to your database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "ott";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch movie details based on the movie ID
if (isset($_GET['Id'])) {
    $Id = $_GET['Id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM links WHERE id = ?");
    $stmt->bind_param("i", $Id); // 'i' indicates the parameter is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data of each row
        $row = $result->fetch_assoc();
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>Director: " . htmlspecialchars($row['director']) . "</p>";
        echo "<p>Release Year: " . htmlspecialchars($row['release_year']) . "</p>";
    } else {
        echo "Movie not found";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
