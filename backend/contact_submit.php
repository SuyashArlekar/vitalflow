<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method.'
    ]);
    exit;
}

require_once __DIR__ . '/config.php';

function get_post_value($key) {
    return trim($_POST[$key] ?? '');
}

$fullName = get_post_value('name');
$email    = get_post_value('email');
$phone    = get_post_value('phone');
$subject  = get_post_value('subject');
$message  = get_post_value('message');
$ip       = $_SERVER['REMOTE_ADDR'] ?? null;

$errors = [];

if ($fullName === '') {
    $errors[] = 'Full name is required.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}

if ($subject === '') {
    $errors[] = 'Subject is required.';
}

if ($message === '') {
    $errors[] = 'Message is required.';
}

if ($errors) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'error'   => implode(' ', $errors)
    ]);
    exit;
}

$sql  = 'INSERT INTO contact_messages (full_name, email, phone, subject, message, ip_address) VALUES (?, ?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Unable to prepare database statement.'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ssssss', $fullName, $email, $phone, $subject, $message, $ip);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to store your message. Please try again later.'
    ]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your message has been sent successfully.'
]);

