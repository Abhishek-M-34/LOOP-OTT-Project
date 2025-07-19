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

// Function to fetch series with pagination and search
function fetchSeries($pdo, $offset, $limit, $searchQuery = null)
{
    // Base query
    $query = "SELECT 
                series.series_id, 
                series.title, 
                series.description, 
                series.current_status, 
                series.uploading_date, 
                seasons.id AS season_id,
                COUNT(episodes.id) AS episode_count
              FROM 
                `series`
              LEFT JOIN 
                `seasons` ON series.series_id = seasons.series_id
              LEFT JOIN 
                `episodes` ON seasons.id = episodes.season_id";
    
    // If a search query is provided, add the WHERE clause
    if ($searchQuery !== null) {
        $query .= " WHERE series.title LIKE :searchQuery OR series.description LIKE :searchQuery";
    }

    // Add LIMIT and OFFSET clauses for pagination
    $query .= " GROUP BY series.series_id LIMIT :offset, :limit";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    // If a search query is provided, bind the search parameter
    if ($searchQuery !== null) {
        $searchParam = "%$searchQuery%";
        $stmt->bindParam(':searchQuery', $searchParam, PDO::PARAM_STR);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create update_series.php functionality directly in this file
if (isset($_POST['update_series'])) {
    try {
        // Get and sanitize input
        $serieId = intval($_POST['id']);
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);

        // Update series information in the database
        $stmt = $pdo->prepare("UPDATE series SET title = :title, description = :description WHERE series_id = :id");
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $serieId, PDO::PARAM_INT);
        $stmt->execute();
        
        echo "Series updated successfully";
        exit;
    } catch (PDOException $e) {
        die("Error updating series: " . $e->getMessage());
    }
}

// Pagination parameters
$limit = 25; // Number of series per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get search query if provided
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;

// Fetch series from the database
$series = fetchSeries($pdo, $offset, $limit, $searchQuery);

// Check if $series is not null
if ($series !== null && !empty($series)) {
    // Proceed with displaying the series
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Series Management</title>
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="style.css">
        <link rel="stylesheet" href="../css/styles.css">
        <!-- <link rel="stylesheet" href="../css/series_man.css"> -->
    </head>

    <body>
        <header>
            <!-- Add your logo and navigation here -->
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
                <h1>Series Management</h1>
                <button class="toggle-button" onclick="switchToMovieManagement()">Go To Video Management</button>
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
                            <th>Season ID<span class="icon-arrow">&UpArrow;</span></th>
                            <th>Title<span class="icon-arrow">&UpArrow;</span></th>
                            <th>Description</th>
                            <th>Current status</th>
                            <th>Uploding Date</th>
                            <th>Number Of Episodes</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($series as $serie) : ?>
                            <tr data-id="<?php echo $serie['series_id']; ?>">
                                <td><?php echo $serie['series_id']; ?></td>
                                <td><?php echo $serie['season_id']; ?></td>
                                <td><?php echo $serie['title']; ?></td>
                                <td><?php echo $serie['description']; ?></td>
                                <td><?php echo $serie['current_status']; ?></td>
                                <td><?php echo $serie['uploading_date']; ?></td>
                                <td><?php echo $serie['episode_count']; ?></td>
                                <td>
                                    <button class="edit" onclick="editSerie(this)">Edit</button>
                                    <button class="save" style="display:none;" onclick="saveSerie(this)">Save</button>
                                </td>
                                <td>
                                    <button class="delete" onclick="deleteSerie(<?php echo $serie['series_id']; ?>)">Delete</button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!empty($series)) : ?>
                    <div class="pagination">
                        <a href="?page=<?php echo $page - 1 . ($searchQuery !== null ? '&search=' . urlencode($searchQuery) : ''); ?>" <?php if ($page == 1) echo "style='display:none;'" ?>>Previous</a> <span>
                            <a href="?page=<?php echo $page + 1 . ($searchQuery !== null ? '&search=' . urlencode($searchQuery) : ''); ?>">Next</a>
                    </div>
                <?php endif; ?>
            </section>

            <script>
                function switchToMovieManagement() {
                    window.location.href = 'video_management.php';
                }

                function editSerie(button) {
                    var row = button.parentElement.parentElement;
                    var titleCell = row.querySelector('td:nth-child(3)');
                    var descriptionCell = row.querySelector('td:nth-child(4)');

                    // Check if input fields already exist, if so, update their values
                    var titleInput = titleCell.querySelector('input[type="text"]');
                    var descriptionInput = descriptionCell.querySelector('input[type="text"]');

                    if (!titleInput) {
                        var title = titleCell.textContent.trim();
                        titleCell.innerHTML = '<input type="text" class="de" value="' + title + '">';
                    }

                    if (!descriptionInput) {
                        var description = descriptionCell.textContent.trim();
                        descriptionCell.innerHTML = '<input type="text" class="de" value="' + description + '">';
                    }
                       
                    button.style.display = 'none';
                    row.querySelector('button.save').style.display = 'inline';
                    console.log("Edit button clicked");
                }

                function saveSerie(button) {
                    var row = button.parentElement.parentElement;
                    var serieId = row.getAttribute('data-id'); // Retrieve serie ID using data-id attribute
                    var titleInput = row.querySelector('td:nth-child(3) input[type="text"]');
                    var descriptionInput = row.querySelector('td:nth-child(4) input[type="text"]');
                    var title = titleInput.value;
                    var description = descriptionInput.value;

                    // Send an AJAX request to update the series
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', window.location.href, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            // Update the table cells back to text
                            var titleCell = row.querySelector('td:nth-child(3)');
                            var descriptionCell = row.querySelector('td:nth-child(4)');
                            
                            titleCell.textContent = title;
                            descriptionCell.textContent = description;
                            
                            // Show edit button and hide save button
                            row.querySelector('button.save').style.display = 'none';
                            row.querySelector('button.edit').style.display = 'inline';
                        }
                    };

                    xhr.send('update_series=1&id=' + serieId + '&title=' + encodeURIComponent(title) + '&description=' + encodeURIComponent(description));
                }

                function deleteSerie(serieId) {
                    
                    // Send an AJAX request to delete the series
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'serie_delete.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            location.reload();
                        }
                    };
                    xhr.send('id=' + serieId);
                }
            </script>
            <script src="script.js"></script>
        </main>
    </body>

    </html>
<?php } ?>