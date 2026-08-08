<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user preference for layout
$layout = isset($_COOKIE['layout']) ? $_COOKIE['layout'] : 'grid';

// Query journeys
$stmt = $pdo->prepare("
    SELECT j.id, j.title, j.description, j.created_at, COUNT(m.id) as media_count
    FROM journeys j
    LEFT JOIN media m ON j.id = m.journey_id
    WHERE j.user_id = ?
    GROUP BY j.id
    ORDER BY j.created_at DESC
");
$stmt->execute([$userId]);
$journeys = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Travel Journey</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <h1>Travel Journey</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="create_journey.php">Create Journey</a></li>
                <li><a href="preference.php">Preferences</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="dashboard-container">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <div class="dashboard-actions">
                <a href="create_journey.php" class="btn">Create New Journey</a>
            </div>

            <div class="layout-toggle">
                <label>Layout: </label>
                <select id="layoutSelect">
                    <option value="grid" <?php echo $layout === 'grid' ? 'selected' : ''; ?>>Grid</option>
                    <option value="list" <?php echo $layout === 'list' ? 'selected' : ''; ?>>List</option>
                </select>
            </div>

            <div class="journeys <?php echo $layout; ?>">
                <?php if (empty($journeys)): ?>
                    <p>No journeys yet. <a href="create_journey.php">Create your first journey!</a></p>
                <?php else: ?>
                    <?php foreach ($journeys as $journey): ?>
                        <div class="journey-card">
                            <h3><?php echo htmlspecialchars($journey['title']); ?></h3>
                            <p><?php echo htmlspecialchars($journey['description']); ?></p>
                            <p>Created: <?php echo date('Y-m-d', strtotime($journey['created_at'])); ?></p>
                            <p>Photos: <?php echo $journey['media_count']; ?></p>
                            <div class="journey-actions">
                                <a href="journey_details.php?id=<?php echo $journey['id']; ?>" class="btn small">View Details</a>
                                <a href="edit_journey.php?id=<?php echo $journey['id']; ?>" class="btn small">Edit</a>
                                <form method="POST" action="delete_journey.php" style="display:inline;">
                                    <input type="hidden" name="journey_id" value="<?php echo $journey['id']; ?>">
                                    <button type="submit" class="btn small danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        document.getElementById('layoutSelect').addEventListener('change', function() {
            const layout = this.value;
            document.querySelector('.journeys').className = 'journeys ' + layout;
            // Save preference via AJAX or redirect to preference.php
            fetch('preference.php?layout=' + layout);
        });
    </script>
</body>
</html>