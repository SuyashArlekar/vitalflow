<?php
include 'config.php';
session_start();

// If user system uses session after login
if (!isset($_SESSION['user_id'])) {
    echo "Please login to register for a camp!";
    exit;
}

$user_id = $_SESSION['user_id'];
$camp_id = $_POST['camp_id'];

// Prevent duplicate booking
$check = mysqli_query($conn, "SELECT * FROM registrations WHERE camp_id='$camp_id' AND user_id='$user_id'");
if (mysqli_num_rows($check) > 0) {
    echo "You have already booked this camp!";
    exit;
}

// Insert booking
$sql = "INSERT INTO registrations (camp_id, user_id) VALUES ('$camp_id', '$user_id')";
if (mysqli_query($conn, $sql)) {
    echo "Successfully booked your appointment!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
