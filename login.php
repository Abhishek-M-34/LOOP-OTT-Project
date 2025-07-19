<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <img src="logo.png" alt="Your Logo" class="logo">

    <div class="login-container">
        <h1>LOGIN</h1>
        <div class="alert-container" id="alertBox"></div>
        <form class="login-form" action="action/login_action.php" method="post">
            <div class="form-group">
                <label for="username">Email or phone number:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
            <p class="login-link">Create an account? <a href="index.php">Signup</a></p>

        </form>
    </div>
    <script>
        // Function to show the centered alert box
        function showAlert(message) {
            var alertBox = document.getElementById("alertBox");
            alertBox.innerHTML = message;
            alertBox.style.display = "block";

            // Hide the alert box after 3 seconds (adjust as needed)
            setTimeout(function() {
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
