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

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch upcoming movies with current_status = 'Upcoming'
    $stmt = $pdo->prepare("SELECT * FROM links WHERE current_status = 'Upcoming' ORDER BY id DESC");
    $stmt->execute();
    $letest = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<?php
// Database configuration
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";


try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch movies uploaded in the last month with status 'Upcoming'
    $stmt = $pdo->prepare("SELECT * FROM links WHERE current_status = 'uploaded' AND uploading_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ORDER BY id DESC");
    $stmt->execute();
    $latestMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug statement to inspect the fetched movies
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/movie.css">
    <link rel="stylesheet" href="css/background-animation.css">
    <link rel="stylesheet" href="css/navbar.css">

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
                <h2 id="movies-headingg">Latest Added</h2>
                <div class="letest-movies" id="letestMoviesContainer">
                    <div class="scrollable-row" id="letestMoviesScrollable">
                        <?php
                        if (isset($latestMovies) && !empty($latestMovies)) {
                            foreach ($latestMovies as $movie) {
                                echo '<div class="letest-movie-item">';
                                echo '<a href="movie_detail.php?movie_id=' . $movie['id'] . '">';
                                echo '<img src="' . $movie['image_link'] . '" alt="' . $movie['title'] . '" class="latest-movie-image">';
                                echo '</a>';
                                echo '<p>' . $movie['title'] . '</p>';
                                // Add more movie details if needed
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No latest movies available</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="aa">
            <h2 id="movies-heading">Movies </h2>
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
                    // Select only movies with current_status = 'uploaded' or 'p_uploded'
                    $stmt = $pdo->prepare("SELECT * FROM links WHERE current_status = 'uploaded' OR current_status = 'p_uploaded' ORDER BY id DESC");
                    $stmt->execute();
                    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
                ?>

                <?php foreach ($links as $link) : ?>
                    <div class="movie-item">
                        <a href="movie_detail.php?movie_id=<?php echo $link['id']; ?>">
                            <img src="<?php echo $link['image_link']; ?>" alt="<?php echo $link['title']; ?>">
                        </a>
                        <div class="caption"><?php echo $link['title']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </main>


    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $(".letest-movie-item").draggable({
                axis: "x", // Allow dragging along the horizontal axis
                cursor: "grabbing", // Change cursor style
                containment: "parent", // Limit dragging within the parent container
            });
        });
        let lastScrollTop = 0; // Variable to store the last scroll position
        const header = document.querySelector('header');

        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > lastScrollTop) { // Scrolling down
                header.classList.remove('header-scroll-down');
                header.classList.add('header-scroll-up');
            } else { // Scrolling up
                header.classList.remove('header-scroll-up');
                header.classList.add('header-scroll-down');
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
        });
        // Wait for the document to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Get the element with the class 'letest-movies'
            var moviesContainer = document.querySelector('.letest-movies');
            // Set the 'overflow' property to 'hidden' to hide the scrollbar
            moviesContainer.style.overflow = 'hidden';
        });
    </script>


</body>

</html>
