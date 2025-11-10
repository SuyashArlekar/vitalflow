<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bloodbank";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($role) || empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        echo "⚠️ All fields are required!";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "❌ Passwords do not match!";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (role, full_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $role, $full_name, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
        echo "✅ Account created successfully!";
    } else {
        echo "❌ Error inserting data: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
