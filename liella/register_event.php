<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// require login (adjust when your auth is ready)
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'requireLogin' => true,
        'redirect' => 'login.php',
        'message' => 'Please log in first to register for events.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$event_id  = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$user_name = $_SESSION['username'];
$email     = trim($_POST['email'] ?? '');



// optional: basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// check that event exists
$eventCheck = $conn->prepare("SELECT id FROM events WHERE id = ? LIMIT 1");
$eventCheck->bind_param('i', $event_id);
$eventCheck->execute();
$eventCheck->store_result();
if ($eventCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Event not found.']);
    exit;
}
$eventCheck->close();

// check duplicate registration (same event + email)
$dupCheck = $conn->prepare("SELECT id FROM event_registrations WHERE event_id = ? AND email = ? LIMIT 1");
$dupCheck->bind_param('is', $event_id, $email);
$dupCheck->execute();
$dupCheck->store_result();

if ($dupCheck->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Email already registered to this event.'
    ]);
    $dupCheck->close();
    exit;
}
$dupCheck->close();

// insert new registration
$insert = $conn->prepare("INSERT INTO event_registrations (event_id, user_name, email) VALUES (?, ?, ?)");
$insert->bind_param('iss', $event_id, $user_name, $email);

if ($insert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Success!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to register. Please try again.']);
}

$insert->close();
$conn->close();
