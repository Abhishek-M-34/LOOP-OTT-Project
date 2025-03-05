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
        <style>
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
                max-width: 100%;
                margin: 0 auto;
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
                top: 0;
                width: 100%;
                z-index: 1000;
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

            /* Button styles */
            button {
                padding: 8px 15px;
                border: none;
                background-color: #007bff;
                color: #fff;
                cursor: pointer;
                border-radius: 4px;
                transition: background-color 0.3s;
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

            .de {
                padding: 8px;
                width: 100%;
                border-radius: 4px;
                border: 1px solid #ccc;
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
                <h1>Series Management</h1>
                <button class="toggle-button" onclick="switchToMovieManagement()">Go To Series Management</button>
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
                    xhr.open('POST', 'update_series.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            titleInput.disabled = true;
                            descriptionInput.disabled = true;
                            row.querySelector('button.save').style.display = 'none';
                            row.querySelector('button.edit').style.display = 'inline';
                        }
                    };

                    xhr.send('id=' + serieId + '&title=' + title + '&description=' + description);
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
