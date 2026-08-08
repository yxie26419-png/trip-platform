<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    if (empty($errors)) {
        // Insert journey
        $stmt = $pdo->prepare("INSERT INTO journeys (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $description]);
        $journeyId = $pdo->lastInsertId();

        // Handle file uploads
        if (!empty($_FILES['media']['name'][0])) {
            $uploadDir = 'uploads/';
            foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['media']['name'][$key];
                $fileType = $_FILES['media']['type'][$key];
                $fileSize = $_FILES['media']['size'][$key];

                // Determine type
                $allowedTypes = ['image/jpeg', 'image/png', 'video/mp4', 'application/gpx+xml'];
                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = "Invalid file type for $fileName.";
                    continue;
                }

                $type = 'photo';
                if (strpos($fileType, 'video') !== false) $type = 'video';
                elseif (strpos($fileType, 'gpx') !== false) $type = 'gpx';

                $newFileName = uniqid() . '_' . $fileName;
                $filePath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO media (journey_id, type, file_path) VALUES (?, ?, ?)");
                    $stmt->execute([$journeyId, $type, $filePath]);
                } else {
                    $errors[] = "Failed to upload $fileName.";
                }
            }
        }

        if (empty($errors)) {
            $success = true;
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Journey - Travel Journey</title>
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
            <h2>Create New Journey</h2>
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
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="media">Upload Media (Images, Videos, GPX):</label>
                    <input type="file" id="media" name="media[]" multiple accept=".jpg,.jpeg,.png,.mp4,.gpx">
                    <div id="filePreview"></div>
                </div>
                <button type="submit" class="btn">Create Journey</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        // File upload preview
        document.getElementById('media').addEventListener('change', function(e) {
            const preview = document.getElementById('filePreview');
            preview.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                const item = document.createElement('div');
                item.textContent = file.name;
                preview.appendChild(item);
            });
        });
    </script>
</body>
</html>