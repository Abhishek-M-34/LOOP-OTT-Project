<?php
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

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

       

        form {
            margin: 12.70%;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        p.success {
            color: green;
            text-align: center;
        }

        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding-top: 60px;
            /* Adjusted padding to accommodate the fixed header */
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header styles */
        header {
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            /* Fixed position */
            top: 0;
            /* Fixed to the top */
            width: 100%;
            /* Full width */
            z-index: 1000;
            /* Ensure it's above other content */
        }

        header img {
            max-width: 50px;
        }

        nav ul {
            list-style: none;
            display: flex;
        }

        nav ul li {
            margin-right: 15px;
        }

        nav a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
        }

        nav ul li:hover::before {
            content: attr(title);
            position: absolute;
            background-color: #000;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
        }

        nav ul li a i {
            font-size: 24px;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 80px;
            /* Adjusted margin to prevent content from being hidden behind the fixed header */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Button styles */
        button {
            padding: 8px 15px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        button.save {
            background-color: #28a745;
        }

        button.save:hover {
            background-color: #218838;
        }

        button.delete {
            background-color: #dc3545;
        }

        button.delete:hover {
            background-color: #c82333;
        }

        /* Pagination styles */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            color: #007bff;
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
        <link rel="stylesheet" href="style.css">

</head>

<body>
    <header>
        <!-- Add your logo and navigation here -->
        <img src="../your-logo.png" alt="Your Logo">
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