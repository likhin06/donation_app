<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

if (!isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
    die(json_encode(["status" => "error", "message" => "Missing required fields."]));
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];

if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    die(json_encode(["status" => "error", "message" => "All fields are required."]));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(["status" => "error", "message" => "Invalid email format."]));
}

if (!preg_match('/^\d{10}$/', $phone)) {
    die(json_encode(["status" => "error", "message" => "Phone number must be 10 digits."]));
}

$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    die(json_encode(["status" => "error", "message" => "Email is already registered."]));
}
$check_stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]));
}

$stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
