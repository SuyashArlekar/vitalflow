<?php
include 'config.php';
header('Content-Type: application/json');

$camps = [];

$sql = "SELECT camp_id, title, description, address, city, date, time, capacity 
        FROM camps 
        ORDER BY date DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . mysqli_error($conn)
    ]);
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $camps[] = $row;
}

echo json_encode($camps);
