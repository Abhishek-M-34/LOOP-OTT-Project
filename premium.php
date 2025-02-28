<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Perform necessary actions to upgrade the user's account to premium
    // This might include updating the database, setting session variables, etc.
    // Make sure to sanitize and validate user input to prevent SQL injection and other security vulnerabilities
    if (isset($_POST['plan'])) {
        // Database configuration
        $host = "localhost";
        $dbname = "ott";
        $username = "root";
        $password = "";

        try {
            // Establish database connection
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch user email from session
            $user_email = $_SESSION['user_email'];

            // Get the selected plan from the form
            $selected_plan = $_POST['plan'];

            // Initialize $utr_number
            $utr_number = '';

            // Get the UTR number based on the selected plan
            switch ($selected_plan) {
                case '79':
                    $utr_number = $_POST['utr_number_79'];
                    break;
                case '229':
                    $utr_number = $_POST['utr_number_229'];
                    break;
                case '469':
                    $utr_number = $_POST['utr_number_469'];
                    break;
                case '709':
                    $utr_number = $_POST['utr_number_709'];
                    break;
                case '899':
                    $utr_number = $_POST['utr_number_899'];
                    break;
                default:
                    // Handle invalid plan selection
                    die("Invalid plan selection.");
            }

            // Check if UTR number is empty
            if (empty($utr_number)) {
                die("UTR number is required.");
            }

            // Example: Check if UTR number contains only digits and is of a specific length
            if (!preg_match('/^\d{12}$/', $utr_number)) {
                die("Invalid UTR number format. UTR number should be 12 digits long.");
            }

            // Define the price based on the selected plan (you might need to adjust this based on your actual pricing)
            switch ($selected_plan) {
                case '79':
                    $price = 79.00;
                    break;
                case '229':
                    $price = 229.00;
                    break;
                case '469':
                    $price = 469.00;
                    break;
                case '709':
                    $price = 709.00;
                    break;
                case '899':
                    $price = 899.00;
                    break;
                default:
                    // Handle invalid plan selection
                    die("Invalid plan selection.");
            }

            // Define the payment time
            $payment_time = date('Y-m-d H:i:s');

            // Set the initial status for the payment request
            $status = 'Pending';

            // Prepare and execute the SQL statement to insert the payment request
            $stmt = $pdo->prepare("INSERT INTO payment_request (user_email, plan, price, payment_time, utr_number, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_email, $selected_plan, $price, $payment_time, $utr_number, $status]);

            // Redirect to a success page or display a success message
            header("Location: payment_success.php");
            exit();
        } catch (PDOException $e) {
            // Handle database connection errors
            die("Database error: " . $e->getMessage());
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade to Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card input[type="radio"] {
            display: none;
        }

        .card label {
            cursor: pointer;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .card input[type="radio"]:checked+label {
            border-color: #007bff;
            background-color: #f0faff;
        }

        .card input[type="radio"]:checked+label::after {
            content: '\f058';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: -10px;
            right: -10px;
            width: 30px;
            height: 30px;
            background-color: #007bff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card label:hover {
            border-color: #007bff;
            background-color: #f0faff;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding-top: 60px;
            /* Adjusted padding to accommodate the fixed header */
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header styles */
        header {
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            /* Fixed position */
            top: 0;
            /* Fixed to the top */
            width: 100%;
            /* Full width */
            z-index: 1000;
            /* Ensure it's above other content */
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

        nav ul li:hover::before {
            content: attr(title);
            position: absolute;
            background-color: #000;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
        }

        nav ul li a i {
            font-size: 24px;
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
            /* Adjust spacing between icon and text */
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

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 80px;
            /* Adjusted margin to prevent content from being hidden behind the fixed header */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Button styles */
        button {
            padding: 8px 15px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        button.save {
            background-color: #28a745;
        }

        button.save:hover {
            background-color: #218838;
        }

        button.delete {
            background-color: #dc3545;
        }

        button.delete:hover {
            background-color: #c82333;
        }

        /* Pagination styles */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            color: #007bff;
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        /* Search form styles */
        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form input[type="text"] {
            padding: 8px;
            width: 250px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 8px 15px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        .payment_qr_img img {
            display: flex;

            max-width: 50%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .container {
            margin-top: 300px;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to left, #ffffff, #3d2fea);

            /* Updated background */
            line-height: 1.6;
            margin: 0;
        }

        /* Hide scrollbar for Chrome, Safari, and Opera */
        ::-webkit-scrollbar {
            display: none;
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
    <div class="payment_qr_img"></div>

    <div class="container">
        <h1>Upgrade to Premium</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="card">
                <input type="radio" id="plan_79" name="plan" value="79">
                <label for="plan_79">
                    <h2>₹79 per month</h2>
                    <p>Watch premium movies and series with no ads (1 month)</p>
                </label>
                <!-- Input field for UTR number -->
                <div class="utr-input" style="display: none;" data-plan="79">
                    <label for="utr_number_79">Enter UTR Number:</label>
                    <input type="text" id="utr_number_79" name="utr_number_79">
                </div>
            </div>
            <div class="card">
                <input type="radio" id="plan_229" name="plan" value="229">
                <label for="plan_229">
                    <h2>₹229 for 3 months</h2>
                    <p>Watch premium movies and series with no ads (3 months)</p>
                </label>
                <!-- Input field for UTR number -->
                <div class="utr-input" style="display: none;" data-plan="229">
                    <label for="utr_number_229">Enter UTR Number:</label>
                    <input type="text" id="utr_number_229" name="utr_number_229">
                </div>
            </div>
            <div class="card">
                <input type="radio" id="plan_469" name="plan" value="469">
                <label for="plan_469">
                    <h2>₹469 for 6 months</h2>
                    <p>Watch premium movies and series with no ads (6 months)</p>
                </label>
                <!-- Input field for UTR number -->
                <div class="utr-input" style="display: none;" data-plan="469">
                    <label for="utr_number_469">Enter UTR Number:</label>
                    <input type="text" id="utr_number_469" name="utr_number_469">
                </div>
            </div>
            <div class="card">
                <input type="radio" id="plan_709" name="plan" value="709">
                <label for="plan_709">
                    <h2>₹709 for 9 months</h2>
                    <p>Watch premium movies and series with no ads (9 months)</p>
                </label>
                <!-- Input field for UTR number -->
                <div class="utr-input" style="display: none;" data-plan="709">
                    <label for="utr_number_709">Enter UTR Number:</label>
                    <input type="text" id="utr_number_709" name="utr_number_709">
                </div>
            </div>
            <div class="card">
                <input type="radio" id="plan_899" name="plan" value="899">
                <label for="plan_899">
                    <h2>₹899 for 1 year</h2>
                    <p>Watch premium movies and series with no ads (1 year)</p>
                </label>
                <!-- Input field for UTR number -->
                <div class="utr-input" style="display: none;" data-plan="899">
                    <label for="utr_number_899">Enter UTR Number:</label>
                    <input type="text" id="utr_number_899" name="utr_number_899">
                </div>
            </div>
            <button type="submit">Upgrade to Premium</button>
        </form>

    </div>

    <script>
        // Function to update the image and show/hide the UTR input field based on the selected plan
        function updateImageAndInput() {
            var planRadios = document.querySelectorAll('input[name="plan"]');
            var paymentQRImg = document.querySelector('.payment_qr_img');
            var utrInputs = document.querySelectorAll('.utr-input');

            planRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    var selectedPlan = this.value;
                    var imgSrc = 'QR/' + selectedPlan + '.jpg'; // Assuming images are located in 'QR' directory
                    paymentQRImg.innerHTML = '<img src="' + imgSrc + '" alt="QR Code">';

                    // Show/hide UTR input field based on the selected plan
                    utrInputs.forEach(function(input) {
                        var inputPlan = input.getAttribute('data-plan');
                        if (selectedPlan === inputPlan) {
                            input.style.display = 'block';
                        } else {
                            input.style.display = 'none';
                        }
                    });
                });
            });
        }

        // Call the function once the DOM is loaded
        document.addEventListener('DOMContentLoaded', updateImageAndInput);
    </script>
</body>

</html>