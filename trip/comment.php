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
$comment = trim($data['comment']);

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    exit;
}

// Insert comment
$stmt = $pdo->prepare("INSERT INTO comments (journey_id, user_id, comment) VALUES (?, ?, ?)");
if ($stmt->execute([$journeyId, $userId, $comment])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
}
?>