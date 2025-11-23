<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in.'
    ]);
    exit;
}

require_once 'db.php';

$registrationId = intval($_POST['registration_id'] ?? 0);
if ($registrationId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid registration.'
    ]);
    exit;
}

// We will delete only if this registration belongs to this username
$username = $_SESSION['username'] ?? '';

$stmt = $conn->prepare("
    DELETE r FROM event_registrations r
    WHERE r.id = ? AND r.user_name = ?
");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error.'
    ]);
    exit;
}

$stmt->bind_param("is", $registrationId, $username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Could not delete this registration.'
    ]);
}

$stmt->close();
