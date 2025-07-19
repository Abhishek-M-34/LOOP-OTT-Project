<?php
# Edited by Amish
session_start();
// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php"); // Redirect to your login page
    exit();
}

// Database configuration
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Establish connection to the database
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables for search query and results
$search_query = "";
$search_results = [];

// Check if the search form is submitted
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];

    // Escape special characters to prevent SQL injection
    $search_query = mysqli_real_escape_string($conn, $search_query);

    // Prepare the query to search in both movies and series
    $sql = "
        (SELECT id, title, image_link, 'movie' as type FROM links WHERE title LIKE ?)
        UNION
        (SELECT series_id, title, image_path, 'series' as type FROM series WHERE title LIKE ?)
    ";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind parameters to the prepared statement
        $search_query_with_wildcards = "%" . $search_query . "%";
        mysqli_stmt_bind_param($stmt, "ss", $search_query_with_wildcards, $search_query_with_wildcards);

        // Execute the query
        mysqli_stmt_execute($stmt);

        // Get the result of the query
        $result = mysqli_stmt_get_result($stmt);

        // Check if there are any results
        if (mysqli_num_rows($result) > 0) {
            // Fetch data and store it in $search_results array
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        } else {
            // No results found
            echo "No results found!";
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        // If there was an issue with the prepared statement
        echo "Error preparing the query: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/background-animation.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/search.css">
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

    
    <div class="container">
    <div class="form-wrapper">
        <form method="POST" action="">
            <input type="text" class="s" name="search_query" placeholder="Search for movies and series" value="<?php echo $search_query; ?>">
            <button type="submit" name="search"><i class='bx bx-search'></i> Search</button>
        </form>
</div>
        <!-- Display search results -->
        <?php if (!empty($search_results)) : ?>
            <div class="search-results">
                <?php foreach ($search_results as $result) : ?>
                    <?php if ($result['type'] == 'movie') : ?>
                        <div class="result-item movie">
                            <a href="movie_detail.php?movie_id=<?php echo $result['id']; ?>">
                                <img src="<?php echo $result['image_link']; ?>" alt="<?php echo $result['title']; ?>">
                            </a>
                            <div class="title"><?php echo $result['title']; ?></div>
                        </div>
                    <?php else : ?>
                        <div class="result-item series-item">
                            <a href="series_detail.php?series_id=<?php echo $result['id']; ?>">
                                <img src="<?php echo $result['image_link']; ?>" alt="<?php echo $result['title']; ?>">
                            </a>
                            <div class="title"><?php echo $result['title']; ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <script>
            // Function to reload the page after a delay
            function reloadPage() {
                setTimeout(function() {
                    window.location.reload();
                }, 1000); // Reload after 1 second
            }
        </script>
</body>

</html>
