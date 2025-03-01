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
$stmt = $pdo->prepare("SELECT * FROM links WHERE id = ?");
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
    <style>
        /* Add your CSS styles here */
        /* Example styles for demonstration */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            position: fixed;
            top: 0;
            width: 98%;
        }

        header img {
            margin-left: 20px;
            max-width: 40px;
        }

        nav ul {
            list-style: none;
            display: flex;
            padding-left: 2px;
        }



        main {
            padding: 20px;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-top: 80px;
            /* Add top margin to ensure content is below the fixed header */
        }

        .movie-poster {
            margin-top: 5%;
            margin-right: 20px;
            max-width: 300px;
        }

        .movie-poster img {
            max-width: 100%;
        }

        .movie-description {
            margin-top: 13%;
            max-width: 50%;
        }

        .movie-description h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .movie-description p {
            font-size: 16px;
            /* Decrease the font size */
            color: #888;
            /* Set the color to gray */
        }

        .watch-movie-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-align: center;
            cursor: pointer;
            /* Add cursor pointer to indicate it's clickable */
        }

        .watch-movie-btn:hover {
            background-color: #0056b3;
        }

        header {
            position: fixed;
            width: 98%;
            z-index: 1000;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            font-size: 15px;
        }

        nav ul {
            text-align: center;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            display: inline-block;
            margin: 0 10px;
        }

        nav ul li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            /* Adjust color as needed */
        }

        nav ul li a i {
            margin-right: 5px;
            font-size: 24px;
        }

        @media screen and (max-width: 600px) {

            header {
                position: fixed;
                width: 98%;
                z-index: 1000;
                background-color: #000;
                color: #fff;
                padding: 7px 5px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            nav a {
                text-decoration: none;
                color: #fff;
                font-weight: bold;
                font-size: 10px;
            }

            nav ul li a i {
                font-size: 17px;
            }

            nav ul li {
                display: inline-block;
                margin: 0px 2px;
            }

            nav a {
                text-decoration: none;
                color: #fff;
                font-weight: bold;
                font-size: 8px;
            }
        }
    </style>
</head>

<body>
    <header>
        <!-- Add your logo and navigation here -->
        <img src="your-logo.png" alt="Your Logo">
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
