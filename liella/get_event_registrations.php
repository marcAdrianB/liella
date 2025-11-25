<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized.'
    ]);
    exit;
}

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
if ($eventId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid event ID.'
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT user_name, email
    FROM event_registrations
    WHERE event_id = ?
    ORDER BY id DESC
");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error.'
    ]);
    exit;
}

$stmt->bind_param('i', $eventId);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = [
        'user_name' => $row['user_name'],
        'email'     => $row['email']
    ];
}
$stmt->close();

echo json_encode([
    'success' => true,
    'registrations' => $rows
]);
