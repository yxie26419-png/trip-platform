<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['layout'])) {
    $layout = $_GET['layout'];
    if (in_array($layout, ['grid', 'list'])) {
        setcookie('layout', $layout, time() + (30 * 24 * 60 * 60), '/'); // 30 days
    }
    exit;
}

// For POST request to set preference
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $layout = $_POST['layout'];
    if (in_array($layout, ['grid', 'list'])) {
        setcookie('layout', $layout, time() + (30 * 24 * 60 * 60), '/');
    }
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferences - Travel Journey</title>
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
            <h2>User Preferences</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="layout">Default Layout:</label>
                    <select id="layout" name="layout">
                        <option value="grid" <?php echo (isset($_COOKIE['layout']) && $_COOKIE['layout'] === 'grid') ? 'selected' : ''; ?>>Grid</option>
                        <option value="list" <?php echo (isset($_COOKIE['layout']) && $_COOKIE['layout'] === 'list') ? 'selected' : ''; ?>>List</option>
                    </select>
                </div>
                <button type="submit" class="btn">Save Preferences</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>