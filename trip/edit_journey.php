<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$journeyId = (int)$_GET['id'];

// Check ownership
$stmt = $pdo->prepare("SELECT * FROM journeys WHERE id = ? AND user_id = ?");
$stmt->execute([$journeyId, $userId]);
$journey = $stmt->fetch();

if (!$journey) {
    header("Location: dashboard.php");
    exit;
}

// Get existing media
$stmt = $pdo->prepare("SELECT * FROM media WHERE journey_id = ?");
$stmt->execute([$journeyId]);
$media = $stmt->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    if (empty($errors)) {
        // Update journey
        $stmt = $pdo->prepare("UPDATE journeys SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $journeyId]);

        // Handle new uploads
        if (!empty($_FILES['media']['name'][0])) {
            $uploadDir = 'uploads/';
            foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['media']['name'][$key];
                $fileType = $_FILES['media']['type'][$key];

                $allowedTypes = ['image/jpeg', 'image/png', 'video/mp4', 'application/gpx+xml'];
                if (!in_array($fileType, $allowedTypes)) continue;

                $type = 'photo';
                if (strpos($fileType, 'video') !== false) $type = 'video';
                elseif (strpos($fileType, 'gpx') !== false) $type = 'gpx';

                $newFileName = uniqid() . '_' . $fileName;
                $filePath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO media (journey_id, type, file_path) VALUES (?, ?, ?)");
                    $stmt->execute([$journeyId, $type, $filePath]);
                }
            }
        }

        // Handle deletions
        if (isset($_POST['delete_media'])) {
            foreach ($_POST['delete_media'] as $mediaId) {
                $stmt = $pdo->prepare("SELECT file_path FROM media WHERE id = ? AND journey_id = ?");
                $stmt->execute([$mediaId, $journeyId]);
                $file = $stmt->fetch();
                if ($file) {
                    unlink($file['file_path']);
                    $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
                    $stmt->execute([$mediaId]);
                }
            }
        }

        header("Location: journey_details.php?id=$journeyId");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Journey - Travel Journey</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <h1>Travel Journey</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="form-container">
            <h2>Edit Journey</h2>
            <?php if ($errors): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($journey['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($journey['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Existing Media:</label>
                    <div class="existing-media">
                        <?php foreach ($media as $m): ?>
                            <div class="media-item">
                                <input type="checkbox" name="delete_media[]" value="<?php echo $m['id']; ?>">
                                <?php echo htmlspecialchars($m['file_path']); ?> (<?php echo $m['type']; ?>)
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="media">Add New Media:</label>
                    <input type="file" id="media" name="media[]" multiple accept=".jpg,.jpeg,.png,.mp4,.gpx">
                </div>
                <button type="submit" class="btn">Update Journey</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>