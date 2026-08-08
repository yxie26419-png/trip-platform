<?php
require_once 'config/db.php';

// Get all photos
$stmt = $pdo->query("
    SELECT m.file_path, j.title, u.username
    FROM media m
    JOIN journeys j ON m.journey_id = j.id
    JOIN users u ON j.user_id = u.id
    WHERE m.type = 'photo'
    ORDER BY m.id DESC
");
$photos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Travel Journey</title>
    <link rel="stylesheet" href="style.css">
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
        <div class="gallery">
            <h2>Photo Gallery</h2>
            <div class="photo-grid">
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-item">
                        <img src="<?php echo htmlspecialchars($photo['file_path']); ?>" alt="<?php echo htmlspecialchars($photo['title']); ?>">
                        <p><?php echo htmlspecialchars($photo['title']); ?> by <?php echo htmlspecialchars($photo['username']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>