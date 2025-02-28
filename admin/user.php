<?php
session_start();
// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php"); // Redirect to your login page
    exit();
}

// Database connection details
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to fetch users with pagination and search
function fetchUsers($pdo, $offset, $limit, $searchQuery = '')
{
    $searchCondition = '';
    if (!empty($searchQuery)) {
        $searchCondition = " WHERE email LIKE :searchQuery OR mobile_number LIKE :searchQuery";
    }
    $stmt = $pdo->prepare("SELECT user_id, email, mobile_number, account_type FROM users $searchCondition LIMIT :offset, :limit");
    if (!empty($searchQuery)) {
        $stmt->bindValue(':searchQuery', "%$searchQuery%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Pagination parameters
$limit = 15; // Number of users per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Search query
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch users from the database
$users = fetchUsers($pdo, $offset, $limit, $searchQuery);

// Handle case where no users are retrieved
if (!$users) {
    // You can display a message or take any other appropriate action
    echo "No users found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
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

        /* Table and pagination styles */
        .table {
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
        <img src="../your-logo.png" alt="Your Logo">
        <nav>
            <ul>
                <!-- Add navigation links -->
                <li><a href="admin_home.php"><i class='bx bxs-home-alt-2'></i></a></li>
                <li><a href="upload.html"><i class='bx bx-upload'></i></a></li>
                <li><a href="user.php"><i class='bx bxs-user'></i></i></a></li>
                <li><a href="video_management.php"><i class='bx bxs-videos'></i></i></a></li>
                <li><a href="payment_management.php"><i class='bx bxs-purchase-tag'></i></i></i></a></li>
                <li><a href="ad_upload.php"><i class='bx bx-cloud-upload'></i></a></li>
                <li><a href="../signout.php"><i class='bx bx-log-out-circle'></i></a></li>
            </ul>
        </nav>
    </header>

    <main class="table" id="customers_table">
        <section class="table__header">
            <h1>User Detail</h1>
            <div class="input-group">
                <input type="search" id="searchInput" placeholder="Search Data...">
                <img src="images/search.png" alt="">
            </div>
        </section>
        <section class="table__body">
            <table>
                <thead>
                    <tr>
                        <th>User ID<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Email<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Phone Number<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Account Type<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Action<span class="icon-arrow">&UpArrow;</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr data-id="<?php echo $user['user_id']; ?>">
                            <td><?php echo $user['user_id']; ?></td>
                            <td class="email"><?php echo $user['email']; ?></td>
                            <td class="phone"><?php echo $user['mobile_number']; ?></td>
                            <td><?php echo $user['account_type']; ?></td>
                            <td>
                                <button class="edit" onclick="editUser(this)">Edit</button>
                                <button class="save" style="display:none;" onclick="saveUser(this)">Save</button>
                                <button class="delete" onclick="deleteUser(<?php echo $user['user_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (!empty($users)) : ?>
                <div class="pagination">
                    <a href="?page=<?php echo $page - 1; ?>" <?php if ($page == 1) echo "style='display:none;'" ?>>Previous</a>
                    <a href="?page=<?php echo $page + 1; ?>">Next</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        // Search and handle pagination
        document.getElementById('searchInput').addEventListener('input', function () {
            var searchQuery = this.value.trim();
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_users.php?search=' + encodeURIComponent(searchQuery), true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var userListContainer = document.querySelector('tbody');
                    userListContainer.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });

        function editUser(button) {
            var row = button.parentElement.parentElement;
            var userId = row.getAttribute('data-id');
            var emailCell = row.querySelector('.email');
            var phoneCell = row.querySelector('.phone');
            
            var email = emailCell.textContent;
            var phone = phoneCell.textContent;

            emailCell.innerHTML = '<input type="text" value="' + email + '">';
            phoneCell.innerHTML = '<input type="text" value="' + phone + '">';

            button.style.display = 'none';
            row.querySelector('button.save').style.display = 'inline';
        }

        function saveUser(button) {
            var row = button.parentElement.parentElement;
            var userId = row.getAttribute('data-id');
            var email = row.querySelector('.email input').value;
            var phone = row.querySelector('.phone input').value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText);
                }
            };

            var data = "userId=" + userId + "&email=" + encodeURIComponent(email) + "&phone=" + encodeURIComponent(phone);
            xhr.send(data);

            row.querySelector('.email').textContent = email;
            row.querySelector('.phone').textContent = phone;

            button.style.display = 'none';
            row.querySelector('button.edit').style.display = 'inline';
        }

        function deleteUser(userId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var row = document.querySelector('tr[data-id="' + userId + '"]');
                    row.parentNode.removeChild(row);
                }
            };
            var data = "userId=" + userId;
            xhr.send(data);
        }
    </script>

</body>

</html>
