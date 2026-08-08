<?php
require_once 'config/db.php';

// Get all GPX files
$stmt = $pdo->query("
    SELECT m.file_path, j.title, u.username
    FROM media m
    JOIN journeys j ON m.journey_id = j.id
    JOIN users u ON j.user_id = u.id
    WHERE m.type = 'gpx'
    ORDER BY m.id DESC
");
$gpxFiles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map Overview - Travel Journey</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-gpx@1.7.0/gpx.js"></script>
</head>
<body>
    <nav>
        <div class="nav-container">
            <h1>Travel Journey</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="video_page.php">Videos</a></li>
                <li><a href="map_overview.php">Maps</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="map-overview">
            <h2>All Journey Tracks</h2>
            <div id="map" style="height: 600px;"></div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script>
        const map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        <?php foreach ($gpxFiles as $gpx): ?>
        new L.GPX("<?php echo htmlspecialchars($gpx['file_path']); ?>", {
            async: true,
            polyline_options: {
                color: 'red',
                opacity: 0.75,
                weight: 3
            },
            marker_options: {
                startIconUrl: null,
                endIconUrl: null,
                shadowUrl: null
            }
        }).on('loaded', function(e) {
            // Fit bounds if first track
        }).addTo(map);
        <?php endforeach; ?>
    </script>

    <script src="script.js"></script>
</body>
</html>