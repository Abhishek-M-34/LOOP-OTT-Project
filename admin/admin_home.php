<?php
//edited by Abhishek
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
    <link rel="stylesheet" href="../css/admin_home.css">

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
    <main>


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
</body>
</html>
