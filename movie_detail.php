<?php
# Edited by Amish
// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if movie ID is provided in the URL
if (!isset($_GET['movie_id'])) {
    header("Location: home.php"); // Redirect to home page if movie ID is not provided
    exit();
}

// Database configuration for XAMPP (MySQL)
$host = "localhost";
$dbname = "ott"; // Your database name
$username = "root"; // Default MySQL username in XAMPP
$password = ""; // Default password is empty in XAMPP

// Create MySQLi connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch movie details based on the provided movie ID
$movieId = $_GET['movie_id'];
$stmt = $mysqli->prepare("SELECT * FROM links WHERE id = ?");
$stmt->bind_param("i", $movieId); // Bind the movie ID parameter
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

// Check if movie exists
if (!$movie) {
    header("Location: home.php"); // Redirect to home page if movie not found
    exit();
}

// Fetch user's account type from the session
$userEmail = $_SESSION['user_email'];
$userStmt = $mysqli->prepare("SELECT account_type FROM user WHERE email = ?");
$userStmt->bind_param("s", $userEmail); // Bind the user email parameter
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userAccountType = $user['account_type'];

// Close the prepared statements
$stmt->close();
$userStmt->close();

// Close the MySQLi connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Movie Detail</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/background-animation.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/movie_detail.css">
</head>

<body>
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

    <main>
        <!-- Display movie poster -->
        <div class="movie-poster">
            <img src="<?php echo htmlspecialchars($movie['image_link']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="img-fluid">
        </div>
        <!-- Display movie description -->
        <div class="movie-description">
            <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
            <p><?php echo htmlspecialchars($movie['description']); ?></p>
            <!-- Add watch movie button -->
            <button class="watch-movie-btn" onclick="checkStatus('<?php echo $movie['current_status']; ?>')">Watch Movie</button>
        </div>
    </main>

    <script>
        function checkStatus(status) {
            // Check if the movie's current status is 'p_uploaded'
            if (status === 'p_uploaded') {
                // Check if the user is a Premium user
                <?php if ($userAccountType === 'Premium') : ?>
                    // Redirect to the video player page
                    var videoLink = "<?php echo urlencode($movie['video_link']); ?>";
                    window.location.href = "video_player.html?movie=" + videoLink;
                <?php else : ?>
                    // Display an alert and redirect to premium page after 5 seconds
                    alert("To watch this movie, please upgrade to Premium.");
                    setTimeout(function() {
                        window.location.href = "premium.php";
                    }, 2000);
                <?php endif; ?>
            } else {
                // Redirect to the video player page for other status
                var videoLink = "<?php echo urlencode($movie['video_link']); ?>";
                window.location.href = "video_player.html?movie=" + videoLink;
            }
        }
    </script>
</body>
</html>
