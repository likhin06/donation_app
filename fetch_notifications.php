<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, message, request_id FROM notifications";
$result = $conn->query($sql);

$notifications = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

echo json_encode($notifications);

$conn->close();
?>