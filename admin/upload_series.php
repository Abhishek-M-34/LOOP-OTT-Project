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

// Establish database connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Function to upload series data to the database
function uploadSeries($conn, $title, $description, $imageFile, $seasons, $currentStatus)
{
    // Get current date
    $uploadingDate = date("Y-m-d");

    // Remove '../' prefix from image file path
    $imageFile = str_replace('../', '', $imageFile);

    // Prepare SQL statement to insert series data
    $stmt = $conn->prepare("INSERT INTO series (title, description, image_path, current_status, uploading_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $imageFile, $currentStatus, $uploadingDate);

    // Execute the insert statement
    $stmt->execute();

    // Get the ID of the last inserted series
    $seriesId = $stmt->insert_id;

    // Iterate through each season
    for ($i = 1; $i <= $seasons; $i++) {
        $seasonNum = $i;
        $numEpisodes = $_POST['season' . $i . 'Episodes'];

        // Prepare SQL statement to insert season data
        $stmt = $conn->prepare("INSERT INTO seasons (series_id, season_number, num_episodes) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $seriesId, $seasonNum, $numEpisodes);
        $stmt->execute();

        // Get the ID of the last inserted season
        $seasonId = $stmt->insert_id;

        // Iterate through each episode in the season
        for ($j = 1; $j <= $numEpisodes; $j++) {
            $episodeNum = $j;
            $fileKey = 'season' . $i . 'Episode' . $j;
            $fileName = $_FILES[$fileKey]['name'];
            $fileTmpName = $_FILES[$fileKey]['tmp_name'];
            $fileError = $_FILES[$fileKey]['error'];

            // Handle file upload and insert episode data into database
            if ($fileError === 0) {
                $fileDestination = '../uploads/' . $fileName;
                move_uploaded_file($fileTmpName, $fileDestination);

                // Remove '../' prefix from file destination
                $fileDestination = str_replace('../', '', $fileDestination);

                // Prepare SQL statement to insert episode data
                $stmt = $conn->prepare("INSERT INTO episodes (season_id, episode_number, file_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $seasonId, $episodeNum, $fileDestination);
                $stmt->execute();
            }
        }
    }

    // Redirect to a success page or display a success message
    header("Location: series_upload_success.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['seriesTitle'];
    $description = $_POST['description'];
    $imageFile = $_FILES['imageFile']['name'];
    $seasons = $_POST['seasons'];
    $currentStatus = $_POST['currentStatus'];

    // File upload path
    $targetDir = "../uploads/";
    $targetFilePath = $targetDir . basename($imageFile);

    // Upload series image
    move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFilePath);

    // Call the uploadSeries function to insert data into the database
    uploadSeries($conn, $title, $description, $targetFilePath, $seasons, $currentStatus);

    // Redirect to success page
    header("Location: success.php");
    exit();
}
?>
