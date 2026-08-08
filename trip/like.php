<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$journeyId = (int)$data['journey_id'];
$action = $data['action'];

if ($action === 'like') {
    $stmt = $pdo->prepare("INSERT IGNORE INTO likes (journey_id, user_id) VALUES (?, ?)");
    $stmt->execute([$journeyId, $userId]);
} elseif ($action === 'unlike') {
    $stmt = $pdo->prepare("DELETE FROM likes WHERE journey_id = ? AND user_id = ?");
    $stmt->execute([$journeyId, $userId]);
}

// Get new count
$stmt = $pdo->prepare("SELECT COUNT(*) as likes FROM likes WHERE journey_id = ?");
$stmt->execute([$journeyId]);
$likes = $stmt->fetch()['likes'];

// Check if liked
$stmt = $pdo->prepare("SELECT id FROM likes WHERE journey_id = ? AND user_id = ?");
$stmt->execute([$journeyId, $userId]);
$liked = $stmt->fetch() ? true : false;

echo json_encode(['success' => true, 'likes' => $likes, 'liked' => $liked]);
?>