<?php
session_start();
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$journeyId = (int)$_GET['id'];
$loggedIn = isset($_SESSION['user_id']);
$userId = $loggedIn ? $_SESSION['user_id'] : null;

// Get journey details
$stmt = $pdo->prepare("
    SELECT j.*, u.username
    FROM journeys j
    JOIN users u ON j.user_id = u.id
    WHERE j.id = ?
");
$stmt->execute([$journeyId]);
$journey = $stmt->fetch();

if (!$journey) {
    header("Location: index.php");
    exit;
}

// Get media
$stmt = $pdo->prepare("SELECT * FROM media WHERE journey_id = ? ORDER BY id");
$stmt->execute([$journeyId]);
$media = $stmt->fetchAll();

// Get comments
$stmt = $pdo->prepare("
    SELECT c.*, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.journey_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$journeyId]);
$comments = $stmt->fetchAll();

// Get likes count
$stmt = $pdo->prepare("SELECT COUNT(*) as likes FROM likes WHERE journey_id = ?");
$stmt->execute([$journeyId]);
$likesCount = $stmt->fetch()['likes'];

// Check if user liked
$liked = false;
if ($loggedIn) {
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE journey_id = ? AND user_id = ?");
    $stmt->execute([$journeyId, $userId]);
    $liked = $stmt->fetch() ? true : false;
}

// Check for GPX
$gpxFile = null;
foreach ($media as $m) {
    if ($m['type'] === 'gpx') {
        $gpxFile = $m['file_path'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($journey['title']); ?> - Travel Journey</title>
    <link rel="stylesheet" href="style.css">
    <?php if ($gpxFile): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-gpx@1.7.0/gpx.js"></script>
    <?php endif; ?>
</head>
<body>
    <nav>
        <div class="nav-container">
            <h1>Travel Journey</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if ($loggedIn): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main>
        <div class="journey-details">
            <h2><?php echo htmlspecialchars($journey['title']); ?></h2>
            <p>By: <?php echo htmlspecialchars($journey['username']); ?> | Created: <?php echo date('Y-m-d', strtotime($journey['created_at'])); ?></p>
            <p><?php echo htmlspecialchars($journey['description']); ?></p>

            <div class="media-gallery">
                <?php foreach ($media as $m): ?>
                    <?php if ($m['type'] === 'photo'): ?>
                        <img src="<?php echo htmlspecialchars($m['file_path']); ?>" alt="Journey photo">
                    <?php elseif ($m['type'] === 'video'): ?>
                        <video controls>
                            <source src="<?php echo htmlspecialchars($m['file_path']); ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($gpxFile): ?>
                <div id="map" style="height: 400px;"></div>
                <script>
                    const map = L.map('map').setView([51.505, -0.09], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    new L.GPX("<?php echo htmlspecialchars($gpxFile); ?>", {
                        async: true
                    }).on('loaded', function(e) {
                        map.fitBounds(e.target.getBounds());
                    }).addTo(map);
                </script>
            <?php endif; ?>

            <div class="likes-comments">
                <div class="likes">
                    <span id="likeCount"><?php echo $likesCount; ?> likes</span>
                    <?php if ($loggedIn): ?>
                        <button id="likeBtn" class="btn small" data-liked="<?php echo $liked ? '1' : '0'; ?>">
                            <?php echo $liked ? 'Unlike' : 'Like'; ?>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="comments">
                    <h3>Comments</h3>
                    <?php if ($loggedIn): ?>
                        <form id="commentForm">
                            <textarea id="commentText" placeholder="Add a comment..." required></textarea>
                            <button type="submit" class="btn small">Post Comment</button>
                        </form>
                    <?php endif; ?>
                    <div id="commentsList">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                <?php echo htmlspecialchars($comment['comment']); ?>
                                <small><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        <?php if ($loggedIn): ?>
        // Like functionality
        document.getElementById('likeBtn').addEventListener('click', function() {
            const liked = this.dataset.liked === '1';
            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ journey_id: <?php echo $journeyId; ?>, action: liked ? 'unlike' : 'like' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('likeCount').textContent = data.likes + ' likes';
                    this.textContent = data.liked ? 'Unlike' : 'Like';
                    this.dataset.liked = data.liked ? '1' : '0';
                }
            });
        });

        // Comment functionality
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const comment = document.getElementById('commentText').value;
            fetch('comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ journey_id: <?php echo $journeyId; ?>, comment: comment })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple reload for now
                }
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>