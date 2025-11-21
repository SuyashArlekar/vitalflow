<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit;
}

if ($_SESSION['role'] !== 'donor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only donors can book appointments']);
    exit;
}

$campId = isset($_POST['camp_id']) ? intval($_POST['camp_id']) : 0;
if ($campId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid camp selected']);
    exit;
}

require_once 'config.php';

$email = $_SESSION['email'];

// Fetch user id
$userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$userStmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare user query']);
    exit;
}

$userStmt->bind_param("s", $email);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    $userStmt->close();
    exit;
}

$user = $userResult->fetch_assoc();
$userId = intval($user['id']);
$userStmt->close();

// Ensure camp exists
$campStmt = $conn->prepare("SELECT camp_id FROM camps WHERE camp_id = ?");
if (!$campStmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare camp query']);
    exit;
}

$campStmt->bind_param("i", $campId);
$campStmt->execute();
$campResult = $campStmt->get_result();

if ($campResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Camp not found']);
    $campStmt->close();
    exit;
}
$campStmt->close();

// Prevent duplicate registrations
$checkStmt = $conn->prepare("SELECT reg_id FROM registrations WHERE camp_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $campId, $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'You are already registered for this camp']);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Insert registration
$insertStmt = $conn->prepare("INSERT INTO registrations (camp_id, user_id) VALUES (?, ?)");
if (!$insertStmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare insert statement']);
    exit;
}

$insertStmt->bind_param("ii", $campId, $userId);

if ($insertStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to book appointment']);
}

$insertStmt->close();
$conn->close();
?>

