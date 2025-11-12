<?php
include 'config.php';

$title = $_POST['title'];
$desc = $_POST['description'];
$address = $_POST['address'];
$city = $_POST['city'];
$date = $_POST['date'];
$time = $_POST['time'];
$capacity = $_POST['capacity'];

$sql = "INSERT INTO camps (title, description, address, city, date, time, capacity)
        VALUES ('$title', '$desc', '$address', '$city', '$date', '$time', '$capacity')";

if (mysqli_query($conn, $sql)) {
  echo "Camp added successfully!";
} else {
  echo "Error: " . mysqli_error($conn);
}
?>
