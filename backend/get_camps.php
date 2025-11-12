<?php
include 'config.php';
header('Content-Type: application/json');

$result = mysqli_query($conn, "SELECT * FROM camps ORDER BY date DESC");
$camps = [];

while ($row = mysqli_fetch_assoc($result)) {
    $camps[] = $row;
}

echo json_encode($camps);
?>
