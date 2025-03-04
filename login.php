<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix-like Login Page</title>
    <link rel="stylesheet" type="text/css" href="style.css">

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

        .login-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
        }

        .login-form {
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

        .login-button {
            background: #3d2fea;
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }

        .login-button:hover {
            background-color: #b20710;
        }

        .forgot-password {
            margin-top: 15px;
            font-size: 0.8rem;
            color: #ccc;
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

        img.logo {
            max-width: 80px;
            /* Adjust the width as needed */
            position: fixed;
            top: 10px;
            /* Adjust the top position */
            left: 10px;
            /* Adjust the left position */
            z-index: 1000;
            /* Ensure it's on top of other elements */
        }
    </style>
</head>

<body>
    <img src="OIP.png" alt="Your Logo" class="logo">

    <div class="login-container">
        <h1>Login</h1>
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
