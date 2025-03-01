<?php
# Edited by Amish
// Start the session
session_start();

$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "ott";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $phone = $_POST["phone"];

    // Check if any of the form fields is empty
    if (empty($email) || empty($password) || empty($phone)) {
        // Redirect to index.php if any field is empty
        header("Location: ../index.php");
        exit();
    }

    // Validate password criteria
    $uppercase = preg_match('@[A-Z]@', $password);
    $numeric = preg_match('@[0-9]@', $password);
    $symbol = preg_match('@[^A-Za-z0-9]@', $password);

    // Check if password criteria are not met
    if (strlen($password) < 6 || !$uppercase || !$numeric || !$symbol) {
        echo "<script>alert('Password must be at least 6 characters long and contain at least one uppercase letter, one numeric character, and one symbol.');
        // Redirect to index.php as password criteria are not met
        window.location.href = '../index.php';
        </script>";
        exit();
    }

    // Check if the email already exists using a prepared statement
    $check_email_sql = "SELECT * FROM User WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, redirect to index.php
        echo "<script>alert('Email Already Exists.');
        window.location.href = '../index.php';
        </script>";
        exit();
    }
    $stmt->close();

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the User table using a prepared statement
    $insert_sql = "INSERT INTO User (email, password, mobile_number, account_type) VALUES (?, ?, ?, 'Basic')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sss", $email, $hashed_password, $phone);

    if ($stmt->execute()) {
        // Set session variables upon successful registration
        $_SESSION['user_email'] = $email;

        // Redirect to the dashboard or another page after registration
        header("Location: ../home.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
