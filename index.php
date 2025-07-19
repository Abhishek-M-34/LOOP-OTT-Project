<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <!-- Your logo at the top corner -->
    <img src="logo.png" alt="Your Logo" class="logo">

    <div class="signup-container">
        <h1>SIGN UP</h1>
        <div class="alert-container" id="alertBox"></div>
        <form class="signup-form" action="action/signup_process.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="number" id="phone" name="phone" min="1000000000" max="9999999999" required>
            </div>
            <button type="submit" class="signup-button">Sign Up</button>
            <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST["password"];

        // Password validation criteria
        $uppercase = preg_match('@[A-Z]@', $password);
        $numeric = preg_match('@[0-9]@', $password);
        $symbol = preg_match('@[^A-Za-z0-9]@', $password);

        if (strlen($password) < 6 || !$uppercase || !$numeric || !$symbol) {
            echo "<script>alert('Password must be at least 6 characters long and contain at least one uppercase letter, one numeric character, and one symbol.');</script>";
            // You can redirect back to the sign-up page or handle the error as needed
        }
    }
    ?>
     <script>
        // Function to show the centered alert box
        function showAlert(message) {
            var alertBox = document.getElementById("alertBox");
            alertBox.innerHTML = message;
            alertBox.style.display = "block";

            // Hide the alert box after 3 seconds (adjust as needed)
            setTimeout(function () {
                alertBox.style.display = "none";
            }, 2000);
        }

        // Check if there's an error message in the URL parameters
        var errorMessage = "<?php echo isset($_GET['error']) ? $_GET['error'] : ''; ?>";
        if (errorMessage !== "") {
            showAlert(errorMessage);
        }
    </script>
</body>

</html>
