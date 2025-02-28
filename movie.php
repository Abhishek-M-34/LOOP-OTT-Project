<?php
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
    <title>HINDI MOVIE OTT</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

    <style>
        /* Hide scrollbar for Chrome, Safari, and Opera */
        ::-webkit-scrollbar {
            display: none;
        }


        * {
            box-sizing: border-box;

        }


        /* Style the header section */
        header {
            background-color: #000;
            color: #fff;
            padding: 5px 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header img {
            max-width: 50px;
            /* Adjust the max-width as needed */
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
            font-size: 14px;
            /* Adjust the font size */
        }

        /* Style the main content section */
        main {
            padding: 20px;
        }

        .hero {
            text-align: center;
            margin-bottom: 30px;
        }

        .hero h4 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .hero h6 {
            font-size: 18px;
            color: #666;
        }

        /* Style the footer section */
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #000;
            color: #fff;
        }

        #movies-heading {
            margin-left: 20px;
        }

        /* Movie item styles */
        .movie-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-item {
            margin: 10px;
            width: 200px;
            /* Adjust width as needed */
            transition: transform 0.3s ease-in-out;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .movie-item img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 5px 5px 0 0;
        }

        /* Add hover effect */
        .movie-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .movie-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 8px;
            box-sizing: border-box;
            transform: translateY(100%);
            transition: transform 0.3s ease-in-out;
            border-radius: 0 0 5px 5px;
            opacity: 0;
        }

        .movie-item:hover .caption {
            transform: translateY(0%);
            opacity: 1;
        }

        @media screen and (max-width: 600px) {
            .movie-item {
                width: 150px;
                /* Adjust width for smaller screens */
                margin: 5px;
                /* Adjust margin for smaller screens */
            }

            .movie-item img {
                width: 100%;
                height: auto;
                display: block;
            }
        }

        nav ul li a i {
            font-size: 24px;
            /* Adjust the font size as needed */
        }

        /* CSS for the upcoming movies section */

        .letest-movies h2 {
            font-size: 24px;
            margin-bottom: 15px;

        }


        /* Style for each upcoming movie item */
        .letest-movie-item {
            flex: 0 0 auto;
            width: 150px;
            /* Set fixed width for each item */
            text-align: center;
            /* Optional: Adjust margin/padding for each item */
        }

        .letest-movie-item img {
            width: 100%;
            height: 200px;
            border-radius: 5px;
        }

        .movie-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }

        /* Caption animation */
        .movie-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 8px;
            box-sizing: border-box;
            transform: translateY(100%);
            transition: transform 0.3s ease-in-out;
            border-radius: 0 0 5px 5px;
            opacity: 0;
        }

        .movie-item:hover .caption {
            transform: translateY(0%);
            opacity: 1;
        }

        /* Adjust the scale and shadow on hover */
        .letest-movie-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            /* Include box-shadow in transition */
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .letest-movie-item img {
            width: 100%;
            height: 200px;
            border-radius: 5px;
        }

        .letest-movie-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            /* Add a shadow on hover */
        }

        /* Caption animation */
        .letest-movie-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 8px;
            box-sizing: border-box;
            transform: translateY(100%);
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            /* Include opacity in transition */
            border-radius: 0 0 5px 5px;
            opacity: 0;
        }

        .letest-movie-item:hover .caption {
            transform: translateY(0%);
            /* Slide up the caption on hover */
            opacity: 1;
            /* Fade in the caption on hover */
        }

        /* Reset some default margin and padding for consistency */
        body,
        h1,
        h2,
        h3,
        p,
        ul {
            margin: 0;
            padding: 0;
        }

        /* Apply a global box-sizing border-box for easier layout calculations */
        * {
            box-sizing: border-box;
        }

        /* Set a dark background color and font for the entire page */

        /* Style the header section */
        header {
            position: fixed;
            width: 100%;
            z-index: 1000;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header img {
            max-width: 50px;
            /* Adjust the max-width as needed */
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
            font-size: 14px;
            /* Adjust the font size */
        }

        /* Style the main content section */
        main {
            padding: 20px;
            padding-top: 70px;
            /* Adjust this value based on your header height
                background-color: #0d1117; ------- */
        }

        /* Style the footer section */
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        /* Movie item styles */
        .movie-list {
            display: inline;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-item {
            display: inline-flex;

            margin: 3.4px;
            width: 120px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .movie-item img {
            width: 100%;
            height: 200px;
            display: block;
            border-radius: 5px 5px 0 0;
        }

        /* Add hover effect */
        .movie-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(255, 255, 255, 0.2);
            /* Add a shadow on hover */
        }

        /* Caption animation */
        .movie-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 8px;
            box-sizing: border-box;
            transform: translateY(100%);
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            /* Include opacity in transition */
            border-radius: 0 0 5px 5px;
            opacity: 0;
        }

        .movie-item:hover .caption {
            transform: translateY(0%);
            opacity: 1;
        }

        @media screen and (max-width: 1191px) {
            .movie-item {
                display: inline-flex;

                margin: auto;
                width: 140px;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .movie-item img {
                width: 110%;
                height: 200px;
                display: block;
                border-radius: 5px 5px 0 0;
            }
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
            /* Adjust spacing between icon and text */
        }

        @media screen and (max-width: 600px) {
            .movie-item {
                display: inline-flex;
                margin: auto;
                width: 100px;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .movie-item img {
                width: 110%;
                height: 150px;
                display: block;
                border-radius: 5px 5px 0 0;
            }

            header {
                position: fixed;
                width: 100%;
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



        .letest-movies {
            max-width: 100%;
            overflow: auto;
            margin-right: 1%;
        }

        ::-webkit-scrollbar-track {
            background-color: #0d1117;
            /* Change this to your desired background color */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #888;
            /* Change this to your desired thumb color */
        }

        /* Change scrollbar handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background-color: #555;
            /* Change this to your desired thumb color on hover */
        }

        h2#movies-heading {
            margin-top: 20px;
            /* Adjust the value as needed */
        }

        div::-webkit-scrollbar {
            display: none;
        }

        .wrapper::-webkit-scrollbar {
            width: 0;
        }

        /* Example animation styles for header */
        .header-scroll-up {
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .header-scroll-down {
            transform: translateY(0);
            transition: transform 0.3s ease-in-out;
        }

        /* Set a linear gradient background color for the entire page */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to left, #ffffff, #3d2fea);

            /* Updated background */
            color: #c9d1d9;
            line-height: 1.6;
            margin: 0;
        }

        .scrollable-row {
            color: #000;
            /* Set text color to black */

        }

        .movies-headingg {
            color: black;
        }


        .latest-movie-image {
            width: 150px;
            /* Set a fixed width for the images */
            height: 200px;

            /* Maintain aspect ratio */
            border-radius: 5px;
            /* Apply border radius if needed */
        }




        .scrollable-row {
            display: inline-flex;
            gap: 10px;
            padding-bottom: 20px;
            padding-top: 20px;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
        }

        .scrollable-row::-webkit-scrollbar {
            display: none;
        }

        .latest-movie-image {
            width: 100px;
            height: 200px;
        }

        .slideshow-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            /* Adjust max-width as needed */
            margin: 0 auto;
        }

        .mySlides {
            display: none;
            width: 100%;
        }

        .mySlides img {
            width: 100%;
            height: auto;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
            opacity: 0;
            /* Initially hidden */
            transition: opacity 0.3s ease;
            /* Smooth transition */
            cursor: pointer;
            /* Change cursor to pointer */
        }

        .mySlides:hover .overlay {
            opacity: 1;
            /* Show overlay on hover */
        }

        .overlay h2,
        .overlay p {
            margin: 0;
        }
    </style>

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