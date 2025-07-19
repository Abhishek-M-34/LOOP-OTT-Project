<?php
# Edited by Amish
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
    <link rel="stylesheet" href="css/premium.css">
    <link rel="stylesheet" href="css/background-animation.css">
    <link rel="stylesheet" href="css/navbar.css">
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
    <div class="cinema-particles">
        <div class="cinema-particle"></div>
        <div class="cinema-particle"></div>
        <div class="cinema-particle"></div>
        <div class="cinema-particle"></div>
    </div>
    
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
