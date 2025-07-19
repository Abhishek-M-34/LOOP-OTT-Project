<?php
# Edited by Amish
// Database connection
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adTitle = $_POST["adTitle"];

    // Process ad image upload
    $adImageFile = $_FILES["adImage"];

    if ($adImageFile["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "ad_images/";
        $adImagePath = $uploadDir . basename($adImageFile["name"]);

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move uploaded image to destination directory
        if (move_uploaded_file($adImageFile["tmp_name"], $adImagePath)) {
            try {
                // Insert advertisement data into database using prepared statement
                $stmt = $pdo->prepare("INSERT INTO ad (ad_title, ad_image) VALUES (:adTitle, :adImage)");
                $stmt->bindParam(':adTitle', $adTitle, PDO::PARAM_STR);
                $stmt->bindParam(':adImage', $adImagePath, PDO::PARAM_STR);
                $stmt->execute();

                // Redirect to success page
                header("Location: ad_upload.php?success=true");
                exit();
            } catch (PDOException $e) {
                echo "Error inserting advertisement: " . $e->getMessage();
            }
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "Error uploading image: " . $adImageFile["error"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisement Upload</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/ad_upload.css">
</head>

<body>
    <header>
        <!-- Add your logo and navigation here -->
        <img src="../logo.png" alt="Your Logo">
        <nav>
            <ul>
                <!-- Add navigation links -->
                <li><a href="admin_home.php"><i class='bx bxs-home-alt-2'></i></a></li>
                <li><a href="upload.html"><i class='bx bx-upload'></i></a></li>
                <li><a href="user.php"><i class='bx bxs-user'></i></i></a></li>
                <li><a href="video_management.php"><i class='bx bxs-videos'></i></i></a></li>
                <li><a href="payment_managemant.php"><i class='bx bxs-purchase-tag'></i></i></i></a></li>
                <li><a href="ad_upload.php"><i class='bx bx-cloud-upload'></i></a></li>
                <li><a href="../signout.php"><i class='bx bx-log-out-circle'></i></a></li>
            </ul>
        </nav>
    </header>
    <main class="table" id="customers_table">
        <section class="table__header">
        <h1>Upload Advertisement</h1>
        </section>
        <section class="table__body">
            <?php if (isset($_GET['success']) && $_GET['success'] == 'true') : ?>
                <p>Advertisement uploaded successfully.</p>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="adTitle">Advertisement Title:</label><br>
                <input type="text" name="adTitle" id="adTitle" required><br><br>

                <label for="adImage">Advertisement Image:</label><br>
                <input type="file" name="adImage" id="adImage" accept="image/*" required><br><br>

                <input type="submit" value="Upload Advertisement">
            </form>
        </section>
        <script src="script.js"></script>


</body>

</html>
