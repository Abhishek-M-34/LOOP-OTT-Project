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

    //
    // 🔴 Check if prepare() failed
    if (!$stmt) {
        die("SQL Prepare Error: " . $mysqli->error); // Debugging output
    }
    //


    // If a search query is provided, bind the search parameter
    if ($searchQuery !== null) {
        $searchParam = "%$searchQuery%";
        $stmt->bind_param('ssii', $searchParam, $searchParam, $offset, $limit);
    } else {
        $stmt->bind_param('ii', $offset, $limit);
    }

    $stmt->execute();//
    if (!$stmt->execute()) {
        die("Execute Error: " . $stmt->error);
    }//

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
        <link rel="stylesheet" href="../css/styles.css">
        <!-- <link rel="stylesheet" href="../css/video_management.css"> -->
    </head>
    <body>
        <header>
            <img src="../logo.png" alt="Your Logo">
            <nav>
                <ul>
                    <!-- Add navigation links -->
                    <li><a href="admin_home.php"><i class='bx bxs-home-alt-2'></i></a></li>
                    <li><a href="upload.html"><i class='bx bx-upload'></i></a></li>
                    <li><a href="user.php"><i class='bx bxs-user'></i></i></a></li>
                    <li><a href="video_management.php"><i class='bx bxs-videos'></i></i></a></li>
                    <li><a href="payment_managemant.php"><i class='bx bxs-purchase-tag'></i></i></i></a></li>
                    <!-- <li><a href="ad_upload.php"><i class='bx bx-cloud-upload'></i></a></li> -->
                    <li><a href="../signout.php"><i class='bx bx-log-out-circle'></i></a></li>
                </ul>
            </nav>
        </header>

        <main class="table" id="customers_table">
            <section class="table__header">
                <h1>Video Management</h1>
                <button class="toggle-button" onclick="switchToSeriesManagement()">Go To Series Management</button>
                <div class="input-group">
                    <input type="search" placeholder="Search Data...">
                    <img src="images/search.png" alt="">
                </div>
                <!-- <div class="export__file">
                    <label for="export-file" class="export__file-btn" title="Export File"></label>
                    <input type="checkbox" id="export-file">
                    <div class="export__file-options">
                        <label>Export As &nbsp; &#10140;</label>
                        <label for="export-file" id="toJSON">JSON <img src="images/json.png" alt=""></label>
                        <label for="export-file" id="toCSV">CSV <img src="images/csv.png" alt=""></label>
                    </div>
                </div> -->

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
            //switch to series management
            function switchToSeriesManagement() {
                    window.location.href = 'series_man.php';
                }

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
                    xhr.open('POST', 'video_delete.php', true);
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
        <script src="script.js"></script>
    </body>

    </html>

<?php
} else {
    echo '<p>No videos found.</p>';
}

$mysqli->close();
?>
