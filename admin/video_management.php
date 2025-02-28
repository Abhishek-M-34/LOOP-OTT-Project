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

// Establish database connection using mysqli
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Function to fetch videos with pagination and search
function fetchVideos($mysqli, $offset, $limit, $searchQuery = null)
{
    // Base query
    $query = "SELECT id, title, video_link, image_link, description, current_status, uploading_date FROM links";

    // If a search query is provided, add the WHERE clause
    if ($searchQuery !== null) {
        $query .= " WHERE title LIKE ? OR description LIKE ?";
    }

    // Add LIMIT and OFFSET clauses for pagination
    $query .= " LIMIT ?, ?";

    // Prepare the statement
    $stmt = $mysqli->prepare($query);

    // If a search query is provided, bind the search parameter
    if ($searchQuery !== null) {
        $searchParam = "%$searchQuery%";
        $stmt->bind_param('ssii', $searchParam, $searchParam, $offset, $limit);
    } else {
        $stmt->bind_param('ii', $offset, $limit);
    }

    $stmt->execute();

    // Get result
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Pagination parameters
$limit = 15; // Number of videos per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get search query if provided
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;

// Fetch videos from the database
$videos = fetchVideos($mysqli, $offset, $limit, $searchQuery);

// Check if $videos is not null
if ($videos !== null && !empty($videos)) {
    // Proceed with displaying the videos
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - User Management</title>
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="style.css">
        
        <style>
            /* Reset CSS */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* Global styles */
            body {
                width: 100%;
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                color: #333;
                line-height: 1.6;
                padding-top: 60px;
                /* Adjusted padding to accommodate the fixed header */
            }

            .container {
                max-width: 95%;
                /* Adjusted max-width for better fit on smaller screens */
                margin: 0 auto;
                padding: 20px 10px;
                /* Adjusted padding for better spacing on smaller screens */
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
                width: 150px;
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

            .des {
                height: 30px;
                /* Set the height of the input field */
                width: 100%;
                /* Set the width of the input field to fill its container */
                padding: 8px;
                /* Add padding to provide space inside the input field */
                border: 1px solid #ccc;
                /* Add a border with a light gray color */
                border-radius: 4px;
                /* Add rounded corners to the input field */
                box-sizing: border-box;
                /* Ensure that padding and border are included in the width */
                font-size: 14px;
                /* Set the font size of the text in the input field */
                margin-top: 5px;
                /* Add some space at the top of the input field */
                margin-bottom: 5px;
                /* Add some space at the bottom of the input field */
            }

            /* Style for when the input field is focused */
            .des:focus {
                border-color: #007bff;
                /* Change the border color to blue when the input field is focused */
                outline: none;
                /* Remove the default focus outline */
            }

            .ti {
                height: 30px;
                /* Set the height of the input field */
                width: 100%;
                /* Set the width of the input field to fill its container */
                padding: 8px;
                /* Add padding to provide space inside the input field */
                border: 1px solid #ccc;
                /* Add a border with a light gray color */
                border-radius: 4px;
                /* Add rounded corners to the input field */
                box-sizing: border-box;
                /* Ensure that padding and border are included in the width */
                font-size: 14px;
                /* Set the font size of the text in the input field */
                margin-top: 5px;
                /* Add some space at the top of the input field */
                margin-bottom: 5px;
                /* Add some space at the bottom of the input field */
            }

            /* Style for when the input field is focused */
            .ti:focus {
                border-color: #007bff;
                /* Change the border color to blue when the input field is focused */
                outline: none;
                /* Remove the default focus outline */
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
                <h1>Video Management</h1>
                <button class="toggle-button" onclick="switchToMovieUpload()">Go To Series Management</button>
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
                            <th>ID<span class="icon-arrow">&UpArrow;</span></th>
                            <th>Title<span class="icon-arrow">&UpArrow;</span></th>
                            <th>Description<span class="icon-arrow">&UpArrow;</span></th>
                            <th>Edit</th> <!-- Separate column for Edit button -->
                            <th>Delete</th> <!-- Separate column for Delete button -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($videos as $video) : ?>
                            <tr data-id="<?php echo $video['id']; ?>">
                                <td><?php echo $video['id']; ?></td>
                                <td class="title"><?php echo $video['title']; ?></td>
                                <td class="description"><?php echo $video['description']; ?></td>
                                <td> <!-- Edit button column -->
                                    <button class="edit" onclick="editVideo(this)">Edit</button>
                                    <button class="save" style="display:none;" onclick="saveVideo(this)">Save</button>
                                </td>
                                <td> <!-- Delete button column -->
                                    <button class="delete" onclick="deleteVideo(this)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    // Get total number of records
                    $result = $mysqli->query("SELECT COUNT(id) AS total FROM links");
                    $totalRecords = $result->fetch_assoc()['total'];
                    $totalPages = ceil($totalRecords / $limit);

                    // Display pagination links
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo '<a href="?page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
                    }
                    ?>
                </div>

            </section>

        </main>

        <script>
            // JavaScript for edit/save functionality
            function editVideo(button) {
                var row = button.closest('tr');
                var titleCell = row.querySelector('.title');
                var descriptionCell = row.querySelector('.description');
                var saveButton = row.querySelector('.save');
                var editButton = row.querySelector('.edit');

                // Convert text to input fields for editing
                var titleInput = document.createElement('input');
                titleInput.type = 'text';
                titleInput.value = titleCell.textContent;

                var descriptionInput = document.createElement('textarea');
                descriptionInput.value = descriptionCell.textContent;

                titleCell.innerHTML = '';
                descriptionCell.innerHTML = '';
                titleCell.appendChild(titleInput);
                descriptionCell.appendChild(descriptionInput);

                // Toggle visibility of buttons
                saveButton.style.display = 'inline';
                editButton.style.display = 'none';
            }

            function saveVideo(button) {
                var row = button.closest('tr');
                var titleCell = row.querySelector('.title');
                var descriptionCell = row.querySelector('.description');
                var saveButton = row.querySelector('.save');
                var editButton = row.querySelector('.edit');

                // Get new values from input fields
                var newTitle = titleCell.querySelector('input').value;
                var newDescription = descriptionCell.querySelector('textarea').value;

                // Make an AJAX request to save the updated values
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'save_video.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Update the cells with the new values
                        titleCell.textContent = newTitle;
                        descriptionCell.textContent = newDescription;

                        // Toggle buttons
                        saveButton.style.display = 'none';
                        editButton.style.display = 'inline';
                    }
                };
                xhr.send('id=' + row.getAttribute('data-id') + '&title=' + newTitle + '&description=' + newDescription);
            }

            function deleteVideo(button) {
                var row = button.closest('tr');
                var videoId = row.getAttribute('data-id');

                if (confirm('Are you sure you want to delete this video?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete_video.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            // Remove the row from the table
                            row.remove();
                        }
                    };
                    xhr.send('id=' + videoId);
                }
            }
        </script>

    </body>

    </html>

<?php
} else {
    echo '<p>No videos found.</p>';
}

$mysqli->close();
?>
