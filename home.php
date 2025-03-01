<?php
# Edited by Amish
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
    <title>HINDI MOVIE OTT</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

    <style>
        ::-webkit-scrollbar {
            display: none;
        }

        * {
            box-sizing: border-box;

        }



        /* Style the main content section */
        main {}

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

        #movies-heading {}

        /* Movie item styles */
        .movie-list {
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

        .upcoming-movies h2 {
            font-size: 24px;
            margin-bottom: 15px;

        }

        /* CSS for the scrollable row */
        .scrollable-row {
            display: flex;
            gap: 10px;
            /* Optional: Add scrollbar styles */
            scrollbar-width: thin;
            scrollbar-color: #888 #f5f5f5;
            /* Ensure each item has fixed width */
        }

        /* Style for each upcoming movie item */
        .upcoming-movie-item {
            flex: 0 0 auto;
            width: 150px;
            /* Set fixed width for each item */
            text-align: center;
            /* Optional: Adjust margin/padding for each item */
        }

        .upcoming-movie-item img {
            width: 100%;
            height: auto;
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
        .upcoming-movie-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            /* Include box-shadow in transition */
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .upcoming-movie-item img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .upcoming-movie-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            /* Add a shadow on hover */
        }

        /* Caption animation */
        .upcoming-movie-item .caption {
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

        .upcoming-movie-item:hover .caption {
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
            padding-left: 20px;
            padding-right: 20px;
            padding-bottom: 20px;

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
            justify-content: space-around;
        }

        .movie-item {
            display: inline-flex;

            margin: auto;
            width: 123.5px;
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

        @media screen and (max-width: 600px) {
            .movie-item {
                display: inline-flex;
                margin: 4.3874998px;
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

            .slideshow-container img {
                width: 100%;
                height: 250px;
            }
        }



        .upcoming-movies {
            padding: 13px;
            /* Change this line to set the background color to black */
            margin-top: 15px;
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

        ::-webkit-scrollbar {
            display: none;
        }

        /* Optional: Add scrollbar styles */
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





        .slideshow-container img {
            width: 100%;
            height: 550px;
        }

        /* Optional: Style the previous and next buttons */
        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            margin-top: -22px;
            padding: 16px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
        }

        .prev {
            left: 0;
            border-radius: 3px 0 0 3px;
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .slideshow-container {
            overflow: hidden;
            position: relative;
        }

        .mySlides {
            display: none;
            width: 100%;
            transition: transform 0.5s ease-in-out;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 99%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black background */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
        }

        h2 {

            margin-bottom: 10px;
            margin-right: 75%;
        }

        p {
            margin: 0;
        }

        .upcoming-movie-item img {
            width: 100%;
            height: 85%;
            border-radius: 5px;
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
                margin: 4.3874998px;
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

            .slideshow-container img {
                width: 100%;
                height: 250px;
            }

            header {
                position: fixed;
                width: 100%;
                z-index: 1000;
                background-color: #000;
                color: #fff;
                padding: 5px 5px;
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

        .movie-item {
            display: inline-flex;
            margin: auto;
            width: 123.5px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
    <h1>pranav xxx</h1>
    <div class="slideshow-container">
        <!-- Slideshow images will be displayed here -->
        <?php
        try {
            // Fetch slideshow images from the database
            $stmt = $pdo->prepare("SELECT s.slideshow_image_path, l.title, l.description, l.id 
                                       FROM slideshow_images AS s 
                                       INNER JOIN links AS l ON s.link_id = l.id");
            $stmt->execute();
            $slideshowImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($slideshowImages)) {
                foreach ($slideshowImages as $index => $image) {
                    $slideIndex = $index + 1;
                    // Open anchor tag for the entire slide
                    echo '<a href="movie_detail.php?movie_id=' . $image['id'] . '" class="slide-link">';
                    echo '<div class="mySlides fade">';
                    if (!empty($image['slideshow_image_path']) && file_exists($image['slideshow_image_path'])) {
                        // Image and overlay content
                        echo '<img src="' . $image['slideshow_image_path'] . '" alt="Slideshow Image ' . $slideIndex . '">';
                        echo '<div class="overlay">';
                        echo '<h2>' . $image['title'] . '</h2>';
                        echo '</div>';
                    } else {
                        echo '<p>Error: Image not found</p>';
                    }
                    echo '</div>';
                    // Close anchor tag for the entire slide
                    echo '</a>';
                }
            } else {
                // Handle case where no slideshow images are found
                echo '<p>No slideshow images available</p>';
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
        ?>

    </div>





    <main>
        <div class="featured">
            <div class="movie-list">
                <div class="upcoming-movies" id="upcomingMoviesContainer">
                    <h2 id="movies-headingg">Upcoming</h2>

                    <div class="scrollable-row" id="upcomingMoviesScrollable">
                        <?php
                        if (isset($upcomingMovies) && !empty($upcomingMovies)) {
                            foreach ($upcomingMovies as $movie) {
                                echo '<div class="upcoming-movie-item">';
                                // Wrap image with anchor tag
                                echo '<a href="upcoming_detail.php?movie_id=' . $movie['id'] . '">';
                                echo '<img src="' . $movie['image_link'] . '" alt="' . $movie['title'] . '">';
                                echo '</a>';
                                echo '<p>' . $movie['title'] . '</p>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No upcoming movies available</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="aa">
            <h2 id="movies-heading">Movies </h2>
            <div class="movie-list">
                <!-- PHP Code to Fetch and Display Images Linked to Video URLs from Database -->
                <?php
                // Replace this with your database connection logic
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
                    // Fetch movies with current_status = 'p_uploaded'
                    $stmt = $pdo->prepare("SELECT * FROM links WHERE current_status = 'p_uploaded' ORDER BY id DESC");
                    $stmt->execute();
                    $premiumLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Fetch all movies with current_status = 'uploaded'
                    $stmt = $pdo->query("SELECT * FROM links WHERE current_status = 'uploaded' ORDER BY id DESC");
                    $allLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Combine both sets of links
                    $links = array_merge($premiumLinks, $allLinks);
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
                ?>

                <?php foreach ($links as $link) : ?>
                    <div class="movie-item">
                        <!-- Wrap the image with a link -->
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
        // JavaScript for Slideshow
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 3000); // Change image every 3 seconds
        }

        $(function() {
            $(".upcoming-movie-item").draggable({
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
    </script>


</body>

</html>
