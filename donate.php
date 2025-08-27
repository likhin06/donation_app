<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default for XAMPP
$password = ""; // Default password is empty
$dbname = "donation_db"; // Replace with your actual database name

header("Content-Type: application/json"); // Set JSON response header

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Validate input data
if (!isset($_POST['fromAddress'], $_POST['toAddress'], $_POST['quantity'], $_POST['phoneNumber'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit;
}

$fromAddress = htmlspecialchars($_POST['fromAddress']);
$toAddress = htmlspecialchars($_POST['toAddress']);
$quantity = intval($_POST['quantity']);
$phoneNumber = $_POST['phoneNumber'];

// Validate phone number (must be exactly 10 digits)
if (!preg_match('/^\d{10}$/', $phoneNumber)) {
    echo json_encode(["status" => "error", "message" => "Phone number must be exactly 10 digits."]);
    exit;
}

// Insert data into database
$sql = "INSERT INTO donations (from_address, to_address, quantity, phone_number) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit;
}

$stmt->bind_param("ssis", $fromAddress, $toAddress, $quantity, $phoneNumber);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Donation recorded successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>
