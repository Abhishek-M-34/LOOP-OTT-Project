<?php
// Database connection
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Create connection using mysqli
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch advertisements from the database
$query = "SELECT * FROM ad ORDER BY id DESC LIMIT 1";
$result = $mysqli->query($query);


if (!$result) {
    die("Query failed: " . $mysqli->error); 
}
// Fetch the row if available
$num_rows = $result->num_rows; 
if ($num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $row = null;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisement Page</title>
    <style>
        ::-webkit-scrollbar {
            display: none;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #111;
            color: #fff;
            padding: 20px;
        }

        .content-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 100px;
            height: auto;
        }

        .ad-image {
            max-width: 65%;
            margin-bottom: 20px;
            position: relative;
        }

        .skip-button {
            background-color: #e50914;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>

<body>

    <div class="content-container">
        <div class="latest-ad">
            <?php if ($row && isset($row['ad_image']) && isset($row['ad_title'])): ?>
                <!-- Display advertisement -->
                <img id="adImage" src="admin/<?php echo htmlspecialchars($row['ad_image']); ?>" class="ad-image" alt="<?php echo htmlspecialchars($row['ad_title']); ?>">
                <h3><?php echo htmlspecialchars($row['ad_title']); ?></h3>
                <button id="skipButton" class="skip-button">Skip</button>
            <?php else: ?>
                <!-- No advertisement available -->
                <p>No advertisement available.</p>                                              
            <?php endif; ?>
        </div>
        <div class="logo">
            <img src="OIP.png" alt="Website Logo">
        </div>
    </div>

    <script>
        document.getElementById("skipButton").addEventListener("click", function() {
            window.location.href = "home.php"; 
        });

        // Auto skip after 6 seconds
        setTimeout(function() {
            window.location.href = "home.php"; 
        }, 6000);
    </script>

</body>

</html>

<?php
// Close the database connection
$mysqli->close();
?>
