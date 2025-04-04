<?php
# Edited by Amish
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Replace these values with your actual database credentials
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

// Function to generate unique file name
function generateUniqueFileName($fileName) {
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $basename = pathinfo($fileName, PATHINFO_FILENAME);
    return $basename . '_' . uniqid() . '.' . $extension;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contentTitle = $_POST["contentTitle"]; // Fetch the title from the form
    $uploadingDate = $_POST["uploadingDate"]; // Fetch the uploading date from the form
    $description = $_POST["description"]; // Fetch the description from the form
    $currentStatus = $_POST["currentStatus"]; // Fetch the current status from the form

    // Initialize variables for file paths
    $videoPath = "";
    $imagePath = "";
    $slideshowImagePaths = array();

    // Process video upload
    if ($currentStatus !== "upcoming") {
        $videoFile = $_FILES["videoFile"];
        if ($videoFile["error"] === UPLOAD_ERR_OK) {
            $uploadDir = "uploads/";
            $videoFileName = generateUniqueFileName($videoFile["name"]);
            $videoPath = $uploadDir . $videoFileName;
            if (!move_uploaded_file($videoFile["tmp_name"], $videoPath)) {
                die("Error uploading video file");
            }
        } else {
            die("Error uploading video file: " . $videoFile["error"]);
        }
    }

    // Process image upload
    $imageFile = $_FILES["imageFile"];
    if ($imageFile["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $imageFileName = generateUniqueFileName($imageFile["name"]);
        $imagePath = $uploadDir . $imageFileName;
        if (!move_uploaded_file($imageFile["tmp_name"], $imagePath)) {
            die("Error uploading image file");
        }
    } else {
        die("Error uploading image file: " . $imageFile["error"]);
    }

    // Process slideshow image upload
    if ($currentStatus !== "upcoming") {
        $slideshowImages = $_FILES["slideshowImages"];
        if (!empty($slideshowImages['name'][0])) {
            foreach ($slideshowImages['name'] as $key => $imageName) {
                $uploadDir = "uploads/";
                $slideshowImageFileName = generateUniqueFileName($imageName);
                $slideshowImagePath = $uploadDir . $slideshowImageFileName;
                $slideshowImagePaths[] = $slideshowImagePath;
                if (!move_uploaded_file($slideshowImages['tmp_name'][$key], $slideshowImagePath)) {
                    die("Error uploading slideshow image: " . $imageName);
                }
            }
        }
    }

    // Insert data into the database using mysqli
    try {
        // Prepare the query to insert into links table
        $stmt = $conn->prepare("INSERT INTO links (video_link, image_link, title, uploading_date, description, current_status) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $videoPath, $imagePath, $contentTitle, $uploadingDate, $description, $currentStatus);
        $stmt->execute();

        // Get the last inserted ID
        $lastInsertId = $stmt->insert_id;

        // Insert slideshow image paths into the database
        if (!empty($slideshowImagePaths)) {
            foreach ($slideshowImagePaths as $slideshowImagePath) {
                $stmt = $conn->prepare("INSERT INTO slideshow_images (link_id, slideshow_image_path) VALUES (?, ?)");
                $stmt->bind_param("is", $lastInsertId, $slideshowImagePath);
                $stmt->execute();
            }
        }

        header("Location: admin/success.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        // Handle the database error appropriately
        die("Database error: " . $e->getMessage());
    }
}
?>
