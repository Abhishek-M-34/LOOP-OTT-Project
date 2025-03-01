<?php
# Edited by Amish
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Database configuration
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch payment requests from the database
$sql = "SELECT * FROM payment_request";
$result = $conn->query($sql);
$payment_requests = $result->fetch_all(MYSQLI_ASSOC);

// Process payment requests
foreach ($payment_requests as $payment_request) {
    if ($payment_request['status'] == 'Accepted') {
        // Calculate premium end time
        $payment_time = strtotime($payment_request['payment_time']);
        $selected_plan = $payment_request['plan'];
        $premium_end_time = calculatePremiumEndTime($payment_time, $selected_plan);
        $remaining_days = calculateRemainingDays($premium_end_time);

        // Update user account type, premium end time, and remaining days
        updateUserAccountType($conn, $payment_request['user_email'], $premium_end_time, $remaining_days);

        // Update payment request table with premium end time
        $stmt = $conn->prepare("UPDATE payment_request SET premium_end_time = ? WHERE id = ?");
        $stmt->bind_param("si", $premium_end_time, $payment_request['id']);
        $stmt->execute();
    }
}

// Handle form submission for accepting or declining payments
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['payment_id'])) {
        $payment_id = $_POST['payment_id'];
        $action = $_POST['action'];

        if ($action === 'accept') {
            // Fetch the payment request from the database
            $stmt = $conn->prepare("SELECT * FROM payment_request WHERE id = ?");
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $payment_request = $result->fetch_assoc();

            if ($payment_request && $payment_request['status'] == 'Pending') {
                // Update the payment status to 'Accepted' and set accept time
                $current_time = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("UPDATE payment_request SET status = 'Accepted', payment_time = ? WHERE id = ?");
                $stmt->bind_param("si", $current_time, $payment_id);
                $stmt->execute();

                // Update the user's account type, premium end time, and remaining days
                $selected_plan = $payment_request['plan'];
                $premium_end_time = calculatePremiumEndTime(strtotime($current_time), $selected_plan);
                $remaining_days = calculateRemainingDays($premium_end_time);
                updateUserAccountType($conn, $payment_request['user_email'], $premium_end_time, $remaining_days);

                // Update the payment request table with premium end time
                $stmt = $conn->prepare("UPDATE payment_request SET premium_end_time = ? WHERE id = ?");
                $stmt->bind_param("si", $premium_end_time, $payment_id);
                $stmt->execute();
                echo "<script>alert('Payment accepted.');</script>";
            } else { 
                echo "<script>alert('Payment request not found or already accepted.');</script>";
            }
        }
    }
}

// Function to calculate premium end time based on selected plan
function calculatePremiumEndTime($payment_time, $selected_plan)
{
    switch ($selected_plan) {
        case '79':
            return date('Y-m-d H:i:s', strtotime('+30 days', $payment_time));
        case '229':
            return date('Y-m-d H:i:s', strtotime('+90 days', $payment_time));
        case '469':
            return date('Y-m-d H:i:s', strtotime('+180 days', $payment_time));
        case '709':
            return date('Y-m-d H:i:s', strtotime('+270 days', $payment_time));
        case '899':
            return date('Y-m-d H:i:s', strtotime('+365 days', $payment_time));
        default:
            return null;
    }
}

// Function to update user account type, premium end time, and remaining days
function updateUserAccountType($conn, $email, $premium_end_time, $remaining_days)
{
    $account_type = ($premium_end_time && strtotime($premium_end_time) > time()) ? 'Premium' : 'Basic';

    $stmt = $conn->prepare("UPDATE user SET account_type = ?, premium_end_time = ?, premium_duration = ? WHERE email = ?");
    $stmt->bind_param("ssss", $account_type, $premium_end_time, $remaining_days, $email);
    $stmt->execute();
}

// Function to calculate premium duration based on selected plan
function calculatePremiumDuration($selected_plan)
{
    switch ($selected_plan) {
        case '79':
            return '30 days';
        case '229':
            return '90 days';
        case '469':
            return '180 days';
        case '709':
            return '270 days';
        case '899':
            return '365 days';
        default:
            return null;
    }
}

// Function to calculate remaining premium days from the current date
function calculateRemainingDays($premium_end_time)
{
    $current_time = time();
    $premium_end_timestamp = strtotime($premium_end_time);
    $remaining_seconds = $premium_end_timestamp - $current_time;
    $remaining_days = floor($remaining_seconds / (60 * 60 * 24));
    return $remaining_days;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }



        button {
            padding: 8px 16px;
            cursor: pointer;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            text-transform: uppercase;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        button:active {
            background-color: #3e8e41;
        }

        header {
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        nav ul li a i {
            font-size: 24px;
        }

        main.table {
            width: 99vw;
            height: 90vh;
            background-color: #fff5;
            margin-top: 0.5%;
            margin-left: 0.5%;
            backdrop-filter: blur(7px);
            box-shadow: 0 0.4rem 0.8rem #0005;
            border-radius: .8rem;
            overflow: hidden;
        }
    </style>

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
    <main class="table" id="customers_table">
        <section class="table__header">
            <h1>Payment Management</h1>
            <div class="input-group">
                <input type="search" placeholder="Search Data...">
                <img src="images/search.png" alt="">
            </div>
            <div class="export__file">
                <label for="export-file" class="export__file-btn" title="Export File"></label>
                <input type="checkbox" id="export-file">
                <div class="export__file-options">
                    <label>Export As &nbsp; &#10140;</label>
                    <label for="export-file" id="toJSON">JSON <img src="images/json.png" alt=""></label>
                    <label for="export-file" id="toCSV">CSV <img src="images/csv.png" alt=""></label>
                </div>
            </div>
        </section>
        <section class="table__body">
            <table>
                <thead>
                    <tr>
                        <th>User Email</th>
                        <th>Selected Plan</th>
                        <th>Price</th>
                        <th>Payment Time</th>
                        <th>Premium Duration</th>
                        <th>Remaining Days</th>
                        <th>UTR Number</th>
                        <th>Status<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Accept</th>
                        <th>Decline</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payment_requests as $payment_request) : ?>
                        <tr>
                            <td><?php echo $payment_request['user_email']; ?></td>
                            <td><?php echo $payment_request['plan']; ?></td>
                            <td><?php echo $payment_request['price']; ?></td>
                            <td><?php echo $payment_request['payment_time']; ?></td>
                            <td><?php echo calculatePremiumDuration($payment_request['plan']); ?></td>
                            <td><?php echo calculateRemainingDays($payment_request['premium_end_time']); ?></td>
                            <td><?php echo $payment_request['utr_number']; ?></td>
                            <td><?php echo $payment_request['status']; ?></td>
                            <td>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="payment_id" value="<?php echo $payment_request['id']; ?>">
                                    <button type="submit" name="action" value="accept">Accept</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="payment_id" value="<?php echo $payment_request['id']; ?>">
                                    <button type="submit" name="action" value="decline">Decline</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script src="script.js"></script>

</body>

</html>
