<?php
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
    <style>
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

        /* Set a background color and font for the entire page */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
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
        }

        nav ul li a i {
            font-size: 24px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 70%;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .search-results {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .result-item {
            width: 140px;
            /* Adjust width for both laptop and mobile */
            height: px;
            /* Adjust height for both laptop and mobile */
            margin: 25px;
            /* Adjust margin as needed */
        }

        .result-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            
        }

        .title {
            text-align: center;
            margin-top: 5px;
        }

        /* Media query for mobile devices */
        @media only screen and (max-width: 768px) {
            .result-item {
                width: calc(40% - 20px);
                /* Adjust width for mobile */
                height: 200px;
                margin: 25px;
                /* Adjust height for mobile */
            }
        }

        .s {
            margin-top: 10%;
        }

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
    <div class="container">
        <form method="POST" action="">
            <input type="text" class="s" name="search_query" placeholder="Search for movies and series" value="<?php echo $search_query; ?>">
            <button type="submit" name="search"><i class='bx bx-search'></i> Search</button>
        </form>
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