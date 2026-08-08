<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Journey Sharing</title>
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
                <?php if ($loggedIn): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main>
        <section class="hero">
            <h2>Welcome to Travel Journey Sharing</h2>
            <p>Share your travel experiences, upload photos, videos, and GPX tracks, and connect with fellow travelers.</p>
            <?php if (!$loggedIn): ?>
                <a href="register.php" class="btn">Get Started</a>
            <?php else: ?>
                <a href="create_journey.php" class="btn">Create New Journey</a>
            <?php endif; ?>
        </section>

        <section class="features">
            <h3>Features</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <h4>Share Journeys</h4>
                    <p>Create and share your travel stories with photos, videos, and maps.</p>
                </div>
                <div class="feature-card">
                    <h4>Interactive Maps</h4>
                    <p>Upload GPX tracks and view them on interactive maps.</p>
                </div>
                <div class="feature-card">
                    <h4>Community</h4>
                    <p>Like, comment, and connect with other travelers.</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>