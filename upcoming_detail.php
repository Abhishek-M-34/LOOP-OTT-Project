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

// Database configuration
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch movie details based on the provided movie ID
$movieId = $_GET['movie_id'];
$stmt = $pdo->prepare("SELECT * FROM links WHERE id = :id");
$stmt->execute(['id' => $movieId]);

$movie = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if movie exists
if (!$movie) {
    header("Location: home.php"); // Redirect to home page if movie not found
    exit();
}

// Debug output
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Movie Detail</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/background-animation.css">
    <link rel="stylesheet" href="css/upcoming_details.css">
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
            <a class="watch-movie-btn">Movie Coming Soon</a>
        </div>
    </main>

</body>

</html>
