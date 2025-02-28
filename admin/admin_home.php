<?php
session_start();
// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php"); // Redirect to your login page
    exit();
}

// Database connection details
$host = "localhost";
$dbname = "ott"; // Ensure your XAMPP database matches this name
$username = "root";
$password = "";

// Create connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of movies from the links table
$sql_movies = "SELECT COUNT(*) AS total_movies FROM links";
$result_movies = $conn->query($sql_movies);
if (!$result_movies) {
    die("Error executing query: " . $conn->error);
}
$row_movies = $result_movies->fetch_assoc();
$total_movies = $row_movies['total_movies'];

// Fetch total number of series from the series table
$sql_series = "SELECT COUNT(*) AS total_series FROM series";
$result_series = $conn->query($sql_series);
if (!$result_series) {
    die("Error executing query: " . $conn->error);
}
$row_series = $result_series->fetch_assoc();
$total_series = $row_series['total_series'];

// Fetch total number of users from the user table
$sql_users = "SELECT COUNT(*) AS total_users FROM user";
$result_users = $conn->query($sql_users);
if (!$result_users) {
    die("Error executing query: " . $conn->error);
}
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];

// Fetch total number of premium users from the user table where the account_type is 'premium'
$sql_premium_users = "SELECT COUNT(*) AS total_premium_users FROM user WHERE account_type = 'premium'";
$result_premium_users = $conn->query($sql_premium_users);
if (!$result_premium_users) {
    die("Error executing query: " . $conn->error);
}
$row_premium_users = $result_premium_users->fetch_assoc();
$total_premium_users = $row_premium_users['total_premium_users'];

// Fetch total revenue from payment_request table where status is accepted
$sql_revenue = "SELECT SUM(price) AS total_revenue FROM payment_request WHERE status = 'accepted'";
$result_revenue = $conn->query($sql_revenue);
if (!$result_revenue) {
    die("Error executing query: " . $conn->error);
}
$row_revenue = $result_revenue->fetch_assoc();
$total_revenue = $row_revenue['total_revenue'] ?? 0; // Default to 0 if no revenue
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

        body {
            font-family: 'Arial', sans-serif;
            color: #fff;
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

        nav ul li a i {
            font-size: 24px;
            /* Adjust the font size as needed */
        }

        /* Style the main content section */
        main {
            padding: 20px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .a {
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-basis: 48%;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-basis: 48%;
            margin-top: 20px;
        }

        .card h2 {
            margin-bottom: 10px;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
        }


        .card1 {
            background: linear-gradient(to right, #ff6a00, #ee0979);
            /* Gradient from orange to pink */
        }

        .card2 {
            background: linear-gradient(to right, #4CAF50, #2196F3);
            /* Gradient from green to blue */
        }

        .card3 {
            background: linear-gradient(to right, #FFC107, #FF5722);
            /* Gradient from yellow to orange */
        }

        .card4 {
            background: linear-gradient(to right, #9C27B0, #673AB7);
            /* Gradient from purple to indigo */
        }

        .card5 {
            background: linear-gradient(to right, #FFC107, #FF9800);
            /* Gradient from yellow to amber */
        }

        .card {
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-basis: 48%;
            color: #fff;
            /* Set text color to white */
            text-align: center;
            /* Center align text */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .a:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>

<body>
    <header>
        <!-- Add your logo and navigation here -->
        <img src="../your-logo.png" alt="Your Logo">
        <nav>
            <ul>
                <!-- Add navigation links -->
                <li><a href="admin_home.php"><i class='bx bxs-home-alt-2'></i></a></li>
                <li><a href="upload.html"><i class='bx bx-upload'></i></a></li>
                <li><a href="user.php"><i class='bx bxs-user'></i></i></a></li>
                <li><a href="video_management.php"><i class='bx bxs-videos'></i></i></a></li>
                <li><a href="payment_managemant.php"><i class='bx bxs-purchase-tag'></i></i></i></a></li>
                <li><a href="ad_upload.php"><i class='bx bx-cloud-upload'></i></a></li>
                <li><a href="../signout.php"><i class='bx bx-log-out-circle'></i></a></li>
            </ul>
        </nav>
    </header>   
    <div class="row">
        <a class="a" href="video_management.php" style="text-decoration: none; color: inherit;">
            <div class="card card1">
                <h2>Total Movies</h2>
                <p><?php echo $total_movies; ?></p>
            </div>
        </a>
        <a class="a" href="series_man.php" style="text-decoration: none; color: inherit;">

            <div class="card card2">
                <h2>Total Series</h2>
                <p><?php echo $total_series; ?></p>
            </div>
        </a>

    </div>
    <div class="row">
        <a class="a" href="user.php" style="text-decoration: none; color: inherit;">
            <div class="card card3">
                <h2>Total Users</h2>
                <p><?php echo $total_users; ?></p>
            </div>
        </a>
        <a class="a" href="payment_managemant.php" style="text-decoration: none; color: inherit;">

            <div class="card card4">
                <h2>Total Premium Users</h2>
                <p><?php echo $total_premium_users; ?></p>
            </div>
        </a>
        
    </div>
    <div class="roww">
        <div class="card card5">
            <h2>Total Revenue</h2>
            
            <p>₹ <?php echo $total_revenue; ?></p>
        </div>
    </div>
    </main>
    <script src="script.js"></script>

</body>

</html>