<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Only allow donors to access this endpoint
if ($_SESSION['role'] !== 'donor') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bloodbank";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get user details from database
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT full_name, email, phone, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Get donation statistics (if donations table exists)
    $donations_data = ['total_donations' => 0, 'total_volume' => 0, 'last_donation' => null];
    $history = [];
    
    // Check if donations table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'donations'");
    if ($table_check && $table_check->num_rows > 0) {
        $donations_query = "SELECT COUNT(*) as total_donations, COALESCE(SUM(volume), 0) as total_volume, MAX(donation_date) as last_donation 
                            FROM donations WHERE donor_email = ?";
        $donations_stmt = $conn->prepare($donations_query);
        if ($donations_stmt) {
            $donations_stmt->bind_param("s", $email);
            $donations_stmt->execute();
            $donations_result = $donations_stmt->get_result();
            if ($donations_result) {
                $donations_data = $donations_result->fetch_assoc();
            }
            $donations_stmt->close();
        }
        
        // Get donation history
        $history_query = "SELECT * FROM donations WHERE donor_email = ? ORDER BY donation_date DESC LIMIT 10";
        $history_stmt = $conn->prepare($history_query);
        if ($history_stmt) {
            $history_stmt->bind_param("s", $email);
            $history_stmt->execute();
            $history_result = $history_stmt->get_result();
            while ($row = $history_result->fetch_assoc()) {
                $history[] = $row;
            }
            $history_stmt->close();
        }
    }
    
    // Calculate next eligible date (90 days after last donation)
    $next_eligible = null;
    if ($donations_data['last_donation']) {
        $last_donation_date = new DateTime($donations_data['last_donation']);
        $last_donation_date->modify('+90 days');
        $next_eligible = $last_donation_date->format('Y-m-d');
    }
    
    // Generate initials for avatar
    $name_parts = explode(' ', $user['full_name']);
    $initials = '';
    if (count($name_parts) >= 2) {
        $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[count($name_parts) - 1], 0, 1));
    } else {
        $initials = strtoupper(substr($user['full_name'], 0, 2));
    }
    
    $response = [
        'success' => true,
        'user' => [
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'initials' => $initials
        ],
        'stats' => [
            'total_donations' => $donations_data['total_donations'] ?? 0,
            'total_volume' => $donations_data['total_volume'] ?? 0,
            'lives_impacted' => ($donations_data['total_donations'] ?? 0) * 3, // Assuming each donation helps 3 people
            'last_donation' => $donations_data['last_donation'] ?? null,
            'next_eligible' => $next_eligible
        ],
        'donation_history' => $history
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>

