<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(to left, #ffffff, #3d2fea);
            

            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
        }

        img.logo {
            max-width: 80px; /* Adjust the width as needed */
            position: fixed;
            top: 10px; /* Adjust the top position */
            left: 10px; /* Adjust the left position */
            z-index: 1000; /* Ensure it's on top of other elements */
        }

        .signup-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .signup-container h1 {
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
        }

        .signup-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: none;
            border-bottom: 2px solid #e50914;
            background-color: #333;
            color: #fff;
            margin-top: 8px;
            font-size: 1rem;
        }

        .signup-button {
            background-color: #3d2fea;
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }

        .signup-button:hover {
            background-color: #b20710;
        }

        .login-link {
            margin-top: 15px;
            font-size: 0.8rem;
            color: #ccc;
        }

        .form-group input[type="tel"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: none;
            border-bottom: 2px solid #e50914;
            background-color: #333;
            color: #fff;
            margin-top: 8px;
            font-size: 1rem;
        }
        
        .alert-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #e50914;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
    </style>
</head>

<body>
    <!-- Your logo at the top corner -->
    <img src="OIP.png" alt="Your Logo" class="logo">

    <div class="signup-container">
        <h1>Sign Up</h1>
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
