<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['journey_id']) || !is_numeric($_POST['journey_id'])) {
    header("Location: dashboard.php");
    exit;
}

$journeyId = (int)$_POST['journey_id'];

// Check ownership
$stmt = $pdo->prepare("SELECT id FROM journeys WHERE id = ? AND user_id = ?");
$stmt->execute([$journeyId, $userId]);
if (!$stmt->fetch()) {
    header("Location: dashboard.php");
    exit;
}

// Delete media files
$stmt = $pdo->prepare("SELECT file_path FROM media WHERE journey_id = ?");
$stmt->execute([$journeyId]);
$files = $stmt->fetchAll();
foreach ($files as $file) {
    if (file_exists($file['file_path'])) {
        unlink($file['file_path']);
    }
}

// Delete from database (cascade will handle related records)
$stmt = $pdo->prepare("DELETE FROM journeys WHERE id = ?");
$stmt->execute([$journeyId]);

header("Location: dashboard.php");
exit;
?>