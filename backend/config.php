<?php
// Database connection settings
$servername = "localhost";   // XAMPP default
$username   = "root";        // default MySQL username in XAMPP
$password   = "";            // usually blank in XAMPP
$database   = "bloodbank"; // your database name (change if different)

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
