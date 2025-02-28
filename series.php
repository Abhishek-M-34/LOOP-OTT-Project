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

        #series-heading {
            margin-left: 20px;
        }

        .series-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .series-item {
            margin: 10px;
            width: 200px;
            /* Adjust width as needed */
            transition: transform 0.3s ease-in-out;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .series-item img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 5px 5px 0 0;
        }

        /* Add hover effect */
        .series-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .series-item .caption {
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

        .series-item:hover .caption {
            transform: translateY(0%);
            opacity: 1;
        }

        @media screen and (max-width: 600px) {
            .series-item {
                width: 150px;
                /* Adjust width for smaller screens */
                margin: 5px;
                /* Adjust margin for smaller screens */
            }

            .series-item img {
                width: 100%;
                height: auto;
                display: block;
            }
        }

        nav ul li a i {
            font-size: 24px;
            /* Adjust the font size as needed */
        }


        .letest-series h2 {
            font-size: 24px;
            margin-bottom: 15px;

        }


        .letest-series-item {
            flex: 0 0 auto;
            width: 150px;
            /* Set fixed width for each item */
            text-align: center;
            /* Optional: Adjust margin/padding for each item */
        }

        .letest-series-item img {
            width: 100%;
            height: 200px;
            border-radius: 5px;
        }

        .series-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }

        /* Caption animation */
        .series-item .caption {
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

        .series-item:hover .caption {
            transform: translateY(0%);
            opacity: 1;
        }

        /* Adjust the scale and shadow on hover */
        .letest-series-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            /* Include box-shadow in transition */
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .letest-series-item img {
            width: 100%;
            height: 200px;
            border-radius: 5px;
        }

        .letest-series-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            /* Add a shadow on hover */
        }

        /* Caption animation */
        .letest-series-item .caption {
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

        .letest-series-item:hover .caption {
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

        .series-list {
            display: inline;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .series-item {
            display: inline-flex;

            margin: 3.4px;
            width: 120px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .series-item img {
            width: 100%;
            height: 200px;
            display: block;
            border-radius: 5px 5px 0 0;
        }

        /* Add hover effect */
        .series-item:hover {
            transform: scale(1.05);
            /* Increase the scale on hover */
            box-shadow: 0 6px 8px rgba(255, 255, 255, 0.2);
            /* Add a shadow on hover */
        }

        /* Caption animation */
        .series-item .caption {
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

        .series-item:hover .caption {
            transform: translateY(0%);
            opacity: 1;
        }

        @media screen and (max-width: 1191px) {
            .series-item {
                display: inline-flex;
                margin: auto;
                width: 140px;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .series-item img {
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
            .series-item {
                display: inline-flex;
                margin: 8.4px;

                width: 100px;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .series-item img {
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



        .letest-series {
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

        h2#series-heading {
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

        .series-headingg {
            color: black;
        }


        .latest-series-image {
            width: 100%;
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

        .latest-series-image {
            width: 100%;
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

        .latest-series-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        p {
            margin: 0;
            text-align: center;
        }

        .scrollable-row {
            overflow-x: auto;
            white-space: nowrap;
        }

        .scrollable-container {
            display: inline-flex;
            flex-wrap: nowrap;
        }

        .latest-series-item {
            flex: 0 0 auto;
            margin-right: 10px;
            /* Adjust as needed */
            vertical-align: top;
            /* Align items to the top */
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