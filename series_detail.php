<?php
# Edited by Amish
// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if series ID is provided in the URL
if (!isset($_GET['series_id']) || !is_numeric($_GET['series_id'])) {
    header("Location: home.php"); // Redirect to home page if series ID is not provided or invalid
    exit();
}

// Database configuration
$host = "localhost";
$dbname = "ott";
$username = "root";
$password = "";

// Initialize variables
$series_details = null;
$episodes = [];
$seasons = [];
$debug_info = [];

try {
    // Connect to the database using PDO for better error handling
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get series ID from URL parameter
    $seriesId = (int)$_GET['series_id'];
    
    // Fetch series details from the series table
    $stmt = $pdo->prepare("SELECT * FROM series WHERE series_id = ?");
    $stmt->execute([$seriesId]);
    $series_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If the series details are not found, handle it
    if (!$series_details) {
        throw new Exception("Series details not found for ID: $seriesId");
    }
    
    // First, get all seasons for this series to check if they exist
    $stmt = $pdo->prepare("SELECT id, season_number FROM seasons WHERE series_id = ? ORDER BY season_number");
    $stmt->execute([$seriesId]);
    $seasons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $debug_info['seasons_count'] = count($seasons);
    
    if (empty($seasons)) {
        $debug_info['warning'] = "No seasons found for this series";
    } else {
        // Fetch all episodes for this series, joining with seasons
        $stmt = $pdo->prepare("
            SELECT e.id, e.file_name, e.episode_number, s.season_number, s.id as season_id 
            FROM episodes e 
            JOIN seasons s ON e.season_id = s.id 
            WHERE s.series_id = ?
            ORDER BY s.season_number, e.episode_number
        ");
        $stmt->execute([$seriesId]);
        $episodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $debug_info['total_episodes'] = count($episodes);
        
        // Count episodes per season for debugging
        $episodesPerSeason = [];
        foreach ($episodes as $episode) {
            $season = $episode['season_number'];
            if (!isset($episodesPerSeason[$season])) {
                $episodesPerSeason[$season] = 0;
            }
            $episodesPerSeason[$season]++;
        }
        $debug_info['episodes_per_season'] = $episodesPerSeason;
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
    $debug_info['error'] = $error_message;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($series_details['title'] ?? 'Series Detail'); ?> - Series Detail</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
   <link rel="stylesheet" href="css/navbar.css">
   <link rel="stylesheet" href="css/background-animation.css">
   <link rel="stylesheet" href="css/series_detail.css">
</head>

<body>
    <header>
        <img src="logo.png" alt="Your Logo">
        <nav>
            <ul>
                <li><a href="home.php"><i class='bx bx-home'></i> Home</a></li>
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

    <main>
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <!-- Display series poster -->
            <div class="series-poster">
                <img src="<?php echo htmlspecialchars($series_details['image_path'] ?? 'default-image.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($series_details['title'] ?? 'Series Image'); ?>">
            </div>
            
            <!-- Display series description -->
            <div class="series-description">
                <h2><?php echo htmlspecialchars($series_details['title'] ?? 'Series Title'); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($series_details['description'] ?? 'No description available.')); ?></p>
                
                <!-- Display episode dropdown -->
                <div class="episode-selection">
                    <?php if (count($seasons) > 0): ?>
                        <label for="episodeSelect">Select Episode:</label>
                        <?php if (count($episodes) > 0): ?>
                            <select id="episodeSelect">
                                <?php 
                                $currentSeason = null;
                                foreach ($episodes as $episode): 
                                    // Start a new optgroup when we encounter a new season
                                    if ($currentSeason !== $episode['season_number']): 
                                        // Close previous optgroup if it exists
                                        if ($currentSeason !== null): ?>
                                            </optgroup>
                                        <?php endif; ?>
                                        <optgroup label="Season <?php echo htmlspecialchars($episode['season_number']); ?>" class="season-group">
                                        <?php $currentSeason = $episode['season_number']; 
                                    endif; ?>
                                    
                                    <option value="<?php echo htmlspecialchars($episode['file_name']); ?>" 
                                            data-episode-id="<?php echo htmlspecialchars($episode['id']); ?>"
                                            class="episode-option">
                                        Episode <?php echo htmlspecialchars($episode['episode_number']); ?>
                                    </option>
                                <?php endforeach; 
                                
                                // Close the last optgroup
                                if ($currentSeason !== null): ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            
                            <!-- Add watch series button -->
                            <button class="watch-series-btn" onclick="playEpisode()">Watch Episode</button>
                        <?php else: ?>
                            <p>No episodes available for this series. Seasons exist but no episodes are linked.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>No seasons available for this series yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Debug information section (hidden by default) -->
            <div class="debug-info" id="debugInfo">
                <h3>Debug Information</h3>
                <pre><?php echo json_encode($debug_info, JSON_PRETTY_PRINT); ?></pre>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // JavaScript function to play the selected episode
        function playEpisode() {
            var select = document.getElementById("episodeSelect");
            if (select && select.selectedIndex >= 0) {
                var selectedFileName = select.options[select.selectedIndex].value;
                // Check if the filename is not empty
                if (selectedFileName && selectedFileName.trim() !== '') {
                    // Redirect to the selected episode's file
                    window.open(selectedFileName, "_blank");
                } else {
                    alert("Invalid episode selected. Please try again.");
                }
            } else {
                alert("Please select an episode first.");
            }
        }
        
        // Auto-select the first episode on page load
        window.onload = function() {
            var select = document.getElementById("episodeSelect");
            if (select && select.options.length > 0) {
                select.selectedIndex = 0;
            }
            
            // Add toggle for debug info (for development)
            // Press Ctrl+D to toggle debug info visibility
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'd') {
                    e.preventDefault();
                    var debugInfo = document.getElementById('debugInfo');
                    debugInfo.style.display = debugInfo.style.display === 'none' ? 'block' : 'none';
                }
            });
        };
    </script>
</body>
</html>