<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bloodbank";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Handle form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "⚠️ Please fill in all fields.";
        exit;
    }

    // Check user in DB
    $stmt = $conn->prepare("SELECT password, full_name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $full_name, $db_role);
        $stmt->fetch();

        if ($db_role !== $role) {
            echo "❌ Invalid role selected!";
            exit;
        }

        if (password_verify($password, $hashed_password)) {
           session_start();
            $_SESSION['user'] = $full_name;
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $email;

            if ($role === 'donor') {
                echo "Welcome Donor";
            } elseif ($role === 'hospital') {
                echo "Welcome Hospital";
            } else {
                echo "Welcome Staff";
            }
exit;

            exit;
        } else {
            echo "❌ Incorrect password!";
        }
    } else {
        echo "❌ No account found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>
