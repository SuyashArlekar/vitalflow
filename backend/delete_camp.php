<?php
include 'config.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM camps WHERE camp_id = $id");
echo "Camp deleted successfully!";
?>
