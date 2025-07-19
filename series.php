<?php
# Edited by Amish
session_start();
// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Redirect to your login page
    exit();
}

// Database configuration
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Create connection to MySQL using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch series uploaded in the last month with status 'uploaded'
$sql = "SELECT * FROM series WHERE current_status = 'uploaded' AND uploading_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ORDER BY series_id DESC";
$result = $conn->query($sql);

// Initialize an array to store the latest series
$latestSeries = [];

if ($result->num_rows > 0) {
    // Fetch results into an associative array
    while ($row = $result->fetch_assoc()) {
        $latestSeries[] = $row;
    }
} else {
    echo "No series found.";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HINDI MOVIE OTT</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/series.css">
    <link rel="stylesheet" href="css/background-animation.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>

<body>
<!-- Premium cinematic background -->
<div class="cinema-background">
    <!-- Main gradient background -->
    <div class="bg-gradient"></div>
    
    <!-- Noise texture overlay -->
    <div class="noise-overlay"></div>
    
    <!-- Light beams -->
    <div class="light-beams">
        <div class="beam"></div>
        <div class="beam"></div>
        <div class="beam"></div>
    </div>
    
    <!-- Cinematic icons -->
    <div class="cinema-icons">
        <div class="cinema-icon"></div>
        <div class="cinema-icon"></div>
        <div class="cinema-icon"></div>
        <div class="cinema-icon"></div>
    </div>
    
    <!-- Starry background -->
    <div class="stars">
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
    </div>
    
    <!-- Bottom glow effect -->
    <div class="bottom-glow"></div>
</div>
    <header>
        <!-- Add your logo and navigation here -->
        <img src="logo.png" alt="Your Logo">
        <nav>
            <ul>
                <li><a href="home.php"><i class='bx bx-home'></i> home</a></li>
                <li><a href="series.php"><i class='bx bx-tv'></i> Series</a></li>
                <li><a href="movie.php"><i class='bx bx-movie-play'></i> Movie</a></li>
                <li><a href="premium.php"><i class='bx bx-wallet-alt'></i> Premium</a></li>
                <li><a href="profile.php"><i class='bx bx-user'></i> Profile</a></li>
                <li><a href="search.php"><i class='bx bx-search'></i> Search</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="featured">
            <div class="series-list">
                <div class="latest-series" id="latestSeriesContainer">
                    <h2 id="series-heading">Latest Added Series</h2>
                    <div class="scrollable-row" id="latestSeriesScrollable">
                        <?php
                        if (isset($latestSeries) && !empty($latestSeries)) {
                            echo '<div class="scrollable-container">';
                            foreach ($latestSeries as $series) {
                                echo '<div class="latest-series-item">';
                                echo '<a href="series_detail.php?series_id=' . $series['series_id'] . '">';
                                echo '<img src="' . $series['image_path'] . '" alt="' . $series['title'] . '" class="latest-series-image">';
                                echo '</a>';
                                echo '<p>' . $series['title'] . '</p>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p>No latest series available</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="aa">
            <h2 id="series-heading">Series</h2>
            <div class="series-list">
                <?php
                // Database connection
                $host = "localhost";
                $dbname = "ott";
                $username = "root";
                $password = "";

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Fetch series data from the database
                    $stmt = $pdo->query("SELECT * FROM series ORDER BY series_id DESC");
                    $series = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Display series
                    foreach ($series as $s) {
                        echo '<div class="series-item">';
                        echo '<a href="series_detail.php?series_id=' . $s['series_id'] . '">';
                        echo '<img src="' . $s['image_path'] . '" alt="' . $s['title'] . '">';
                        echo '</a>';
                        echo '<div class="caption">' . $s['title'] . '</div>';
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }
                ?>
            </div>
        </div>


    </main>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $(".letest-series-item").draggable({
                axis: "x", // Allow dragging along the horizontal axis
                cursor: "grabbing", // Change cursor style
                containment: "parent", // Limit dragging within the parent container
            });
        });
    </script>

</body>

</html>
