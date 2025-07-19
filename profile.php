<?php
# Edited by Amish
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Establishing connection to the database
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieving user data based on session email
$user_email = $conn->real_escape_string($_SESSION['user_email']); // Prevent SQL injection

// Prepare and execute the query
$sql = "SELECT * FROM user WHERE email = '$user_email'";
$result = $conn->query($sql);

$userData = [];

if ($result && $result->num_rows > 0) { // Check if $result is valid before accessing num_rows
    // Storing user data in an associative array
    $userData = $result->fetch_assoc();
} else {
    echo "<p>No results found for this user.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="css/background-animation.css">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/profile.css">
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


    <div class="container">
        <h1>User Profile</h1>
        <a id="si" href="signout.php" class="btn">Logout</a>
        <?php if (!empty($userData)) : ?>
            <div class="user-details">
                <p><strong>Email:</strong> <?php echo $userData['email']; ?></p>
                <p><strong>Mobile Number:</strong> <?php echo $userData['mobile_number']; ?></p>
                <p><strong>Account Type:</strong> <?php echo $userData['account_type']; ?></p>
                <p><strong>Premium End Time:</strong> <?php echo $userData['premium_end_time']; ?></p>
                <p><strong>Premium Duration:</strong> <?php echo $userData['premium_duration']; ?></p>
            </div>
        <?php endif; ?>

        <?php

        // Establish connection to the database
        $host = "localhost";
        $dbname = "ott";
        $username = "root";
        $password = "";

        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_email = $_SESSION['user_email'];
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Retrieve hashed password from the database
            $sql = "SELECT password FROM user WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stored_password = $row['password'];

                // Verify if the current password matches the stored hashed password
                if (password_verify($current_password, $stored_password)) {
                    // Check if the new password matches the confirm password
                    if ($new_password === $confirm_password) {
                        // Hash the new password before updating in the database
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Update the password in the database
                        $update_sql = "UPDATE user SET password = ? WHERE email = ?";
                        $stmt = $conn->prepare($update_sql);
                        $stmt->bind_param("ss", $hashed_password, $user_email);
                        if ($stmt->execute()) {
                            echo "<div class='success'>Password updated successfully.</div>";
                        } else {
                            echo "<div class='error'>Error updating password: " . $conn->error . "</div>";
                        }
                    } else {
                        echo "<div class='error'>New password and confirm password do not match.</div>";
                    }
                } else {
                    echo "<div class='error'>Current password is incorrect.</div>";
                }
            } else {
                echo "<div class='error'>User not found.</div>";
            }
        }

        $conn->close();
        ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="#" class="btn" onclick="showForgotPasswordForm()">Change Password</a>
        </div>

        <div id="forgotPasswordForm" style="display: none; margin-top: 20px;">
            <h2>Change Password</h2>

            <form method="post">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required><br><br>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required><br><br>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        function showForgotPasswordForm() {
            document.getElementById('forgotPasswordForm').style.display = 'block';
        }
    </script>
</body>

</html>
