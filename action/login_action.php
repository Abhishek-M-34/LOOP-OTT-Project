<?php
# Editied by Amish
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $dbname = "ott";
    $username = "root";
    $password = "";

    try {
        // Establishing the MySQL database connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL query to fetch user details
        $stmt = $pdo->prepare("SELECT * FROM `User` WHERE `email` = :email");
        $stmt->bindParam(':email', $_POST['username']);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the provided password against the hashed password in the database
            if (password_verify($_POST['password'], $user['password'])) {
                // Check if the email belongs to the admin
                if ($user['email'] === 'amish@gmail.com') {
                    $_SESSION['user_email'] = $user['email'];
                    header("Location: ../admin/admin_home.php");
                    exit();
                } else {
                    $_SESSION['user_email'] = $user['email'];
                    header("Location: ../home.php");
                    exit();
                }
            } else {
                // Incorrect password - Redirect to login page with error message
                header("Location: ../login.php?error=Incorrect password. Please try again.");
                exit();
            }
        } else {
            // User not found - Redirect to login page with error message
            header("Location: ../login.php?error=Incorrect email or user not found. Please check your credentials.");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
