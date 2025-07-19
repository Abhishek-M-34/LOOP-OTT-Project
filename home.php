<?php
# Edited by Abhishek
session_start();

// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Redirect to your login page
    exit();
}

// Database configuration for MySQL in XAMPP
$host = "localhost";
$dbname = "ott";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password is empty

try {
    // Connect to the MySQL database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch upcoming movies with current_status = 'Upcoming'
    $stmt = $pdo->prepare("SELECT * FROM links WHERE current_status = 'Upcoming' ORDER BY id DESC");
    $stmt->execute();
    $upcomingMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the current date
    $current_date = date('Y-m-d H:i:s');

    // Check for expired premium users and update their account type
    $stmt = $pdo->prepare("SELECT user_email FROM payment_request WHERE premium_end_time < ?");
    $stmt->execute([$current_date]);
    $expired_premium_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($expired_premium_users as $user) {
        $stmt = $pdo->prepare("UPDATE user SET account_type = 'Basic' WHERE email = ?");
        $stmt->execute([$user['user_email']]);
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOOP OTT Platfrom</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/background-animation.css">
</head>

<body>
    <header>
        <img class="logo" src="logo.png" alt="Your Logo">
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
    
    <!-- Floating particles -->
    <!-- <div class="cinema-particles">
        <div class="cinema-particle"></div>
        <div class="cinema-particle"></div>
        <div class="cinema-particle"></div>
        <div class="cinema-particle"></div>
    </div> -->
    
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
        <div class="featured">
            <div class="movie-list">
                <div class="upcoming-movies" id="upcomingMoviesContainer">
                    <h2 id="movies-heading" style="text-align: center;">Upcoming</h2>
                    <div class="scrollable-row" id="upcomingMoviesScrollable">
                        <?php
                        if (isset($upcomingMovies) && !empty($upcomingMovies)) {
                            foreach ($upcomingMovies as $movie) {
                                echo '<div class="upcoming-movie-item">';
                                echo '<a href="upcoming_detail.php?movie_id=' . $movie['id'] . '">';
                                echo '<img src="' . $movie['image_link'] . '" alt="' . $movie['title'] . '">';
                                echo '</a>';
                                echo '<p>' . $movie['title'] . '</p>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p style="text-align: center;">No upcoming movies available</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="aa">
            <h2 id="movies-heading" style="text-align: center;">Movies</h2>
            <div class="movie-list">
                <?php
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

                try {
                    $stmt = $pdo->prepare("SELECT * FROM links WHERE current_status = 'p_uploaded' ORDER BY id DESC");
                    $stmt->execute();
                    $premiumLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $stmt = $pdo->query("SELECT * FROM links WHERE current_status = 'uploaded' ORDER BY id DESC");
                    $allLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $links = array_merge($premiumLinks, $allLinks);
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
                ?>

                <?php if (!empty($links)) : ?>
                    <?php foreach ($links as $link) : ?>
                        <div class="movie-item">
                            <a href="movie_detail.php?movie_id=<?php echo $link['id']; ?>">
                                <img src="<?php echo $link['image_link']; ?>" alt="<?php echo $link['title']; ?>">
                            </a>
                            <div class="caption"><?php echo $link['title']; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="text-align: center;">No latest movies available</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
